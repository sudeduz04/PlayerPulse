<x-layouts.app title="Sağlık & Ölçümler">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Sağlık & Ölçümler</h1>
                <p class="text-gray-500 text-sm mt-1">Sakatlık geçmişini ve fiziksel gelişim kayıtlarını takip et.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('player.health.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Başlangıç</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Bitiş</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="min-w-[180px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Sakatlık Durumu</label>
                    <select name="status"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Tümü</option>
                        <option value="ongoing" {{ ($filters['status'] ?? '') === 'ongoing' ? 'selected' : '' }}>Devam Ediyor</option>
                        <option value="recovered" {{ ($filters['status'] ?? '') === 'recovered' ? 'selected' : '' }}>İyileşti</option>
                    </select>
                </div>
                <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['status']))
                    <a href="{{ route('player.health.index') }}" class="text-gray-400 hover:text-white px-3 py-2.5 text-sm transition-colors">Temizle</a>
                @endif
            </div>
        </form>

        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Sakatlık</p>
                <p class="text-2xl font-bold text-white">{{ $injurySummary['total_injuries'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Aktif</p>
                <p class="text-2xl font-bold text-red-400">{{ $injurySummary['ongoing'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Ölçüm</p>
                <p class="text-2xl font-bold text-white">{{ $measurementSummary['total_measurements'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Kilo</p>
                <p class="text-2xl font-bold text-blue-400">{{ $measurementSummary['latest_weight'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Sprint</p>
                <p class="text-2xl font-bold text-accent">{{ $measurementSummary['best_sprint_time'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Dayanıklılık</p>
                <p class="text-2xl font-bold text-purple-400">{{ $measurementSummary['average_endurance'] ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h2 class="text-lg font-semibold text-white">Sakatlık Geçmişi</h2>
                </div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">Tür</th>
                            <th class="px-4 py-3">Başlangıç</th>
                            <th class="px-4 py-3">Bitiş</th>
                            <th class="px-4 py-3">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($injuries as $injury)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-4 py-3 text-white font-medium">{{ $injury->injury_type }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $injury->start_date?->format('d.m.Y') }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $injury->end_date?->format('d.m.Y') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($injury->status === 'ongoing')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-500/15 text-red-400">Devam Ediyor</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">İyileşti</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Sakatlık kaydı bulunmuyor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h2 class="text-lg font-semibold text-white">Fiziksel Ölçümler</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3">Tarih</th>
                                <th class="px-4 py-3 text-center">Boy</th>
                                <th class="px-4 py-3 text-center">Kilo</th>
                                <th class="px-4 py-3 text-center">Yağ %</th>
                                <th class="px-4 py-3 text-center">Sprint</th>
                                <th class="px-4 py-3 text-center">Day.</th>
                                <th class="px-4 py-3 text-center">Güç</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($measurements as $measurement)
                                <tr class="hover:bg-surface-600 transition-colors">
                                    <td class="px-4 py-3 text-white font-medium">{{ $measurement->measurement_date?->format('d.m.Y') }}</td>
                                    <td class="px-4 py-3 text-center text-gray-300">{{ $measurement->height ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-300">{{ $measurement->weight ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-300">{{ $measurement->body_fat_percentage ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-300">{{ $measurement->sprint_time ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-300">{{ $measurement->endurance_level ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-300">{{ $measurement->strength_score ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Ölçüm kaydı bulunmuyor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
