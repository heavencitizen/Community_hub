<?php

namespace App\Http\Controllers;

use App\Models\CommunityMember;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil akun sendiri (untuk user yang sedang login)
     */
    public function myProfile()
    {
        return $this->renderProfile(auth()->user());
    }

    /**
     * Tampilkan profil publik user berdasarkan username (/users/{username})
     */
    public function show(User $user)
    {
        return $this->renderProfile($user);
    }

    /**
     * Helper terpusat untuk me-render halaman profil (sendiri atau orang lain)
     */
    protected function renderProfile(User $user)
    {
        $authUser = auth()->user();
        $isOwn = $authUser && $authUser->id === $user->id;
        $isFollowing = ($authUser && ! $isOwn) ? $authUser->isFollowing($user) : false;

        // Eager load stats
        $postsCount = $user->communityPosts()->count();
        $communitiesCount = $user->communityMemberships()->where('status', 'active')->count();
        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        // 1. Tab Postingan
        $posts = $user->communityPosts()
            ->with(['community', 'author', 'comments.user', 'likes.user'])
            ->latest()
            ->paginate(10);

        // 2. Tab Komunitas
        $memberships = CommunityMember::with('community.owner')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('joined_at')
            ->get();

        // 3. Tab Media (Postingan dengan Foto/Video)
        $mediaPosts = $user->communityPosts()
            ->whereNotNull('media_url')
            ->latest()
            ->get();

        // 4. Tab Pengikut (Followers)
        $followers = $user->followers()
            ->withCount(['communityPosts', 'followers'])
            ->latest('user_follows.created_at')
            ->get();

        // 5. Tab Mengikuti (Following)
        $following = $user->following()
            ->withCount(['communityPosts', 'followers'])
            ->latest('user_follows.created_at')
            ->get();

        // 6. Data khusus akun sendiri
        $tickets = $isOwn ? $user->tickets()->with('event.community')->latest()->get() : collect();
        $transactions = $isOwn ? Transaction::where('user_id', $user->id)->latest()->take(5)->get() : collect();

        return view('profile.show', compact(
            'user',
            'isOwn',
            'isFollowing',
            'postsCount',
            'communitiesCount',
            'followersCount',
            'followingCount',
            'posts',
            'memberships',
            'mediaPosts',
            'followers',
            'following',
            'tickets',
            'transactions'
        ));
    }

    public function tickets()
    {
        $tickets = auth()->user()->tickets()->with('event.community')->latest()->paginate(10);

        return view('profile.tickets', compact('tickets'));
    }

    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'min:3',
                'max:40',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => 'required|email|unique:users,email,'.$user->id,
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $user->fill($request->only('name', 'username', 'email', 'bio'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('banner')) {
            $user->banner = $request->file('banner')->store('banners', 'public');
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil dan informasi akun berhasil diperbarui!');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        auth()->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
