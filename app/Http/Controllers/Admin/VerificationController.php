<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Ticket::with(['user', 'event.community']);

        // Jika Community Admin / EO, hanya tampilkan tiket event komunitas miliknya
        if (! $user->isSuperAdmin()) {
            $query->whereHas('event.community', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(15);
        $totalCommission = $user->isSuperAdmin() ? Ticket::where('status', 'approved')->sum('total_price') : 0;
        $platformFeeTotal = $user->isSuperAdmin() ? Ticket::where('status', 'approved')->count() * 3000 : 0;

        return view('admin.verification.index', compact('tickets', 'totalCommission', 'platformFeeTotal'));
    }

    public function approve(Ticket $ticket)
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && $ticket->event->community->user_id !== $user->id) {
            abort(403, 'Akses verifikasi ditolak.');
        }

        $ticket->update(['status' => 'approved']);

        return back()->with('success', 'Tiket '.$ticket->ticket_code.' berhasil disetujui (Approved). E-Ticket & QR Code kini aktif.');
    }

    public function reject(Ticket $ticket)
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && $ticket->event->community->user_id !== $user->id) {
            abort(403, 'Akses verifikasi ditolak.');
        }

        $ticket->update(['status' => 'rejected']);

        return back()->with('warning', 'Tiket '.$ticket->ticket_code.' telah ditolak.');
    }
}
