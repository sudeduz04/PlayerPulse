<x-layouts.app title="AI Analiz Detayı">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route($routePrefix . '.analysis.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Analizlere Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $analysis->player?->first_name }} {{ $analysis->player?->last_name }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $analysis->player?->team?->name }} &middot; {{ $analysis->created_at?->format('d.m.Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs rounded-full bg-purple-500/15 text-purple-400">{{ $analysis->recommendation_type }}</span>
                <form method="POST" action="{{ route($routePrefix . '.analysis.destroy', $analysis->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Bu analizi silmek istediğinize emin misiniz?')"
                            class="bg-red-500/15 text-red-400 hover:bg-red-500/25 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Sil
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">AI Skoru</p>
                <p class="text-3xl font-bold text-purple-400">{{ $analysis->score ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Oyuncu</p>
                <p class="text-white text-lg">{{ $analysis->player?->first_name }} {{ $analysis->player?->last_name }}</p>
                <p class="text-gray-400 text-xs mt-1">#{{ $analysis->player?->jersey_number }} &middot; {{ $analysis->player?->team?->name }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Tarih</p>
                <p class="text-white text-lg">{{ $analysis->created_at?->format('d.m.Y') }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ $analysis->created_at?->format('H:i') }}</p>
            </div>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Analiz</h2>
            <div class="text-gray-300 text-sm whitespace-pre-line leading-relaxed">{{ $analysis->reason }}</div>
        </div>
    </div>
</x-layouts.app>
