<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['author', 'community'])->published();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('content', 'like', '%'.$request->search.'%');
        }

        $articles = $query->orderBy('published_at', 'desc')->paginate(9);
        $categories = ['lifestyle' => 'Lifestyle', 'trend' => 'Trend', 'news' => 'Berita', 'donation' => 'Penggalangan Dana'];

        return view('articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $user = auth()->user();
        $communities = $user->isSuperAdmin()
            ? Community::where('status', 'active')->get()
            : Community::where('user_id', $user->id)->where('status', 'active')->get();

        return view('articles.create', compact('communities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:lifestyle,trend,news,donation',
            'content' => 'required|string',
            'community_id' => 'nullable|exists:communities,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'action' => 'required|in:draft,submit',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        $status = ($request->action === 'submit') ? 'pending' : 'draft';
        $publishedAt = null;

        // Super Admin bisa langsung auto-publish jika submit
        if (auth()->user()->isSuperAdmin() && $request->action === 'submit') {
            $status = 'published';
            $publishedAt = now();
        }

        $slug = Str::slug($request->title).'-'.Str::random(5);

        Article::create([
            'user_id' => auth()->id(),
            'community_id' => $request->community_id,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'image' => $imagePath,
            'category' => $request->category,
            'status' => $status,
            'published_at' => $publishedAt,
        ]);

        $msg = $status === 'published'
            ? 'Artikel berhasil diterbitkan!'
            : ($status === 'pending' ? 'Artikel diajukan untuk moderasi Super Admin.' : 'Draf artikel berhasil disimpan.');

        return redirect()->route('articles.index')->with('success', $msg);
    }

    public function show($slug)
    {
        $article = Article::with(['author', 'community'])->where('slug', $slug)->firstOrFail();

        // Jika belum published, hanya bisa dilihat author atau super admin
        if ($article->status !== 'published') {
            if (! auth()->check() || (auth()->id() !== $article->user_id && ! auth()->user()->isSuperAdmin())) {
                abort(404);
            }
        }

        $relatedArticles = Article::with(['author', 'community'])
            ->published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->take(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}
