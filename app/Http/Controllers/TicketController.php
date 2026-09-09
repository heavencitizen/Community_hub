<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    /**
     * Pembelian / Pendaftaran Tiket Event Terstandarisasi
     */
    public function store(Request $request, Event $event)
    {
        return DB::transaction(function () use ($event) {
            $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->firstOrFail();

            if ($lockedEvent->remainingQuota() <= 0) {
                return back()->with('error', 'Mohon maaf, kuota tiket untuk event ini sudah habis.');
            }

            $userId = auth()->id();
            $feeCalculation = $lockedEvent->calculateFee($userId);
            $ticketCode = 'TKT-'.strtoupper(Str::random(8));

            // 1. Jika Event Gratis: Langsung terbitkan tiket approved (RSVP)
            if ($lockedEvent->price == 0) {
                $ticket = Ticket::create([
                    'user_id' => $userId,
                    'event_id' => $lockedEvent->id,
                    'ticket_code' => $ticketCode,
                    'total_price' => 0,
                    'payment_receipt' => null,
                    'status' => 'approved',
                    'is_scanned' => false,
                ]);

                return redirect()->route('tickets.show', $ticket->id)->with('success', 'Pendaftaran tiket gratis (RSVP) berhasil!');
            }

            // 2. Jika Event Berbayar: Buat tiket pending dan arahkan ke Automated Payment Gateway
            $ticket = Ticket::create([
                'user_id' => $userId,
                'event_id' => $lockedEvent->id,
                'ticket_code' => $ticketCode,
                'total_price' => $feeCalculation['total_amount'],
                'payment_receipt' => null,
                'status' => 'pending',
                'is_scanned' => false,
            ]);

            $transaction = Transaction::create([
                'transaction_code' => 'TRX-'.$ticketCode,
                'user_id' => $userId,
                'type' => 'ticket',
                'reference_id' => $ticket->id,
                'amount' => $lockedEvent->price,
                'platform_fee_percent' => $feeCalculation['fee_percent'],
                'platform_fee_amount' => $feeCalculation['fee_amount'],
                'total_amount' => $feeCalculation['total_amount'],
                'payment_method' => 'qris',
                'payment_status' => 'pending',
                'notes' => 'Pembelian tiket event '.$lockedEvent->title,
            ]);

            return redirect()->route('payment.checkout', $transaction->transaction_code);
        });
    }

    public function show(Ticket $ticket)
    {
        $user = auth()->user();
        if ($ticket->user_id !== $user->id && ! $user->isSuperAdmin() && $ticket->event->community->user_id !== $user->id) {
            abort(403, 'Akses tiket tidak diizinkan.');
        }

        $ticket->load(['user', 'event.community']);

        $qrPayload = json_encode([
            'ticket_code' => $ticket->ticket_code,
            'user' => $ticket->user->name,
            'event' => $ticket->event->title,
            'status' => $ticket->status,
        ]);

        $qrCode = QrCode::size(220)->color(30, 41, 59)->generate($qrPayload);

        return view('tickets.show', compact('ticket', 'qrCode'));
    }

    public function scan(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && $ticket->event->community->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk memindai tiket ini.');
        }

        if ($ticket->status !== 'approved') {
            return back()->with('error', 'Tiket ini belum lunas atau dibatalkan.');
        }

        if ($ticket->is_scanned) {
            return back()->with('warning', 'Tiket sudah pernah dipindai sebelumnya pada '.$ticket->scanned_at->format('d M Y, H:i'));
        }

        $ticket->update([
            'is_scanned' => true,
            'scanned_at' => now(),
        ]);

        return back()->with('success', 'Tiket valid! Pengunjung berhasil diverifikasi masuk.');
    }
}
