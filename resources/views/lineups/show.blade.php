<x-layouts.app title="Kadro Detayı">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route($routePrefix . '.lineups.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Kadrolara Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $lineup->match?->team?->name }} vs {{ $lineup->match?->opponent_team }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $lineup->match?->match_date?->format('d.m.Y') }} &middot; Diziliş: <span class="text-accent font-semibold">{{ $lineup->formation }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                @if($lineup->is_ai_generated)
                    <span class="px-3 py-1 text-xs rounded-full bg-purple-500/15 text-purple-400">AI Üretti</span>
                @else
                    <span class="px-3 py-1 text-xs rounded-full bg-accent/15 text-accent">Manuel</span>
                @endif
                <form method="POST" action="{{ route($routePrefix . '.lineups.destroy', $lineup->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Bu kadroyu silmek istediğinize emin misiniz?')"
                            class="bg-red-500/15 text-red-400 hover:bg-red-500/25 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Sil
                    </button>
                </form>
            </div>
        </div>

        @if($lineup->note)
            <div class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Not</p>
                <p class="text-gray-300 text-sm whitespace-pre-line">{{ $lineup->note }}</p>
            </div>
        @endif

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border">
                <h2 class="text-lg font-semibold text-white">İlk 11 ({{ $lineup->players->count() }})</h2>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Oyuncu</th>
                        <th class="px-4 py-3">Pozisyon</th>
                        <th class="px-4 py-3">Forma</th>
                        @if($lineup->is_ai_generated)
                            <th class="px-4 py-3 text-center">AI Skoru</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($lineup->players as $idx => $row)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-4 py-3 text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 text-white font-medium">{{ $row->player?->first_name }} {{ $row->player?->last_name }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $row->position?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">#{{ $row->player?->jersey_number ?? '-' }}</td>
                            @if($lineup->is_ai_generated)
                                <td class="px-4 py-3 text-center text-purple-400 font-semibold">{{ $row->recommendation_score ?? '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 bg-surface-700 border border-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Kayıt Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Maç</p>
                    <p class="text-white">{{ $lineup->match?->team?->name }} vs {{ $lineup->match?->opponent_team }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Diziliş</p>
                    <p class="text-white">{{ $lineup->formation }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Oluşturan</p>
                    <p class="text-white">{{ $lineup->creator?->name }} {{ $lineup->creator?->surname }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
