<x-layouts.app title="Takımlar">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Takımlar</h1>
                <p class="text-gray-500 text-sm mt-1">Tüm takımları görüntüle ve yönet.</p>
            </div>
            @if(auth()->user()->isRole('manager'))
                <a href="{{ route($routePrefix . '.teams.create') }}"
                   class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    + Yeni Takım
                </a>
            @endif
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route($routePrefix . '.teams.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Arama</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Takım ara..."
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Yaş Kategorisi</label>
                    <select name="age_category"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option value="">Tümü</option>
                        @foreach(['U13', 'U14', 'U15', 'U16', 'U17', 'U19', 'Senior'] as $cat)
                            <option value="{{ $cat }}" {{ ($filters['age_category'] ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Sezon</label>
                    <input type="text" name="season" value="{{ $filters['season'] ?? '' }}"
                           placeholder="2025-2026"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                </div>
                <button type="submit"
                        class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters['search']) || !empty($filters['age_category']) || !empty($filters['season']))
                    <a href="{{ route($routePrefix . '.teams.index') }}"
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
                        <th class="px-6 py-3">Takım Adı</th>
                        <th class="px-6 py-3">Yaş Kategorisi</th>
                        <th class="px-6 py-3">Sezon</th>
                        <th class="px-6 py-3">Oyuncu</th>
                        <th class="px-6 py-3">Antrenör</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($teams as $team)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-6 py-4 font-medium text-white">{{ $team->name }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $team->age_category }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $team->season }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $team->players_count }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $team->coaches_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.teams.show', $team->id) }}"
                                       class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    <a href="{{ route($routePrefix . '.teams.edit', $team->id) }}"
                                       class="text-gray-400 hover:text-white text-sm transition-colors">Düzenle</a>
                                    @if(auth()->user()->isRole('manager'))
                                        <form method="POST" action="{{ route($routePrefix . '.teams.destroy', $team->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Bu takımı silmek istediğinize emin misiniz?')"
                                                    class="text-red-400 hover:text-red-300 text-sm transition-colors">Sil</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Henüz takım bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($teams->hasPages())
            <div class="mt-6">
                {{ $teams->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
