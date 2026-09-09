<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\Event;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = Community::withCount('members')->where('status', 'active');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $communities = $query->latest()->paginate(12)->withQueryString();

        $upcomingEvents = Event::with('community')->where('event_date', '>=', now())->orderBy('event_date', 'asc')->take(4)->get();
        $popularCommunities = Community::withCount('members')->where('status', 'active')->orderBy('members_count', 'desc')->take(5)->get();

        return view('communities.index', compact('communities', 'upcomingEvents', 'popularCommunities'));
    }

    public function create()
    {
        return view('communities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'rules' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $logoPath = $request->hasFile('logo') ? $request->file('logo')->store('communities/logos', 'public') : null;
        $bannerPath = $request->hasFile('banner') ? $request->file('banner')->store('communities/banners', 'public') : null;

        $slug = Str::slug($request->name).'-'.Str::random(4);

        $defaultRules = $request->rules ?? "1. Saling menghormati antar anggota komunitas.\n2. Dilarang menyebarkan ujaran kebencian, SARA, atau konten tidak pantas.\n3. Transaksi jual beli dan lelang wajib jujur dan transparan (anti penipuan).\n4. Pelanggaran aturan akan dikenakan sanksi penonaktifan/pembekuan keanggotaan oleh ketua komunitas.";

        $community = Community::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'slug' => $slug,
            'category' => $request->category,
            'description' => $request->description,
            'rules' => $defaultRules,
            'logo' => $logoPath,
            'banner' => $bannerPath,
            'status' => 'active',
        ]);

        // Otomatis owner menjadi member pertama dengan status captain
        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => auth()->id(),
            'role' => 'captain',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        // Upgrade role user menjadi community_admin jika masih 'user' biasa
        if (auth()->user()->isUser()) {
            auth()->user()->update(['role' => 'community_admin']);
        }

        return redirect()->route('communities.show', $community->slug)->with('success', 'Komunitas baru berhasil dibuat!');
    }

    public function show($slug)
    {
        $community = Community::with([
            'owner',
            'events' => function ($q) {
                $q->orderBy('event_date', 'asc')->take(4);
            },
            'products' => function ($q) {
                $q->latest();
            },
            'auctions' => function ($q) {
                $q->with(['bids.user', 'winner'])->latest();
            },
            'members' => function ($q) {
                $q->with('user')->orderBy('created_at', 'asc');
            },
        ])->withCount('members')->where('slug', $slug)->firstOrFail();

        $posts = $community->posts()->with(['author', 'comments.user', 'likes.user'])->latest()->paginate(10);
        $isMember = auth()->check() ? $community->hasActiveMember(auth()->id()) : false;
        $myMembership = auth()->check() ? $community->getMembership(auth()->id()) : null;

        $popularCommunities = Community::where('id', '!=', $community->id)->withCount('members')->where('status', 'active')->orderBy('members_count', 'desc')->take(4)->get();

        return view('communities.show', compact('community', 'posts', 'isMember', 'myMembership', 'popularCommunities'));
    }

    public function join(Community $community)
    {
        $userId = auth()->id();

        $existing = $community->getMembership($userId);
        if ($existing) {
            if ($existing->status === 'banned') {
                return back()->with('error', 'Akun Anda telah di-banned dari komunitas ini karena pelanggaran aturan.');
            }
            if ($existing->status === 'suspended') {
                return back()->with('error', 'Status keanggotaan Anda sedang dinonaktifkan sementara.');
            }

            return back()->with('info', 'Anda sudah menjadi anggota komunitas ini.');
        }

        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => $userId,
            'role' => 'member',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        // Kirim notifikasi ke Ketua Komunitas
        UserNotification::send(
            $community->user_id,
            $userId,
            'member_joined',
            'Anggota Baru Bergabung',
            auth()->user()->name.' telah resmi bergabung dengan komunitas '.$community->name,
            route('communities.show', $community->slug).'?tab=members',
            'fa-user-plus',
            'text-emerald-500'
        );

        return back()->with('success', 'Selamat! Anda telah resmi bergabung dengan komunitas '.$community->name);
    }

    public function leave(Community $community)
    {
        $userId = auth()->id();

        if ($community->user_id === $userId) {
            return back()->with('error', 'Ketua / Pemilik komunitas tidak dapat meninggalkan komunitas sendiri.');
        }

        CommunityMember::where('community_id', $community->id)
            ->where('user_id', $userId)
            ->delete();

        return back()->with('success', 'Anda telah keluar dari komunitas '.$community->name);
    }

    /**
     * Moderasi / Sanksi Anggota oleh Ketua Komunitas
     */
    public function updateMemberStatus(Request $request, Community $community, CommunityMember $member)
    {
        // Pastikan hanya ketua komunitas atau super admin yang bisa memberi sanksi
        if ($community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya ketua komunitas yang memiliki wewenang memberikan sanksi.');
        }

        if ($member->user_id === $community->user_id) {
            return back()->with('error', 'Tidak dapat mengubah status pemilik komunitas.');
        }

        $request->validate([
            'status' => 'required|in:active,suspended,banned',
            'suspend_reason' => 'nullable|string|max:255',
        ]);

        $member->update([
            'status' => $request->status,
            'suspend_reason' => $request->suspend_reason,
        ]);

        $statusText = match ($request->status) {
            'suspended' => 'dinonaktifkan sementara',
            'banned' => 'diblokir permanen (banned)',
            default => 'diaktifkan kembali',
        };

        return back()->with('success', "Status anggota {$member->user->name} berhasil diubah menjadi {$statusText}.");
    }

    /**
     * Update aturan komunitas
     */
    public function updateRules(Request $request, Community $community)
    {
        if ($community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya ketua komunitas yang dapat memperbarui aturan.');
        }

        $request->validate([
            'rules' => 'required|string|max:4000',
        ]);

        $community->update(['rules' => $request->rules]);

        return back()->with('success', 'Aturan komunitas berhasil diperbarui!');
    }

    /**
     * Update profil, banner, nama grup, kategori, deskripsi, dan aturan oleh Ketua Komunitas
     */
    public function update(Request $request, Community $community)
    {
        if ($community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya ketua komunitas yang memiliki wewenang mengedit informasi komunitas ini.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'description' => 'required|string|max:2000',
            'rules' => 'nullable|string|max:4000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $updateData = [
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
        ];

        if ($request->filled('rules')) {
            $updateData['rules'] = $request->rules;
        }

        // Perbarui slug jika nama berubah
        if ($community->name !== $request->name) {
            $updateData['slug'] = Str::slug($request->name).'-'.Str::random(4);
        }

        if ($request->hasFile('logo')) {
            $updateData['logo'] = $request->file('logo')->store('communities/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $updateData['banner'] = $request->file('banner')->store('communities/banners', 'public');
        }

        $community->update($updateData);

        // Kirim in-app notifikasi konfirmasi ke ketua
        UserNotification::send(
            $community->user_id,
            null,
            'community_updated',
            'Profil Komunitas Diperbarui',
            'Perubahan profil, banner sampul, dan deskripsi untuk '.$community->name.' berhasil disimpan.',
            route('communities.show', $community->slug),
            'fa-check-circle',
            'text-emerald-500'
        );

        return redirect()->route('communities.show', $community->slug)
            ->with('success', 'Profil komunitas, foto, banner, dan deskripsi berhasil diperbarui!');
    }
}
