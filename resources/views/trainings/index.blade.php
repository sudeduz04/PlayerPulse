<x-layouts.app title="Antrenmanlar">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Antrenmanlar</h1>
                <p class="text-gray-500 text-sm mt-1">Antrenman programlarını görüntüle ve yönet.</p>
            </div>
            @if(auth()->user()->isRole('coach'))
                <a href="{{ route($routePrefix . '.trainings.create') }}"
                   class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    + Yeni Antrenman
                </a>
            @endif
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route($routePrefix . '.trainings.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Arama</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Antrenman ara..."
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
                    <input type="text" name="training_type" value="{{ $filters['training_type'] ?? '' }}"
                           placeholder="ör: Taktik"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                </div>
                <button type="submit"
                        class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters['search']) || !empty($filters['team_id']) || !empty($filters['training_type']))
                    <a href="{{ route($routePrefix . '.trainings.index') }}"
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
                        <th class="px-6 py-3">Başlık</th>
                        <th class="px-6 py-3">Takım</th>
                        <th class="px-6 py-3">Tarih</th>
                        <th class="px-6 py-3">Tür</th>
                        <th class="px-6 py-3">Süre</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($trainings as $training)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-6 py-4 font-medium text-white">{{ $training->title }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $training->team?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $training->training_date?->format('d.m.Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/15 text-blue-400">{{ $training->training_type }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-300">{{ $training->duration_minutes }} dk</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.trainings.show', $training->id) }}"
                                       class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    @if(auth()->user()->isRole('coach'))
                                        <a href="{{ route($routePrefix . '.trainings.edit', $training->id) }}"
                                           class="text-gray-400 hover:text-white text-sm transition-colors">Düzenle</a>
                                        <form method="POST" action="{{ route($routePrefix . '.trainings.destroy', $training->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Bu antrenmanı silmek istediğinize emin misiniz?')"
                                                    class="text-red-400 hover:text-red-300 text-sm transition-colors">Sil</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Henüz antrenman bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trainings->hasPages())
            <div class="mt-6">{{ $trainings->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app>
