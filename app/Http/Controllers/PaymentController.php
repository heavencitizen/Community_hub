<?php

namespace App\Http\Controllers;

use App\Models\CommunityAuction;
use App\Models\CommunityProduct;
use App\Models\Donation;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Tampilan checkout pembayaran interaktif
     */
    public function checkout(string $transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)->firstOrFail();

        // Otorisasi kepemilikan transaksi
        if ($transaction->user_id && $transaction->user_id !== auth()->id() && (! auth()->check() || ! auth()->user()->isSuperAdmin())) {
            abort(403, 'Anda tidak memiliki hak untuk mengakses transaksi ini.');
        }

        // Ambil data referensi
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

        return view('payment.checkout', compact('transaction', 'item'));
    }

    /**
     * Simulasi proses pembayaran dan update status
     */
    public function process(Request $request, string $transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)->firstOrFail();

        // Otorisasi kepemilikan transaksi
        if ($transaction->user_id && $transaction->user_id !== auth()->id() && (! auth()->check() || ! auth()->user()->isSuperAdmin())) {
            abort(403, 'Anda tidak memiliki hak untuk memproses transaksi ini.');
        }

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $paymentMethod = $request->payment_method;
        $paymentCode = null;

        if (str_contains($paymentMethod, '_va')) {
            $bankPrefix = match ($paymentMethod) {
                'bca_va' => '88000',
                'mandiri_va' => '89000',
                'bni_va' => '87000',
                'bri_va' => '86000',
                'btn_va' => '85000',
                'cimb_va' => '84000',
                'mega_va' => '83000',
                'maybank_va' => '82000',
                'seabank_va' => '81000',
                'allobank_va' => '80000',
                default => '80000',
            };
            $paymentCode = $bankPrefix.rand(10000000, 99999999);
        } elseif ($paymentMethod === 'qris') {
            $paymentCode = '00020101021226590014ID.LINKAJA.WWW011893600014'.rand(100000, 999999);
        } else {
            $paymentCode = 'PAY-'.strtoupper(bin2hex(random_bytes(4)));
        }

        // Tandai transaksi lunas
        $transaction->update([
            'payment_method' => $paymentMethod,
            'payment_status' => 'completed',
            'payment_code' => $paymentCode,
            'payment_details' => [
                'paid_at' => now()->toDateTimeString(),
                'method_label' => strtoupper(str_replace('_', ' ', $paymentMethod)),
                'ip_address' => $request->ip(),
            ],
        ]);

        // Update target data sesuai tipe transaksi & kirim notifikasi
        if ($transaction->type === 'donation') {
            $donation = Donation::find($transaction->reference_id);
            if ($donation) {
                $donation->increment('collected_amount', $transaction->amount);

                // Notifikasi ke inisiator donasi
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

                // Notifikasi ke penjual
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

                // Notifikasi ke pelelang
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

                // Notifikasi ke pembeli tiket
                if ($ticket->user_id) {
                    UserNotification::send(
                        $ticket->user_id,
                        null,
                        'ticket_approved',
                        'E-Ticket QR Code Terbit!',
                        'Pembayaran tiket event "'.$ticket->event->title.'" berhasil. E-Ticket QR Code Anda telah aktif.',
                        route('tickets.show', $ticket->id),
                        'fa-qrcode',
                        'text-indigo-500'
                    );
                }

                // Notifikasi ke ketua komunitas / penyelenggara
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

        return redirect()->route('payment.success', $transaction->transaction_code)
            ->with('success', 'Pembayaran berhasil dikonfirmasi!');
    }

    /**
     * Halaman sukses & struk pembayaran
     */
    public function success(string $transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)->firstOrFail();

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
}
