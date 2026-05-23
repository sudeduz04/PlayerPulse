@php
    $user = auth()->user();
    $opponent = $match->opponentForUser($user);
    $side = $match->sideForUser($user);
    $goalsFor = $match->goalsForUser($user);
    $goalsAgainst = $match->goalsAgainstUser($user);
    $resultLabel = $match->resultForUser($user);
    $myTeamName = $side === 'away'
        ? ($match->awayTeam?->name ?? '-')
        : ($match->homeTeam?->name ?? $match->team?->name ?? '-');
    $showScore = in_array($match->status, ['finished', 'first_half', 'half_time', 'second_half'], true);
@endphp
<x-layouts.app title="vs {{ $opponent }}">
    <div>
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <div>
                <a href="{{ route($routePrefix . '.matches.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Maçlara Dön</a>
                <h1 class="text-3xl font-bold text-white">vs {{ $opponent }}</h1>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $myTeamName }}
                    @if($side === 'home') <span class="text-emerald-400">· İç Saha</span>
                    @elseif($side === 'away') <span class="text-orange-400">· Deplasman</span>
                    @endif
                    &middot; {{ $match->match_date?->format('d.m.Y') }}
                    @if($match->week) &middot; {{ $match->week }}. Hafta @endif
                </p>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                <div class="flex items-center gap-2">
                    <a href="{{ route($routePrefix . '.matches.stats.edit', $match->id) }}"
                       class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        İstatistik Gir
                    </a>
                    <a href="{{ route($routePrefix . '.matches.edit', $match->id) }}"
                       class="bg-surface-600 hover:bg-surface-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium border border-border transition-colors">
                        Düzenle
                    </a>
                    <form method="POST" action="{{ route($routePrefix . '.matches.destroy', $match->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Bu maçı silmek istediğinize emin misiniz?')"
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

        {{-- Score Banner --}}
        <div class="bg-surface-700 border border-border rounded-xl p-6 mb-6 text-center">
            <div class="flex items-center justify-center gap-8">
                <div>
                    <p class="text-gray-400 text-sm mb-1">{{ $myTeamName }}</p>
                    <p class="text-4xl font-bold text-white">{{ $showScore ? $goalsFor : '-' }}</p>
                </div>
                <p class="text-2xl font-bold text-gray-500">-</p>
                <div>
                    <p class="text-gray-400 text-sm mb-1">{{ $opponent }}</p>
                    <p class="text-4xl font-bold text-white">{{ $showScore ? $goalsAgainst : '-' }}</p>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-center gap-2">
                <span class="px-3 py-1 text-xs rounded-full {{ \App\Support\StatusLabels::badgeClasses($match->status) }}">
                    {{ \App\Support\StatusLabels::matchStatus($match->status) }}
                </span>
                @if($resultLabel)
                    @php
                        $resultClass = match($resultLabel) {
                            'Galibiyet' => 'bg-accent/15 text-accent',
                            'Mağlubiyet' => 'bg-red-500/15 text-red-400',
                            'Beraberlik' => 'bg-yellow-500/15 text-yellow-400',
                            default => 'bg-gray-500/15 text-gray-400',
                        };
                    @endphp
                    <span class="px-3 py-1 text-xs rounded-full {{ $resultClass }}">{{ $resultLabel }}</span>
                @endif
            </div>
        </div>

        {{-- Match Info --}}
        <div class="bg-surface-700 border border-border rounded-xl p-6 mb-6">
            <h2 class="text-lg font-semibold text-white mb-4">Maç Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Bizim Takım</p>
                    <p class="text-white">{{ $myTeamName }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Rakip</p>
                    <p class="text-white">{{ $opponent }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Tarih</p>
                    <p class="text-white">{{ $match->match_date?->format('d.m.Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Tür</p>
                    <p class="text-white">{{ $match->match_type }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">{{ $side === 'away' ? 'Deplasman Sahası' : 'Saha' }}</p>
                    <p class="text-white">{{ $match->location ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Sonuç</p>
                    <p class="text-white">{{ $resultLabel ?? '-' }}</p>
                </div>
            </div>
            @if($match->coach_note)
                <div class="mt-4">
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Antrenör Notu</p>
                    <p class="text-gray-300 text-sm">{{ $match->coach_note }}</p>
                </div>
            @endif
        </div>

        {{-- Player Match Stats --}}
        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Oyuncu İstatistikleri ({{ $match->playerMatchStats->count() }})</h2>
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                    <a href="{{ route($routePrefix . '.matches.stats.edit', $match->id) }}"
                       class="text-accent hover:text-accent-hover text-sm transition-colors">Düzenle &rarr;</a>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Oyuncu</th>
                            <th class="px-4 py-3">Poz.</th>
                            <th class="px-4 py-3 text-center">İlk 11</th>
                            <th class="px-4 py-3 text-center">Dk</th>
                            <th class="px-4 py-3 text-center">Gol</th>
                            <th class="px-4 py-3 text-center">Asist</th>
                            <th class="px-4 py-3 text-center">Şut</th>
                            <th class="px-4 py-3 text-center">B. Pas</th>
                            <th class="px-4 py-3 text-center">Pas %</th>
                            <th class="px-4 py-3 text-center">Top K.</th>
                            <th class="px-4 py-3 text-center">Kes.</th>
                            <th class="px-4 py-3 text-center">Çlm.</th>
                            <th class="px-4 py-3 text-center">Faul</th>
                            <th class="px-4 py-3 text-center">SK</th>
                            <th class="px-4 py-3 text-center">KK</th>
                            <th class="px-4 py-3 text-center">Puan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($match->playerMatchStats->load('player.position') as $stat)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-4 py-3 text-accent font-bold">{{ $stat->player?->jersey_number }}</td>
                                <td class="px-4 py-3 text-white font-medium whitespace-nowrap">{{ $stat->player?->first_name }} {{ $stat->player?->last_name }}</td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $stat->player?->position?->code }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($stat->is_starting)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">Evet</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->minutes_played }}'</td>
                                <td class="px-4 py-3 text-center text-white font-bold">{{ $stat->goals }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->assists }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->shots }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->successful_passes }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->pass_accuracy ? $stat->pass_accuracy.'%' : '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->tackles }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->interceptions }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->dribbles }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $stat->fouls }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($stat->yellow_cards > 0)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-yellow-500/15 text-yellow-400">{{ $stat->yellow_cards }}</span>
                                    @else
                                        <span class="text-gray-500">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($stat->red_cards > 0)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-500/15 text-red-400">{{ $stat->red_cards }}</span>
                                    @else
                                        <span class="text-gray-500">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-accent font-bold">{{ $stat->match_rating ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="px-6 py-8 text-center text-gray-500">Henüz istatistik kaydı bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
