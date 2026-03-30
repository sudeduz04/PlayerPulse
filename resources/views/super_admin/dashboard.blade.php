<x-layouts.app title="Sistem Yönetim Paneli">
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Sistem Yönetim Paneli</h1>
            <p class="text-gray-500 text-sm mt-1">Tüm sistem verileri ve genel bakış.</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-gray-500 text-xs uppercase tracking-wider">Toplam Takım</p>
                    <div class="w-8 h-8 rounded-lg bg-accent/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $totalTeams }}</p>
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
                    <p class="text-gray-500 text-xs uppercase tracking-wider">Toplam Kullanıcı</p>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $totalUsers }}</p>
            </div>

            <div class="bg-surface-700 border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-gray-500 text-xs uppercase tracking-wider">Hesaplı Oyuncu</p>
                    <div class="w-8 h-8 rounded-lg bg-yellow-500/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $playersWithAccounts }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- User Role Distribution --}}
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-sm font-semibold text-white mb-4">Kullanıcı Rol Dağılımı</h2>
                <div class="space-y-4">
                    @php
                        $total = max($totalUsers, 1);
                        $roleLabels = ['super_admin' => 'Süper Yönetici', 'manager' => 'Yönetici', 'coach' => 'Antrenör', 'player' => 'Oyuncu'];
                        $roleColors = ['super_admin' => 'yellow', 'manager' => 'purple', 'coach' => 'blue', 'player' => 'accent'];
                    @endphp
                    @foreach($roleLabels as $roleKey => $roleLabel)
                        @php
                            $count = $usersByRole[$roleKey] ?? 0;
                            $percent = round(($count / $total) * 100);
                            $color = $roleColors[$roleKey];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1.5">
                                <span class="text-gray-400">{{ $roleLabel }}</span>
                                <span class="text-{{ $color === 'accent' ? 'accent' : $color . '-400' }} font-medium">{{ $count }}</span>
                            </div>
                            <div class="h-2 bg-surface-600 rounded-full overflow-hidden">
                                <div class="h-full bg-{{ $color === 'accent' ? 'accent' : $color . '-500' }} rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Player Status --}}
                <h2 class="text-sm font-semibold text-white mb-4 mt-6 pt-4 border-t border-border">Oyuncu Durumları</h2>
                <div class="space-y-4">
                    @php
                        $playerTotal = max($totalPlayers, 1);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-gray-400">Aktif</span>
                            <span class="text-accent font-medium">{{ $activePlayers }}</span>
                        </div>
                        <div class="h-2 bg-surface-600 rounded-full overflow-hidden">
                            <div class="h-full bg-accent rounded-full" style="width: {{ round(($activePlayers / $playerTotal) * 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-gray-400">Sakatlanmış</span>
                            <span class="text-red-400 font-medium">{{ $injuredPlayers }}</span>
                        </div>
                        <div class="h-2 bg-surface-600 rounded-full overflow-hidden">
                            <div class="h-full bg-red-500 rounded-full" style="width: {{ round(($injuredPlayers / $playerTotal) * 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-gray-400">Pasif</span>
                            <span class="text-gray-400 font-medium">{{ $inactivePlayers }}</span>
                        </div>
                        <div class="h-2 bg-surface-600 rounded-full overflow-hidden">
                            <div class="h-full bg-gray-500 rounded-full" style="width: {{ round(($inactivePlayers / $playerTotal) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Team Player Distribution Chart --}}
            <div class="lg:col-span-2 bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-sm font-semibold text-white mb-4">Takım Bazlı Oyuncu Dağılımı</h2>
                <div class="flex items-end gap-2 h-40">
                    @foreach($teamPlayerCounts as $team)
                        @php
                            $maxCount = $teamPlayerCounts->max('players_count') ?: 1;
                            $heightPercent = round(($team->players_count / $maxCount) * 100);
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-xs text-gray-400">{{ $team->players_count }}</span>
                            <div class="w-full bg-accent/20 rounded-t transition-all" style="height: {{ $heightPercent }}%"></div>
                            <span class="text-[9px] text-gray-500 truncate w-full text-center" title="{{ $team->name }}">{{ \Illuminate\Support\Str::limit($team->name, 6) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent Users --}}
            <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-white">Son Kullanıcılar</h2>
                    <a href="{{ route('super_admin.users.index') }}" class="text-accent hover:text-accent-hover text-xs transition-colors">Tümünü Gör &rarr;</a>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Ad</th>
                            <th class="px-4 py-2.5 text-left">Rol</th>
                            <th class="px-4 py-2.5 text-center">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($recentUsers as $user)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ route('super_admin.users.show', $user->id) }}" class="text-white hover:text-accent transition-colors font-medium">{{ $user->name }} {{ $user->surname }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    @switch($user->role)
                                        @case('super_admin')
                                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-yellow-500/15 text-yellow-400">Süper Yönetici</span>
                                            @break
                                        @case('manager')
                                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-purple-500/15 text-purple-400">Yönetici</span>
                                            @break
                                        @case('coach')
                                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-blue-500/15 text-blue-400">Antrenör</span>
                                            @break
                                        @case('player')
                                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">Oyuncu</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($user->status)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-500/15 text-red-400">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Recent Teams --}}
            <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-white">Takımlar</h2>
                    <a href="{{ route('super_admin.teams.index') }}" class="text-accent hover:text-accent-hover text-xs transition-colors">Tümünü Gör &rarr;</a>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Takım</th>
                            <th class="px-4 py-2.5 text-center">Oyuncu</th>
                            <th class="px-4 py-2.5 text-center">Antrenör</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($teams as $team)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ route('super_admin.teams.show', $team->id) }}" class="text-white hover:text-accent transition-colors font-medium">{{ $team->name }}</a>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $team->players_count }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $team->coaches_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Recent Players --}}
            <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-white">Son Oyuncular</h2>
                    <a href="{{ route('super_admin.players.index') }}" class="text-accent hover:text-accent-hover text-xs transition-colors">Tümünü Gör &rarr;</a>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Oyuncu</th>
                            <th class="px-4 py-2.5 text-left">Takım</th>
                            <th class="px-4 py-2.5 text-center">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($recentPlayers as $player)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ route('super_admin.players.show', $player->id) }}" class="text-white hover:text-accent transition-colors font-medium">
                                        {{ $player->first_name }} {{ $player->last_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $player->team?->name }}</td>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
