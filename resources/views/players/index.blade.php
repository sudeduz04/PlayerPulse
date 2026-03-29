<x-layouts.app title="Oyuncular">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Oyuncular</h1>
                <p class="text-gray-500 text-sm mt-1">Tüm oyuncuları görüntüle ve yönet.</p>
            </div>
            <a href="{{ route($routePrefix . '.players.create') }}"
               class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                + Yeni Oyuncu
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route($routePrefix . '.players.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Arama</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Oyuncu ara..."
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
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Pozisyon</label>
                    <select name="position_id"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option value="">Tümü</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ ($filters['position_id'] ?? '') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Durum</label>
                    <select name="status"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option value="">Tümü</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Pasif</option>
                        <option value="injured" {{ ($filters['status'] ?? '') === 'injured' ? 'selected' : '' }}>Sakatlanmış</option>
                    </select>
                </div>
                <button type="submit"
                        class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters['search']) || !empty($filters['team_id']) || !empty($filters['position_id']) || !empty($filters['status']))
                    <a href="{{ route($routePrefix . '.players.index') }}"
                       class="text-gray-400 hover:text-white px-3 py-2.5 text-sm transition-colors">
                        Temizle
                    </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Ad Soyad</th>
                        <th class="px-6 py-3">Takım</th>
                        <th class="px-6 py-3">Pozisyon</th>
                        <th class="px-6 py-3">Durum</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($players as $player)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-6 py-4 text-accent font-bold">{{ $player->jersey_number }}</td>
                            <td class="px-6 py-4 font-medium text-white">{{ $player->first_name }} {{ $player->last_name }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $player->team?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $player->position?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @switch($player->status)
                                    @case('active')
                                        <span class="px-2 py-1 text-xs rounded-full bg-accent/15 text-accent">Aktif</span>
                                        @break
                                    @case('injured')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-500/15 text-red-400">Sakatlanmış</span>
                                        @break
                                    @case('inactive')
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-500/15 text-gray-400">Pasif</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.players.show', $player->id) }}"
                                       class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    <a href="{{ route($routePrefix . '.players.edit', $player->id) }}"
                                       class="text-gray-400 hover:text-white text-sm transition-colors">Düzenle</a>
                                    <form method="POST" action="{{ route($routePrefix . '.players.destroy', $player->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Bu oyuncuyu silmek istediğinize emin misiniz?')"
                                                class="text-red-400 hover:text-red-300 text-sm transition-colors">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Henüz oyuncu bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($players->hasPages())
            <div class="mt-6">
                {{ $players->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
