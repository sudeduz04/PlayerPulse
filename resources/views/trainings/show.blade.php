<x-layouts.app title="{{ $training->title }}">
    <div>
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route($routePrefix . '.trainings.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Antrenmanlara Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $training->title }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $training->team?->name }} &middot; {{ $training->training_date?->format('d.m.Y') }}</p>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                <div class="flex items-center gap-2">
                    <a href="{{ route($routePrefix . '.trainings.performances.edit', $training->id) }}"
                       class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Performans Gir
                    </a>
                    <a href="{{ route($routePrefix . '.trainings.edit', $training->id) }}"
                       class="bg-surface-600 hover:bg-surface-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium border border-border transition-colors">
                        Düzenle
                    </a>
                    <form method="POST" action="{{ route($routePrefix . '.trainings.destroy', $training->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Bu antrenmanı silmek istediğinize emin misiniz?')"
                                class="bg-red-500/15 text-red-400 hover:bg-red-500/25 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Sil
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-accent/10 border border-accent/30 text-accent px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Training Info --}}
        <div class="bg-surface-700 border border-border rounded-xl p-6 mb-6">
            <h2 class="text-lg font-semibold text-white mb-4">Antrenman Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Takım</p>
                    <p class="text-white">{{ $training->team?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Tarih</p>
                    <p class="text-white">{{ $training->training_date?->format('d.m.Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Tür</p>
                    <p class="text-white">{{ $training->training_type }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Süre</p>
                    <p class="text-white">{{ $training->duration_minutes }} dakika</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Oluşturan</p>
                    <p class="text-white">{{ $training->creator?->name }} {{ $training->creator?->surname }}</p>
                </div>
            </div>
            @if($training->description)
                <div class="mt-4">
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Açıklama</p>
                    <p class="text-gray-300 text-sm">{{ $training->description }}</p>
                </div>
            @endif
            @if($training->coach_note)
                <div class="mt-4">
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Antrenör Notu</p>
                    <p class="text-gray-300 text-sm">{{ $training->coach_note }}</p>
                </div>
            @endif
        </div>

        {{-- Player Performances --}}
        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Oyuncu Performansları ({{ $training->performances->count() }})</h2>
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                    <a href="{{ route($routePrefix . '.trainings.performances.edit', $training->id) }}"
                       class="text-accent hover:text-accent-hover text-sm transition-colors">Düzenle &rarr;</a>
                @endif
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Oyuncu</th>
                        <th class="px-4 py-3">Pozisyon</th>
                        <th class="px-4 py-3 text-center">Katılım</th>
                        <th class="px-4 py-3 text-center">Genel</th>
                        <th class="px-4 py-3 text-center">Hız</th>
                        <th class="px-4 py-3 text-center">Dayanıklılık</th>
                        <th class="px-4 py-3 text-center">Teknik</th>
                        <th class="px-4 py-3 text-center">Disiplin</th>
                        <th class="px-4 py-3">Yorum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($training->performances as $perf)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-4 py-3 text-accent font-bold">{{ $perf->player?->jersey_number }}</td>
                            <td class="px-4 py-3 text-white font-medium">{{ $perf->player?->first_name }} {{ $perf->player?->last_name }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $perf->player?->position?->code }}</td>
                            <td class="px-4 py-3 text-center">
                                @switch($perf->attendance_status)
                                    @case('attended')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">Katıldı</span>
                                        @break
                                    @case('absent')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-500/15 text-red-400">Gelmedi</span>
                                        @break
                                    @case('excused')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-yellow-500/15 text-yellow-400">İzinli</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-center text-white">{{ $perf->performance_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $perf->speed_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $perf->endurance_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $perf->technique_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $perf->discipline_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs max-w-[150px] truncate" title="{{ $perf->coach_comment }}">{{ $perf->coach_comment ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500">Henüz performans kaydı bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
