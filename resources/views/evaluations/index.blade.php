<x-layouts.app title="Değerlendirmeler">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Değerlendirmeler</h1>
                <p class="text-gray-500 text-sm mt-1">Oyuncu gelişim raporlarını takip et.</p>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                <a href="{{ route($routePrefix . '.evaluations.create') }}"
                   class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    + Yeni Değerlendirme
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-accent/10 border border-accent/30 text-accent px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Toplam</p>
                <p class="text-2xl font-bold text-white">{{ $summary['total_reports'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Genel</p>
                <p class="text-2xl font-bold text-accent">{{ $summary['average_overall'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Teknik</p>
                <p class="text-2xl font-bold text-blue-400">{{ $summary['average_technical'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Fiziksel</p>
                <p class="text-2xl font-bold text-purple-400">{{ $summary['average_physical'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Taktik</p>
                <p class="text-2xl font-bold text-yellow-400">{{ $summary['average_tactical'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Mental</p>
                <p class="text-2xl font-bold text-pink-400">{{ $summary['average_mental'] ?? '-' }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route($routePrefix . '.evaluations.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Oyuncu Ara</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent text-sm"
                           placeholder="Ad veya soyad...">
                </div>
                <div class="min-w-[220px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Oyuncu</label>
                    <select name="player_id"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Tümü</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}" {{ ($filters['player_id'] ?? '') == $player->id ? 'selected' : '' }}>
                                {{ $player->first_name }} {{ $player->last_name }} - {{ $player->team?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Başlangıç</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Bitiş</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters))
                    <a href="{{ route($routePrefix . '.evaluations.index') }}" class="text-gray-400 hover:text-white px-3 py-2.5 text-sm transition-colors">Temizle</a>
                @endif
            </div>
        </form>

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Oyuncu</th>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3 text-center">Teknik</th>
                        <th class="px-4 py-3 text-center">Fiziksel</th>
                        <th class="px-4 py-3 text-center">Taktik</th>
                        <th class="px-4 py-3 text-center">Mental</th>
                        <th class="px-4 py-3 text-center">Genel</th>
                        <th class="px-4 py-3 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($reports as $report)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-4 py-3 text-white font-medium">{{ $report->player?->first_name }} {{ $report->player?->last_name }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $report->player?->team?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $report->report_date?->format('d.m.Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $report->technical_development ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $report->physical_development ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $report->tactical_development ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $report->mental_development ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-accent font-bold">{{ $report->overall_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.evaluations.show', $report->id) }}" class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                                        <form method="POST" action="{{ route($routePrefix . '.evaluations.destroy', $report->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Bu değerlendirmeyi silmek istediğinize emin misiniz?')"
                                                    class="text-red-400 hover:text-red-300 text-sm transition-colors">Sil</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">Henüz değerlendirme bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="mt-6">{{ $reports->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app>
