<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Toggle follow/unfollow akun pengguna lain (Social Networking)
     */
    public function toggle(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $authUser = auth()->user();

        if ($authUser->id === $user->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Anda tidak dapat mengikuti diri sendiri.'], 422);
            }

            return back()->with('error', 'Anda tidak dapat mengikuti diri sendiri.');
        }

        $isFollowing = $authUser->toggleFollow($user);

        // Jika baru mengikuti, kirim notifikasi ke pengguna tujuan
        if ($isFollowing) {
            UserNotification::send(
                $user->id,
                $authUser->id,
                'new_follower',
                'Teman / Pengikut Baru',
                $authUser->name.' mulai terhubung dan mengikuti Anda.',
                route('users.show', $authUser->username),
                'fa-user-plus',
                'text-indigo-500'
            );
        }

        $message = $isFollowing
            ? 'Berhasil terhubung dan mengikuti '.$user->name
            : 'Berhenti mengikuti '.$user->name;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'following' => $isFollowing,
                'followers_count' => $user->followers()->count(),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
