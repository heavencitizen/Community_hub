<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">Portal Artikel & Media</h1>
                <p class="text-xs text-slate-500">Jelajahi berita komunitas, gaya hidup, tips hobi, dan liputan event.</p>
            </div>
            @auth
                @if(auth()->user()->isCommunityAdmin() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('articles.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition">
                        + Tulis Artikel
                    </a>
                @endif
            @endauth
        </div>

        <!-- Filter Kategori & Search -->
        <div class="p-3 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
            <!-- Category Tabs -->
            <div class="flex flex-wrap gap-1.5">
                <a href="{{ route('articles.index') }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ !request('category') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    Semua Kategori
                </a>
                @foreach($categories as $key => $label)
                    <a href="{{ route('articles.index', ['category' => $key]) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('category') === $key ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Search -->
            <form action="{{ route('articles.index') }}" method="GET" class="flex gap-2">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul artikel atau topik..." class="flex-grow rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-xs focus:ring-indigo-500 py-2 px-3">
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Articles Grid -->
        @if($articles->isEmpty())
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500">
                <p class="text-sm font-bold text-slate-800">Belum ada artikel di kategori ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($articles as $article)
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-200 hover:shadow-md transition flex flex-col justify-between">
                        <div class="h-36 bg-slate-100 relative overflow-hidden">
                            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                            <div class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-black bg-indigo-600 text-white uppercase tracking-wider shadow">
                                {{ $article->category }}
                            </div>
                        </div>

                        <div class="p-4 flex-grow flex flex-col justify-between space-y-3">
                            <div class="space-y-1.5">
                                <h3 class="font-bold text-slate-800 text-xs sm:text-sm line-clamp-2 hover:text-indigo-600 transition">
                                    <a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a>
                                </h3>
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                                <span class="flex items-center gap-1.5">
                                    <img src="{{ $article->author->avatar_url }}" alt="{{ $article->author->name }}" class="w-4 h-4 rounded-full object-cover">
                                    <strong class="text-slate-700">{{ $article->author->name ?? 'Admin' }}</strong>
                                </span>
                                <span>{{ $article->published_at ? $article->published_at->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
