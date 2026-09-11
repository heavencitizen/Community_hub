<?php

namespace App\Http\Controllers;

use App\Models\CommunityAuction;
use App\Models\CommunityProduct;
use App\Models\Donation;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        
        // 3DS dimatikan agar pengujian kartu kredit Sandbox tidak meminta OTP/PIN
        \Midtrans\Config::$is3ds = false;

        // Mematikan verifikasi SSL khusus untuk testing lokal XAMPP
        // Dan mencegah error undefined array 10023 dari Midtrans
        \Midtrans\Config::$curlOptions = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [] 
        ];
    }

    /**
     * Tampilan checkout pembayaran interaktif
     */
    public function checkout(string $transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)->firstOrFail();

        if ($transaction->user_id && $transaction->user_id !== auth()->id() && (! auth()->check() || ! auth()->user()->isSuperAdmin())) {
            abort(403, 'Pengguna tidak memiliki hak untuk mengakses transaksi ini.');
        }

        $item = null;
        if ($transaction->type === 'product') {
            $item = CommunityProduct::with('community')->find($transaction->reference_id);
        } elseif ($transaction->type === 'auction') {
            $item = CommunityAuction::with('community')->find($transaction->reference_id);
        } elseif ($transaction->type === 'donation') {
            $item = Donation::with('community')->find($transaction->reference_id);
        } elseif ($transaction->type === 'ticket') {
            $item = Ticket::with('event.community')->find($transaction->reference_id);
        }

        // Token tidak di-generate di sini lagi, murni mengembalikan tampilan UI
        return view('payment.checkout', compact('transaction', 'item'));
    }

    /**
     * Endpoint API untuk mengambil token dinamis spesifik 1 metode pembayaran (Direct Payment)
     */
    public function getToken(Request $request, string $transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)->firstOrFail();

        if ($transaction->user_id && $transaction->user_id !== auth()->id() && (! auth()->check() || ! auth()->user()->isSuperAdmin())) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $method = $request->input('payment_method');
        $enabledPayments = [];

        // Konversi pilihan antarmuka web menjadi parameter spesifik Midtrans
        switch ($method) {
            case 'credit_card': $enabledPayments = ['credit_card']; break;
            case 'bca_va': $enabledPayments = ['bca_va']; break;
            case 'mandiri_va': $enabledPayments = ['echannel']; break; // Midtrans menggunakan 'echannel' untuk Mandiri
            case 'bni_va': $enabledPayments = ['bni_va']; break;
            case 'bri_va': $enabledPayments = ['bri_va']; break;
            case 'cimb_va': $enabledPayments = ['cimb_va']; break;
            case 'gopay': $enabledPayments = ['gopay']; break;
            case 'shopeepay': $enabledPayments = ['shopeepay']; break;
            case 'qris':
            case 'ovo':
            case 'dana': 
                $enabledPayments = ['qris']; break; // Menyatukan E-Wallet lain melalui QRIS universal
            default: 
                $enabledPayments = ['other_va']; break; // Bank lain seperti BTN, Mega, Maybank, dll
        }

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => (int) $transaction->total_amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->customer_name ?? 'Pengguna',
                'email' => $transaction->customer_email ?? 'customer@example.com',
            ],
            // Memaksa Midtrans hanya menampilkan satu metode ini
            'enabled_payments' => $enabledPayments 
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Endpoint Webhook dari Midtrans
     */
    public function webhook(Request $request)
    {
        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }

        $transactionStatus = $notif->transaction_status;
        $paymentType = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        $transaction = Transaction::where('transaction_code', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'challenge') {
                $transaction->update(['payment_status' => 'challenge']);
            } else {
                // Pastikan tidak mengeksekusi dua kali jika sudah lunas
                if ($transaction->payment_status !== 'completed') {
                    $transaction->update([
                        'payment_method' => $paymentType,
                        'payment_status' => 'completed',
                        'payment_code' => '-', // Midtrans handle ini
                        'payment_details' => [
                            'paid_at' => now()->toDateTimeString(),
                            'method_label' => strtoupper(str_replace('_', ' ', $paymentType)),
                        ],
                    ]);

                    $this->handleSuccessfulPayment($transaction);
                }
            }
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $transaction->update(['payment_status' => 'failed']);
        } else if ($transactionStatus == 'pending') {
            $transaction->update(['payment_status' => 'pending']);
        }

        return response()->json(['message' => 'Notification processed successfully']);
    }

    /**
     * Logika Bisnis: Donasi, Produk, Lelang, Tiket
     */
    private function handleSuccessfulPayment($transaction)
    {
        if ($transaction->type === 'donation') {
            $donation = Donation::find($transaction->reference_id);
            if ($donation) {
                $donation->increment('collected_amount', $transaction->amount);
                UserNotification::send(
                    $donation->user_id,
                    $transaction->user_id,
                    'donation_received',
                    'Donasi Amal Diterima',
                    ($transaction->user ? $transaction->user->name : 'Donatur').' telah berdonasi sebesar Rp'.number_format($transaction->amount, 0, ',', '.').' untuk program "'.$donation->title.'"',
                    route('donations.show', $donation->slug),
                    'fa-hand-holding-heart',
                    'text-teal-500'
                );
            }
        } elseif ($transaction->type === 'product') {
            $product = CommunityProduct::find($transaction->reference_id);
            if ($product && $product->stock > 0) {
                $product->decrement('stock', 1);
                if ($product->stock === 0) {
                    $product->update(['status' => 'sold_out']);
                }
                UserNotification::send(
                    $product->user_id,
                    $transaction->user_id,
                    'product_sold',
                    'Produk Terjual',
                    ($transaction->user ? $transaction->user->name : 'Pembeli').' membeli '.$product->name,
                    route('communities.show', $product->community->slug).'?tab=marketplace',
                    'fa-store',
                    'text-emerald-500'
                );
            }
        } elseif ($transaction->type === 'auction') {
            $auction = CommunityAuction::find($transaction->reference_id);
            if ($auction) {
                $auction->update(['status' => 'completed']);
                UserNotification::send(
                    $auction->user_id,
                    $transaction->user_id,
                    'auction_paid',
                    'Pembayaran Pemenang Lelang Lunas',
                    'Pembayaran lelang untuk "'.$auction->title.'" telah selesai.',
                    route('communities.show', $auction->community->slug).'?tab=auctions',
                    'fa-gavel',
                    'text-purple-500'
                );
            }
        } elseif ($transaction->type === 'ticket') {
            $ticket = Ticket::with(['event.community', 'user'])->find($transaction->reference_id);
            if ($ticket) {
                $ticket->update(['status' => 'approved']);
                if ($ticket->user_id) {
                    UserNotification::send(
                        $ticket->user_id,
                        null,
                        'ticket_approved',
                        'E-Ticket QR Code Terbit!',
                        'Pembayaran tiket event "'.$ticket->event->title.'" berhasil. E-Ticket QR Code pengguna telah aktif.',
                        route('tickets.show', $ticket->id),
                        'fa-qrcode',
                        'text-indigo-500'
                    );
                }
                UserNotification::send(
                    $ticket->event->community->user_id,
                    $ticket->user_id,
                    'ticket_purchased',
                    'Pesanan Tiket Event Baru',
                    ($ticket->user ? $ticket->user->name : 'Peserta').' telah memesan tiket untuk "'.$ticket->event->title.'"',
                    route('admin.tickets.index'),
                    'fa-ticket',
                    'text-purple-500'
                );
            }
        }
    }

    /**
     * Halaman sukses & struk pembayaran
     */
    public function success(string $transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)->firstOrFail();

        // --- TRIK AKAL-AKALAN TANPA NGROK (KHUSUS LOCALHOST) ---
        // Memaksa pelunasan langsung dieksekusi saat diarahkan ke halaman sukses
        if ($transaction->payment_status !== 'completed') {
            $transaction->update([
                'payment_status' => 'completed',
                'payment_code' => '-', 
                'payment_details' => [
                    'paid_at' => now()->toDateTimeString(),
                    'method_label' => 'SIMULASI LOKAL',
                ],
            ]);

            // Mengeksekusi penambahan jumlah donasi dan memunculkan nama donatur
            $this->handleSuccessfulPayment($transaction);
        }
        // --------------------------------------------------------

        $item = null;
        if ($transaction->type === 'product') {
            $item = CommunityProduct::with('community')->find($transaction->reference_id);
        } elseif ($transaction->type === 'auction') {
            $item = CommunityAuction::with('community')->find($transaction->reference_id);
        } elseif ($transaction->type === 'donation') {
            $item = Donation::with('community')->find($transaction->reference_id);
        } elseif ($transaction->type === 'ticket') {
            $item = Ticket::with('event.community')->find($transaction->reference_id);
        }

        return view('payment.success', compact('transaction', 'item'));
    }

    /**
     * Mengirim notifikasi jika pengguna menutup popup sebelum membayar
     */
    public function pendingNotification(string $transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)->firstOrFail();

        if ($transaction->user_id && $transaction->payment_status !== 'completed') {
            // Menggunakan Cache agar notifikasi tidak spam jika popup ditutup berkali-kali (cooldown 5 menit)
            $cacheKey = 'notif_pending_' . $transaction->transaction_code;
            
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                UserNotification::send(
                    $transaction->user_id,
                    null,
                    'payment_pending',
                    'Menunggu Pembayaran',
                    'Selesaikan pembayaran untuk pesanan #' . $transaction->transaction_code . ' sebelum batas waktu habis.',
                    route('payment.checkout', $transaction->transaction_code),
                    'fa-clock',
                    'text-amber-500'
                );
                
                // Kunci cache selama 5 menit agar tidak berlipat ganda
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addMinutes(5));
            }
        }

        return response()->json(['success' => true]);
    }
}