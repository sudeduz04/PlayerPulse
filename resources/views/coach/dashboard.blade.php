<x-layouts.app title="Antrenör Kontrol Paneli">
    <div>
        <div class="mb-6">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">TACTICAL COMMAND CENTER</p>
            <h1 class="text-3xl font-bold text-white">Antrenör Kontrol Paneli</h1>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-gray-500 text-xs uppercase tracking-wider">Takımlarım</p>
                    <div class="w-8 h-8 rounded-lg bg-accent/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $myTeams->count() }}</p>
            </div>

            <div class="bg-surface-700 border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-gray-500 text-xs uppercase tracking-wider">Toplam Oyuncu</p>
                    <div class="w-8 h-8 rounded-lg bg-blue-500/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $totalPlayers }}</p>
            </div>

            <div class="bg-surface-700 border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-gray-500 text-xs uppercase tracking-wider">Aktif Oyuncu</p>
                    <div class="w-8 h-8 rounded-lg bg-accent/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $activePlayers }}</p>
            </div>

            <div class="bg-surface-700 border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-gray-500 text-xs uppercase tracking-wider">Sakatlanmış</p>
                    <div class="w-8 h-8 rounded-lg bg-red-500/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $injuredPlayers }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- My Teams --}}
            <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h2 class="text-sm font-semibold text-white">Takımlarım</h2>
                </div>
                @if($myTeams->isNotEmpty())
                    <div class="divide-y divide-border">
                        @foreach($myTeams as $team)
                            <a href="{{ route('coach.teams.show', $team->id) }}" class="flex items-center justify-between px-6 py-4 hover:bg-surface-600 transition-colors">
                                <div>
                                    <p class="text-white font-medium text-sm">{{ $team->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $team->age_category }} &middot; {{ $team->season }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-accent font-bold text-lg">{{ $team->players_count }}</p>
                                    <p class="text-gray-500 text-[10px] uppercase">Oyuncu</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-500 text-sm">
                        Henüz takım atamanız yapılmamış.
                    </div>
                @endif
            </div>

            {{-- Team Comparison --}}
            <div class="lg:col-span-2 bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-sm font-semibold text-white mb-4">Takım Kadro Durumu</h2>
                @if($myTeams->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($myTeams as $team)
                            @php
                                $teamActivePlayers = $team->players->where('status', 'active')->count();
                                $teamInjuredPlayers = $team->players->where('status', 'injured')->count();
                                $teamInactivePlayers = $team->players->where('status', 'inactive')->count();
                                $teamTotal = max($team->players_count, 1);
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-white text-sm font-medium">{{ $team->name }}</span>
                                    <span class="text-gray-400 text-xs">{{ $team->players_count }} oyuncu</span>
                                </div>
                                <div class="h-3 bg-surface-600 rounded-full overflow-hidden flex">
                                    @if($teamActivePlayers > 0)
                                        <div class="h-full bg-accent" style="width: {{ round(($teamActivePlayers / $teamTotal) * 100) }}%" title="Aktif: {{ $teamActivePlayers }}"></div>
                                    @endif
                                    @if($teamInjuredPlayers > 0)
                                        <div class="h-full bg-red-500" style="width: {{ round(($teamInjuredPlayers / $teamTotal) * 100) }}%" title="Sakat: {{ $teamInjuredPlayers }}"></div>
                                    @endif
                                    @if($teamInactivePlayers > 0)
                                        <div class="h-full bg-gray-500" style="width: {{ round(($teamInactivePlayers / $teamTotal) * 100) }}%" title="Pasif: {{ $teamInactivePlayers }}"></div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 mt-1">
                                    <span class="text-[10px] text-accent">● Aktif {{ $teamActivePlayers }}</span>
                                    <span class="text-[10px] text-red-400">● Sakat {{ $teamInjuredPlayers }}</span>
                                    <span class="text-[10px] text-gray-500">● Pasif {{ $teamInactivePlayers }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center justify-center h-32 text-gray-500 text-sm">Takım verisi bulunamadı.</div>
                @endif
            </div>
        </div>

        {{-- Recent Players --}}
        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white">Oyuncularım</h2>
                <a href="{{ route('coach.players.index') }}" class="text-accent hover:text-accent-hover text-xs transition-colors">Tümünü Gör &rarr;</a>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2.5 text-left">No</th>
                        <th class="px-4 py-2.5 text-left">Oyuncu</th>
                        <th class="px-4 py-2.5 text-left">Takım</th>
                        <th class="px-4 py-2.5 text-left">Pozisyon</th>
                        <th class="px-4 py-2.5 text-center">Durum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($recentPlayers as $player)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-4 py-3 text-accent font-bold">{{ $player->jersey_number }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('coach.players.show', $player->id) }}" class="text-white hover:text-accent transition-colors font-medium">
                                    {{ $player->first_name }} {{ $player->last_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $player->team?->name }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $player->position?->name }}</td>
                            <td class="px-4 py-3 text-center">
                                @switch($player->status)
                                    @case('active')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">Aktif</span>
                                        @break
                                    @case('injured')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-500/15 text-red-400">Sakat</span>
                                        @break
                                    @case('inactive')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-gray-500/15 text-gray-400">Pasif</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Henüz oyuncu bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
