<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Donation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    /**
     * Tampilkan katalog program donasi amal
     */
    public function index(Request $request)
    {
        $query = Donation::with(['community', 'user'])->where('status', 'active');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        $donations = $query->latest()->paginate(9);
        $totalCollected = Donation::sum('collected_amount');

        return view('donations.index', compact('donations', 'totalCollected'));
    }

    /**
     * Detail program donasi
     */
    public function show(string $slug)
    {
        $donation = Donation::with(['community', 'user', 'transactions' => function ($q) {
            $q->latest()->take(10);
        }])->where('slug', $slug)->firstOrFail();

        return view('donations.show', compact('donation'));
    }

    /**
     * Form buat program donasi baru (Super Admin atau Ketua Komunitas)
     */
    public function create()
    {
        $user = auth()->user();
        $communities = $user->isSuperAdmin()
            ? Community::all()
            : Community::where('user_id', $user->id)->get();

        return view('donations.create', compact('communities'));
    }

    /**
     * Simpan program donasi
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:100000',
            'description' => 'required|string',
            'community_id' => 'nullable|exists:communities,id',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('donations', 'public');
        }

        $slug = Str::slug($request->title).'-'.Str::random(5);

        Donation::create([
            'user_id' => auth()->id(),
            'community_id' => $request->community_id,
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'banner' => $bannerPath,
            'target_amount' => $request->target_amount,
            'collected_amount' => 0,
            'status' => 'active',
        ]);

        return redirect()->route('donations.index')->with('success', 'Program donasi amal berhasil dipublikasikan!');
    }

    /**
     * Donasi ke program (Bebas Potongan Fee - 0% Fee Platform)
     * Dapat dilakukan oleh pengguna umum (guest) maupun pengguna terdaftar.
     */
    public function donate(Request $request, Donation $donation)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5000',
            'guest_name' => auth()->check() ? 'nullable' : 'required|string|max:255',
            'guest_email' => auth()->check() ? 'nullable' : 'required|email|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $transactionCode = 'DON-'.strtoupper(Str::random(10));

        // Donasi Amal 0% Fee Platform (Bebas potongan apapun)
        $transaction = Transaction::create([
            'transaction_code' => $transactionCode,
            'user_id' => auth()->id(),
            'type' => 'donation',
            'reference_id' => $donation->id,
            'amount' => $request->amount,
            'platform_fee_percent' => 0.0,
            'platform_fee_amount' => 0.0,
            'total_amount' => $request->amount,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
            'guest_name' => auth()->check() ? auth()->user()->name : $request->guest_name,
            'guest_email' => auth()->check() ? auth()->user()->email : $request->guest_email,
            'notes' => $request->notes ?? ('Donasi untuk '.$donation->title),
        ]);

        return redirect()->route('payment.checkout', $transaction->transaction_code);
    }
}
