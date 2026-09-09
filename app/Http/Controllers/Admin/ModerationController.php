<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CommunityPost;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    public function articles(Request $request)
    {
        $query = Article::with(['author', 'community']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending'); // Default antrean moderasi
        }

        $articles = $query->latest()->paginate(12);

        return view('admin.moderation.articles', compact('articles'));
    }

    public function approveArticle(Article $article)
    {
        $article->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Artikel "'.$article->title.'" berhasil disetujui & dipublikasikan.');
    }

    public function rejectArticle(Article $article)
    {
        $article->update([
            'status' => 'draft',
        ]);

        return back()->with('warning', 'Artikel "'.$article->title.'" ditolak dan dikembalikan ke status draft.');
    }

    public function posts()
    {
        $posts = CommunityPost::with(['author', 'community'])->latest()->paginate(15);

        return view('admin.moderation.posts', compact('posts'));
    }
}
