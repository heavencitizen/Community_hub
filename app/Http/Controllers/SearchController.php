<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Community;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $users = collect();
        $communities = collect();

        if ($query) {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->limit(12)->get();

            $communities = Community::where('name', 'like', "%{$query}%")
                ->limit(12)->get();
        }

        return view('search.index', compact('users', 'communities', 'query'));
    }

    public function liveSearch(Request $request)
    {
        try {
            $query = $request->input('q');
            
            if (!$query || strlen($query) < 2) {
                return response()->json(['users' => [], 'communities' => []]);
            }

            // Cari User
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->limit(4)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'avatar_url' => $user->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($user->name),
                    ];
                });

            // Cari Komunitas
            $communities = Community::where('name', 'like', "%{$query}%")
                ->limit(4)
                ->get()
                ->map(function ($com) {
                    return [
                        'id' => $com->id,
                        'name' => $com->name,
                        'slug' => $com->slug,
                        'category' => $com->category ?? 'Komunitas',
                        'logo_url' => $com->logo_url ?: 'https://ui-avatars.com/api/?name='.urlencode($com->name),
                    ];
                });

            return response()->json([
                'users' => $users,
                'communities' => $communities
            ]);

        } catch (\Exception $e) {
            // Mengembalikan pesan error spesifik jika terjadi kendala DB
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}