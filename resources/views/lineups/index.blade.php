<x-layouts.app title="Kadrolar">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Kadrolar</h1>
                <p class="text-gray-500 text-sm mt-1">Maç kadrolarını oluştur ve görüntüle.</p>
            </div>
            <a href="{{ route($routePrefix . '.lineups.create') }}"
               class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                + Yeni Kadro
            </a>
        </div>

        @if(session('success'))
            <div class="bg-accent/10 border border-accent/30 text-accent px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[720px]">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Maç</th>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3">Diziliş</th>
                        <th class="px-4 py-3">Kaynak</th>
                        <th class="px-4 py-3">Oluşturan</th>
                        <th class="px-4 py-3 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($lineups as $lineup)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-4 py-3 text-white font-medium">vs {{ $lineup->match?->opponent_team ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $lineup->match?->team?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $lineup->match?->match_date?->format('d.m.Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-accent font-bold">{{ $lineup->formation }}</td>
                            <td class="px-4 py-3">
                                @if($lineup->is_ai_generated)
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-purple-500/15 text-purple-400">AI</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">Manuel</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-300 text-xs">{{ $lineup->creator?->name }} {{ $lineup->creator?->surname }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.lineups.show', $lineup->id) }}" class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    <form method="POST" action="{{ route($routePrefix . '.lineups.destroy', $lineup->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Bu kadroyu silmek istediğinize emin misiniz?')"
                                                class="text-red-400 hover:text-red-300 text-sm transition-colors">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Henüz kadro bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lineups->hasPages())
            <div class="mt-6">{{ $lineups->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app>
