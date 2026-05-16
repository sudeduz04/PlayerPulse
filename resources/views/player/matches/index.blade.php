<x-layouts.app title="Performansım">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Performansım</h1>
                <p class="text-gray-500 text-sm mt-1">Maç istatistiklerini, skor katkını ve performans puanlarını takip et.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('player.matches.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Başlangıç</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Bitiş</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Maç Türü</label>
                    <input type="text" name="match_type" value="{{ $filters['match_type'] ?? '' }}"
                           placeholder="Lig, kupa..."
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                </div>
                <button type="submit"
                        class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['match_type']))
                    <a href="{{ route('player.matches.index') }}"
                       class="text-gray-400 hover:text-white px-3 py-2.5 text-sm transition-colors">Temizle</a>
                @endif
            </div>
        </form>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Maç</p>
                <p class="text-2xl font-bold text-white">{{ $summary['total_matches'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">İlk 11</p>
                <p class="text-2xl font-bold text-accent">{{ $summary['starts'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Dakika</p>
                <p class="text-2xl font-bold text-blue-400">{{ $summary['minutes'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Gol / Asist</p>
                <p class="text-2xl font-bold text-purple-400">{{ $summary['goals'] }} / {{ $summary['assists'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Ort. Puan</p>
                <p class="text-2xl font-bold text-yellow-400">{{ $summary['average_rating'] ?? '-' }}</p>
            </div>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">Rakip</th>
                            <th class="px-4 py-3">Takım</th>
                            <th class="px-4 py-3">Tarih</th>
                            <th class="px-4 py-3">Tür</th>
                            <th class="px-4 py-3 text-center">Skor</th>
                            <th class="px-4 py-3 text-center">İlk 11</th>
                            <th class="px-4 py-3 text-center">Dk</th>
                            <th class="px-4 py-3 text-center">Gol</th>
                            <th class="px-4 py-3 text-center">Asist</th>
                            <th class="px-4 py-3 text-center">Pas %</th>
                            <th class="px-4 py-3 text-center">Kart</th>
                            <th class="px-4 py-3 text-center">Puan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($stats as $stat)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-4 py-3 text-white font-medium">{{ $stat->match?->opponent_team ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $stat->match?->team?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $stat->match?->match_date?->format('d.m.Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $stat->match?->match_type ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-white font-bold">{{ $stat->match?->goals_for ?? 0 }} - {{ $stat->match?->goals_against ?? 0 }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($stat->is_starting)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">Evet</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->minutes_played }}</td>
                                <td class="px-4 py-3 text-center text-white">{{ $stat->goals }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->assists }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->pass_accuracy ? $stat->pass_accuracy.'%' : '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->yellow_cards }}S / {{ $stat->red_cards }}K</td>
                                <td class="px-4 py-3 text-center text-accent font-bold">{{ $stat->match_rating ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-6 py-8 text-center text-gray-500">Filtrelere uygun maç istatistiği bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($stats->hasPages())
            <div class="mt-6">{{ $stats->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app>
