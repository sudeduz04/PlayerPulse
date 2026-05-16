<x-layouts.app title="{{ $player->first_name }} {{ $player->last_name }}">
    <div>
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route($routePrefix . '.players.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Oyunculara Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $player->first_name }} {{ $player->last_name }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $player->team?->name ?? '-' }} &middot; #{{ $player->jersey_number }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route($routePrefix . '.players.edit', $player->id) }}"
                   class="bg-surface-600 hover:bg-surface-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium border border-border transition-colors">
                    Düzenle
                </a>
                <form method="POST" action="{{ route($routePrefix . '.players.destroy', $player->id) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Bu oyuncuyu silmek istediğinize emin misiniz?')"
                            class="bg-red-500/15 text-red-400 hover:bg-red-500/25 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Sil
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-accent/10 border border-accent/30 text-accent px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Player Card --}}
            <div class="bg-surface-700 border border-border rounded-xl p-6 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-accent/20 border-2 border-accent/30 flex items-center justify-center text-3xl font-bold text-accent mb-4">
                    {{ $player->jersey_number }}
                </div>
                <h2 class="text-xl font-bold text-white">{{ $player->first_name }} {{ $player->last_name }}</h2>
                <p class="text-gray-400 text-sm mt-1">{{ $player->position?->name ?? '-' }}</p>
                <div class="mt-3">
                    @switch($player->status)
                        @case('active')
                            <span class="px-3 py-1 text-xs rounded-full bg-accent/15 text-accent">Aktif</span>
                            @break
                        @case('injured')
                            <span class="px-3 py-1 text-xs rounded-full bg-red-500/15 text-red-400">Sakatlanmış</span>
                            @break
                        @case('inactive')
                            <span class="px-3 py-1 text-xs rounded-full bg-gray-500/15 text-gray-400">Pasif</span>
                            @break
                    @endswitch
                </div>
            </div>

            {{-- Details --}}
            <div class="lg:col-span-2 bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Oyuncu Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-8">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Takım</p>
                        <p class="text-white">
                            @if($player->team)
                                <a href="{{ route($routePrefix . '.teams.show', $player->team->id) }}" class="text-accent hover:text-accent-hover transition-colors">
                                    {{ $player->team->name }}
                                </a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Pozisyon</p>
                        <p class="text-white">{{ $player->position?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Doğum Tarihi</p>
                        <p class="text-white">{{ $player->birth_date?->format('d.m.Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Forma Numarası</p>
                        <p class="text-white">{{ $player->jersey_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Boy</p>
                        <p class="text-white">{{ $player->height ? $player->height . ' cm' : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Kilo</p>
                        <p class="text-white">{{ $player->weight ? $player->weight . ' kg' : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Baskın Ayak</p>
                        <p class="text-white">
                            @switch($player->dominant_foot)
                                @case('left') Sol @break
                                @case('right') Sağ @break
                                @case('both') Her İkisi @break
                                @default - @break
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Uyruk</p>
                        <p class="text-white">{{ $player->nationality ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Injuries Section --}}
        <div class="mt-6 bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Sakatlıklar ({{ $player->injuries->count() }})</h2>
                <button onclick="document.getElementById('injuryForm').classList.toggle('hidden')"
                        class="text-accent hover:text-accent-hover text-sm transition-colors">+ Yeni Sakatlık</button>
            </div>
            {{-- Add Injury Form --}}
            <div id="injuryForm" class="hidden p-4 border-b border-border bg-surface-600/50">
                <form method="POST" action="{{ route($routePrefix . '.players.injuries.store', $player->id) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <input type="text" name="injury_type" required placeholder="Sakatlık türü"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <input type="date" name="start_date" required
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <input type="date" name="end_date" placeholder="Bitiş tarihi"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <select name="status" class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                                <option value="ongoing">Devam Ediyor</option>
                                <option value="recovered">İyileşti</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <textarea name="description" rows="2" placeholder="Açıklama..."
                                  class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-accent"></textarea>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Kaydet</button>
                    </div>
                </form>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Tür</th>
                        <th class="px-4 py-3">Başlangıç</th>
                        <th class="px-4 py-3">Bitiş</th>
                        <th class="px-4 py-3">Durum</th>
                        <th class="px-4 py-3">Açıklama</th>
                        <th class="px-4 py-3 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($player->injuries->sortByDesc('start_date') as $injury)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-4 py-3 text-white font-medium">{{ $injury->injury_type }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $injury->start_date->format('d.m.Y') }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $injury->end_date?->format('d.m.Y') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($injury->status === 'ongoing')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-500/15 text-red-400">Devam Ediyor</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">İyileşti</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs max-w-[200px] truncate">{{ $injury->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route($routePrefix . '.players.injuries.destroy', [$player->id, $injury->id]) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Silmek istediğinize emin misiniz?')"
                                            class="text-red-400 hover:text-red-300 text-xs transition-colors">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Sakatlık kaydı yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Physical Measurements Section --}}
        <div class="mt-6 bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Fiziksel Ölçümler ({{ $player->physicalMeasurements->count() }})</h2>
                <button onclick="document.getElementById('measurementForm').classList.toggle('hidden')"
                        class="text-accent hover:text-accent-hover text-sm transition-colors">+ Yeni Ölçüm</button>
            </div>
            <div id="measurementForm" class="hidden p-4 border-b border-border bg-surface-600/50">
                <form method="POST" action="{{ route($routePrefix . '.players.measurements.store', $player->id) }}">
                    @csrf
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Tarih *</label>
                            <input type="date" name="measurement_date" required
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Boy (cm)</label>
                            <input type="number" name="height" step="0.1" min="0" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Kilo (kg)</label>
                            <input type="number" name="weight" step="0.1" min="0" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Vücut Yağ %</label>
                            <input type="number" name="body_fat_percentage" step="0.1" min="0" max="100" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Sprint (sn)</label>
                            <input type="number" name="sprint_time" step="0.01" min="0" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Çeviklik (0-10)</label>
                            <input type="number" name="agility_score" step="0.1" min="0" max="10" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Dayanıklılık (0-10)</label>
                            <input type="number" name="endurance_level" step="0.1" min="0" max="10" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Güç (0-10)</label>
                            <input type="number" name="strength_score" step="0.1" min="0" max="10" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                    </div>
                    <div class="mt-3">
                        <textarea name="note" rows="2" placeholder="Not..."
                                  class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-accent"></textarea>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Kaydet</button>
                    </div>
                </form>
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
                            <th class="px-4 py-3 text-center">Çeviklik</th>
                            <th class="px-4 py-3 text-center">Dayanıklılık</th>
                            <th class="px-4 py-3 text-center">Güç</th>
                            <th class="px-4 py-3 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($player->physicalMeasurements->sortByDesc('measurement_date') as $m)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-4 py-3 text-white font-medium">{{ $m->measurement_date->format('d.m.Y') }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $m->height ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $m->weight ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $m->body_fat_percentage ? $m->body_fat_percentage.'%' : '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $m->sprint_time ? $m->sprint_time.'s' : '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $m->agility_score ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $m->endurance_level ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $m->strength_score ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route($routePrefix . '.players.measurements.destroy', [$player->id, $m->id]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Silmek istediğinize emin misiniz?')"
                                                class="text-red-400 hover:text-red-300 text-xs transition-colors">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-6 text-center text-gray-500">Ölçüm kaydı yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Development Reports Section --}}
        <div class="mt-6 bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Gelişim Raporları ({{ $player->developmentReports->count() }})</h2>
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                    <button onclick="document.getElementById('reportForm').classList.toggle('hidden')"
                            class="text-accent hover:text-accent-hover text-sm transition-colors">+ Yeni Rapor</button>
                @endif
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
            <div id="reportForm" class="hidden p-4 border-b border-border bg-surface-600/50">
                <form method="POST" action="{{ route($routePrefix . '.players.reports.store', $player->id) }}">
                    @csrf
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Tarih *</label>
                            <input type="date" name="report_date" required
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Teknik (0-10)</label>
                            <input type="number" name="technical_development" step="0.1" min="0" max="10" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Fiziksel (0-10)</label>
                            <input type="number" name="physical_development" step="0.1" min="0" max="10" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Taktik (0-10)</label>
                            <input type="number" name="tactical_development" step="0.1" min="0" max="10" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Mental (0-10)</label>
                            <input type="number" name="mental_development" step="0.1" min="0" max="10" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Genel Skor (0-10)</label>
                            <input type="number" name="overall_score" step="0.1" min="0" max="10" placeholder="-"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                        <div>
                            <textarea name="strengths" rows="2" placeholder="Güçlü yönler..."
                                      class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-accent"></textarea>
                        </div>
                        <div>
                            <textarea name="weaknesses" rows="2" placeholder="Zayıf yönler..."
                                      class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-accent"></textarea>
                        </div>
                        <div>
                            <textarea name="recommendations" rows="2" placeholder="Öneriler..."
                                      class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-accent"></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Kaydet</button>
                    </div>
                </form>
            </div>
            @endif
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">Tarih</th>
                            <th class="px-4 py-3">Oluşturan</th>
                            <th class="px-4 py-3 text-center">Teknik</th>
                            <th class="px-4 py-3 text-center">Fiziksel</th>
                            <th class="px-4 py-3 text-center">Taktik</th>
                            <th class="px-4 py-3 text-center">Mental</th>
                            <th class="px-4 py-3 text-center">Genel</th>
                            <th class="px-4 py-3">Güçlü</th>
                            <th class="px-4 py-3">Zayıf</th>
                            <th class="px-4 py-3 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($player->developmentReports->sortByDesc('report_date') as $report)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-4 py-3 text-white font-medium">{{ $report->report_date->format('d.m.Y') }}</td>
                                <td class="px-4 py-3 text-gray-300 text-xs">{{ $report->creator?->name }} {{ $report->creator?->surname }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $report->technical_development ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $report->physical_development ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $report->tactical_development ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $report->mental_development ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-accent font-bold">{{ $report->overall_score ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-400 text-xs max-w-[120px] truncate">{{ $report->strengths ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-400 text-xs max-w-[120px] truncate">{{ $report->weaknesses ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route($routePrefix . '.players.reports.destroy', [$player->id, $report->id]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Silmek istediğinize emin misiniz?')"
                                                class="text-red-400 hover:text-red-300 text-xs transition-colors">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-4 py-6 text-center text-gray-500">Gelişim raporu yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Player Notes Section --}}
        <div class="mt-6 bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Notlar ({{ $player->notes->count() }})</h2>
                <button onclick="document.getElementById('noteForm').classList.toggle('hidden')"
                        class="text-accent hover:text-accent-hover text-sm transition-colors">+ Yeni Not</button>
            </div>
            <div id="noteForm" class="hidden p-4 border-b border-border bg-surface-600/50">
                <form method="POST" action="{{ route($routePrefix . '.players.notes.store', $player->id) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <input type="date" name="note_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                        </div>
                        <div class="md:col-span-3">
                            <textarea name="note" rows="2" required placeholder="Not ekleyin..."
                                      class="w-full px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-accent"></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Kaydet</button>
                    </div>
                </form>
            </div>
            <div class="divide-y divide-border">
                @forelse($player->notes->sortByDesc('note_date') as $note)
                    <div class="px-4 py-3 hover:bg-surface-600 transition-colors flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-gray-400 text-xs">{{ $note->note_date->format('d.m.Y') }}</span>
                                <span class="text-gray-500 text-xs">—</span>
                                <span class="text-gray-400 text-xs">{{ $note->author?->name }} {{ $note->author?->surname }}</span>
                            </div>
                            <p class="text-gray-300 text-sm">{{ $note->note }}</p>
                        </div>
                        <form method="POST" action="{{ route($routePrefix . '.players.notes.destroy', [$player->id, $note->id]) }}" class="inline flex-shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Silmek istediğinize emin misiniz?')"
                                    class="text-red-400 hover:text-red-300 text-xs transition-colors">Sil</button>
                        </form>
                    </div>
                @empty
                    <div class="px-4 py-6 text-center text-gray-500">Not yok.</div>
                @endforelse
            </div>
        </div>

        {{-- Development Charts Section --}}
        <div class="mt-6 bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Gelişim Grafikleri</h2>
                <button onclick="document.getElementById('progressCharts').classList.toggle('hidden')"
                        class="text-accent hover:text-accent-hover text-sm transition-colors">Göster / Gizle</button>
            </div>
            <div id="progressCharts" class="hidden p-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-surface-600/40 border border-border rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-200 mb-3">Gelişim Raporu Trendi</h3>
                        @if(!empty($chartData['development']['categories']))
                            <div id="chart-development"></div>
                        @else
                            <div class="text-gray-500 text-sm py-12 text-center">Henüz gelişim raporu yok.</div>
                        @endif
                    </div>
                    <div class="bg-surface-600/40 border border-border rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-200 mb-3">Maç Performans Trendi</h3>
                        @if(!empty($chartData['matches']['categories']))
                            <div id="chart-matches"></div>
                        @else
                            <div class="text-gray-500 text-sm py-12 text-center">Henüz maç istatistiği yok.</div>
                        @endif
                    </div>
                    <div class="bg-surface-600/40 border border-border rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-200 mb-3">Antrenman Performans Trendi</h3>
                        @if(!empty($chartData['trainings']['categories']))
                            <div id="chart-trainings"></div>
                        @else
                            <div class="text-gray-500 text-sm py-12 text-center">Henüz antrenman performansı yok.</div>
                        @endif
                    </div>
                    <div class="bg-surface-600/40 border border-border rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-200 mb-3">Fiziksel Ölçüm Trendi</h3>
                        @if(!empty($chartData['measurements']['categories']))
                            <div id="chart-measurements"></div>
                        @else
                            <div class="text-gray-500 text-sm py-12 text-center">Henüz fiziksel ölçüm yok.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Player Account Section --}}
        @if(auth()->user()->isRole('manager') || auth()->user()->isRole('super_admin'))
            <div class="mt-6 bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Oyuncu Hesabı</h2>
                @if($player->user_id)
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 text-xs rounded-full bg-accent/15 text-accent">Hesap Mevcut</span>
                        <span class="text-gray-400 text-sm">{{ $player->user?->email }}</span>
                    </div>
                @else
                    <p class="text-gray-400 text-sm mb-4">Bu oyuncunun henüz bir giriş hesabı yok.</p>
                    <form method="POST" action="{{ route($routePrefix . '.players.create-account', $player->id) }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Bu oyuncu için hesap oluşturmak istediğinize emin misiniz?')"
                                class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Hesap Oluştur
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <script>
        window.playerProgressData = @json($chartData);

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.ApexCharts === 'undefined') return;
            const d = window.playerProgressData || {};

            const baseOpts = {
                chart: { type: 'line', height: 320, background: 'transparent', toolbar: { show: false } },
                theme: { mode: 'dark' },
                stroke: { curve: 'smooth', width: 2 },
                grid: { borderColor: '#2a2a2a' },
                legend: { labels: { colors: '#9ca3af' } },
                tooltip: { theme: 'dark' },
                markers: { size: 3 },
                dataLabels: { enabled: false },
            };
            const axisStyle = { labels: { style: { colors: '#9ca3af' } } };

            if (d.development && d.development.categories && d.development.categories.length) {
                new ApexCharts(document.querySelector('#chart-development'), {
                    ...baseOpts,
                    series: d.development.series,
                    xaxis: { categories: d.development.categories, ...axisStyle },
                    yaxis: { min: 0, max: 10, ...axisStyle },
                    colors: ['#22d3ee', '#a78bfa', '#f472b6', '#facc15', '#34d399'],
                }).render();
            }

            if (d.matches && d.matches.categories && d.matches.categories.length) {
                new ApexCharts(document.querySelector('#chart-matches'), {
                    ...baseOpts,
                    series: d.matches.series,
                    xaxis: { categories: d.matches.categories, ...axisStyle },
                    yaxis: [
                        { seriesName: 'Maç Puanı', title: { text: 'Puan / Sayı', style: { color: '#9ca3af' } }, ...axisStyle },
                        { seriesName: 'Pas İsabeti %', opposite: true, min: 0, max: 100, title: { text: 'Pas %', style: { color: '#9ca3af' } }, ...axisStyle },
                        { seriesName: 'Gol', show: false },
                        { seriesName: 'Asist', show: false },
                    ],
                    colors: ['#34d399', '#22d3ee', '#facc15', '#f472b6'],
                }).render();
            }

            if (d.trainings && d.trainings.categories && d.trainings.categories.length) {
                new ApexCharts(document.querySelector('#chart-trainings'), {
                    ...baseOpts,
                    series: d.trainings.series,
                    xaxis: { categories: d.trainings.categories, ...axisStyle },
                    yaxis: { min: 0, max: 10, ...axisStyle },
                    colors: ['#34d399', '#22d3ee', '#a78bfa', '#facc15', '#f472b6'],
                }).render();
            }

            if (d.measurements && d.measurements.categories && d.measurements.categories.length) {
                new ApexCharts(document.querySelector('#chart-measurements'), {
                    ...baseOpts,
                    series: d.measurements.series,
                    xaxis: { categories: d.measurements.categories, ...axisStyle },
                    yaxis: { ...axisStyle },
                    colors: ['#22d3ee', '#a78bfa', '#f472b6', '#facc15', '#34d399', '#fb923c', '#60a5fa'],
                }).render();
            }
        });
    </script>
</x-layouts.app>
