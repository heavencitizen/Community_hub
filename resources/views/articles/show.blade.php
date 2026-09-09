<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <!-- Article Header -->
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-600 text-white uppercase tracking-wider shadow-sm">
                    {{ $article->category }}
                </span>
                @if($article->status !== 'published')
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 uppercase">
                        Status: {{ $article->status }} (Moderasi)
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">{{ $article->title }}</h1>

            <div class="flex items-center gap-3 text-xs text-slate-400 pt-2 border-t border-slate-200 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <img src="{{ $article->author->avatar_url }}" alt="{{ $article->author->name }}" class="w-5 h-5 rounded-full object-cover">
                    <strong class="text-slate-700">{{ $article->author->name ?? 'Kontributor' }}</strong>
                </div>
                <span>•</span>
                <span>{{ $article->published_at ? $article->published_at->format('d F Y, H:i') : 'Draf' }} WIB</span>
                @if($article->community)
                    <span>•</span>
                    <a href="{{ route('communities.show', $article->community->slug) }}" class="text-indigo-600 hover:underline flex items-center gap-1 font-semibold">
                        <img src="{{ $article->community->logo_url }}" alt="{{ $article->community->name }}" class="w-4 h-4 rounded-md object-cover">
                        <span>{{ $article->community->name }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Featured Image -->
        <div class="rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 shadow-sm max-h-80">
            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="w-full max-h-80 object-cover">
        </div>

        <!-- Article Content -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm leading-relaxed space-y-4 whitespace-pre-line shadow-sm">
            {{ $article->content }}
        </div>

        <!-- Author / Community Box -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ $article->author->avatar_url }}" alt="{{ $article->author->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                <div>
                    <h3 class="font-bold text-slate-800 text-xs">{{ $article->author->name ?? 'Kontributor' }}</h3>
                    <p class="text-[10px] text-slate-500">Penulis & Kontributor di CommunityHub</p>
                </div>
            </div>
            <a href="{{ route('articles.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                &larr; Kembali ke Artikel
            </a>
        </div>

        <!-- Related Articles -->
        @if(!$relatedArticles->isEmpty())
            <div class="pt-4 border-t border-slate-200 space-y-3">
                <h3 class="text-sm font-black text-slate-800">Artikel Terkait Lainnya</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($relatedArticles as $rel)
                        <a href="{{ route('articles.show', $rel->slug) }}" class="p-3 rounded-xl bg-white border border-slate-200 hover:border-indigo-200 shadow-sm transition block space-y-1">
                            <span class="text-[9px] font-extrabold text-indigo-600 uppercase">{{ $rel->category }}</span>
                            <h4 class="font-bold text-slate-800 text-xs line-clamp-2 hover:text-indigo-600">{{ $rel->title }}</h4>
                            <span class="text-[10px] text-slate-400 block">{{ $rel->published_at ? $rel->published_at->format('d M Y') : '' }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
