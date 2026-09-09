<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityPost;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunityPostController extends Controller
{
    public function store(Request $request, Community $community)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'nullable|in:text,photo,video',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,webp,mp4,mov|max:10240',
        ]);

        // Cek apakah user adalah anggota komunitas / pemilik / super admin
        if (! $community->hasMember(auth()->id()) && $community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Anda harus bergabung dengan komunitas ini terlebih dahulu untuk membuat postingan.');
        }

        $mediaUrl = null;
        $type = $request->input('type', 'text');
        if ($request->hasFile('media')) {
            $mediaUrl = $request->file('media')->store('forum_media', 'public');
            $mime = $request->file('media')->getMimeType();
            $type = str_contains($mime, 'video') ? 'video' : 'photo';
        }

        $post = CommunityPost::create([
            'community_id' => $community->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'media_url' => $mediaUrl,
            'type' => $type,
        ]);

        // Notifikasi ke ketua komunitas jika orang lain yang posting
        UserNotification::send(
            $community->user_id,
            auth()->id(),
            'community_new_post',
            'Postingan Baru di Komunitas',
            auth()->user()->name.' membagikan postingan baru di '.$community->name,
            route('communities.show', $community->slug).'#post-'.$post->id,
            'fa-comments',
            'text-indigo-500'
        );

        return back()->with('success', 'Postingan forum berhasil diunggah!');
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'community_id' => 'required|exists:communities,id',
            'content' => 'required|string|max:2000',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,webp,mp4,mov|max:10240',
        ]);

        $community = Community::findOrFail($request->community_id);

        if (! $community->hasMember(auth()->id()) && $community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Anda harus bergabung dengan komunitas ini terlebih dahulu untuk membuat postingan.');
        }

        $mediaUrl = null;
        $type = 'text';
        if ($request->hasFile('media')) {
            $mediaUrl = $request->file('media')->store('forum_media', 'public');
            $mime = $request->file('media')->getMimeType();
            $type = str_contains($mime, 'video') ? 'video' : 'photo';
        }

        $post = CommunityPost::create([
            'community_id' => $community->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'media_url' => $mediaUrl,
            'type' => $type,
        ]);

        // Notifikasi ke ketua komunitas
        UserNotification::send(
            $community->user_id,
            auth()->id(),
            'community_new_post',
            'Postingan Baru di Komunitas',
            auth()->user()->name.' membagikan postingan baru di '.$community->name,
            route('communities.show', $community->slug).'#post-'.$post->id,
            'fa-comments',
            'text-indigo-500'
        );

        return back()->with('success', 'Postingan Anda berhasil dipublikasikan ke '.$community->name);
    }

    public function destroy(CommunityPost $post)
    {
        $user = auth()->user();

        // Otorisasi: Pembuat postingan, Pemilik komunitas, atau Super Admin
        if ($post->user_id !== $user->id && $post->community->user_id !== $user->id && ! $user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus postingan ini.');
        }

        $post->delete();

        return back()->with('success', 'Postingan forum berhasil dihapus.');
    }

    public function like(CommunityPost $post)
    {
        $userId = auth()->id();
        $user = auth()->user();
        $existing = $post->likes()->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $userId]);
            $post->increment('likes_count');
            $liked = true;

            // Kirim notifikasi ke pemilik postingan
            UserNotification::send(
                $post->user_id,
                $userId,
                'post_liked',
                'Postingan Anda Disukai',
                $user->name.' menyukai postingan Anda di '.$post->community->name,
                route('communities.show', $post->community->slug).'#post-'.$post->id,
                'fa-heart',
                'text-rose-500'
            );
        }

        $post->refresh();
        $likedUsers = $post->likes()->with('user')->get()->map(function ($like) {
            return [
                'name' => $like->user->name ?? 'Pengguna',
                'username' => $like->user->username ?? '',
                'avatar' => $like->user->avatar_url ?? null,
            ];
        });

        if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest' || request()->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $post->likes_count,
                'liked_users' => $likedUsers,
            ]);
        }

        return back()->with($liked ? 'success' : 'info', $liked ? 'Anda menyukai postingan ini!' : 'Batal menyukai postingan.');
    }

    public function comment(Request $request, CommunityPost $post)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        // Kirim notifikasi ke pembuat postingan
        UserNotification::send(
            $post->user_id,
            auth()->id(),
            'post_commented',
            'Komentar Baru pada Postingan',
            auth()->user()->name.' berkomentar: "'.Str::limit($request->comment, 40).'"',
            route('communities.show', $post->community->slug).'#post-'.$post->id,
            'fa-comment',
            'text-indigo-500'
        );

        return back()->with('success', 'Komentar Anda berhasil dipublikasikan!');
    }
}
