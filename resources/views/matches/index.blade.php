<x-layouts.app title="Maçlar">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Maçlar</h1>
                <p class="text-gray-500 text-sm mt-1">Maç programlarını görüntüle ve yönet.</p>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                <a href="{{ route($routePrefix . '.matches.create') }}"
                   class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    + Yeni Maç
                </a>
            @endif
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route($routePrefix . '.matches.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Arama</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Rakip takım ara..."
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Takım</label>
                    <select name="team_id"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option value="">Tümü</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ ($filters['team_id'] ?? '') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Tür</label>
                    <input type="text" name="match_type" value="{{ $filters['match_type'] ?? '' }}"
                           placeholder="ör: Lig, Kupa"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                </div>
                <button type="submit"
                        class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters['search']) || !empty($filters['team_id']) || !empty($filters['match_type']))
                    <a href="{{ route($routePrefix . '.matches.index') }}"
                       class="text-gray-400 hover:text-white px-3 py-2.5 text-sm transition-colors">Temizle</a>
                @endif
            </div>
        </form>

        @if(session('success'))
            <div class="bg-accent/10 border border-accent/30 text-accent px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3">Rakip Takım</th>
                        <th class="px-6 py-3">Takım</th>
                        <th class="px-6 py-3">Tarih</th>
                        <th class="px-6 py-3">Tür</th>
                        <th class="px-6 py-3 text-center">Skor</th>
                        <th class="px-6 py-3">Sonuç</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($matches as $match)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-6 py-4 font-medium text-white">{{ $match->opponent_team }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $match->team?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $match->match_date?->format('d.m.Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/15 text-blue-400">{{ $match->match_type }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-white">{{ $match->goals_for }} - {{ $match->goals_against }}</td>
                            <td class="px-6 py-4">
                                @if($match->result)
                                    @php
                                        $resultClass = match(strtolower($match->result)) {
                                            'galibiyet', 'kazandı', 'win' => 'bg-accent/15 text-accent',
                                            'mağlubiyet', 'kaybetti', 'loss' => 'bg-red-500/15 text-red-400',
                                            'beraberlik', 'draw' => 'bg-yellow-500/15 text-yellow-400',
                                            default => 'bg-gray-500/15 text-gray-400',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $resultClass }}">{{ $match->result }}</span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.matches.show', $match->id) }}"
                                       class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                                        <a href="{{ route($routePrefix . '.matches.edit', $match->id) }}"
                                           class="text-gray-400 hover:text-white text-sm transition-colors">Düzenle</a>
                                        <form method="POST" action="{{ route($routePrefix . '.matches.destroy', $match->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Bu maçı silmek istediğinize emin misiniz?')"
                                                    class="text-red-400 hover:text-red-300 text-sm transition-colors">Sil</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Henüz maç bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($matches->hasPages())
            <div class="mt-6">{{ $matches->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app>
