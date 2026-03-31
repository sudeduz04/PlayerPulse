<x-layouts.app title="{{ $team->name }}">
    <div>
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route($routePrefix . '.teams.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Takımlara Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $team->name }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $team->age_category }} &middot; {{ $team->season }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if(auth()->user()->isRole('manager') || auth()->user()->isRole('super_admin'))
                    <a href="{{ route($routePrefix . '.teams.edit', $team->id) }}"
                       class="bg-surface-600 hover:bg-surface-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium border border-border transition-colors">
                        Düzenle
                    </a>
                @endif
                @if(auth()->user()->isRole('super_admin'))
                    <form method="POST" action="{{ route($routePrefix . '.teams.destroy', $team->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Bu takımı silmek istediğinize emin misiniz?')"
                                class="bg-red-500/15 text-red-400 hover:bg-red-500/25 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Sil
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Team Info --}}
        <div class="bg-surface-700 border border-border rounded-xl p-6 mb-6">
            <h2 class="text-lg font-semibold text-white mb-4">Takım Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Takım Adı</p>
                    <p class="text-white">{{ $team->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Yaş Kategorisi</p>
                    <p class="text-white">{{ $team->age_category }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Sezon</p>
                    <p class="text-white">{{ $team->season }}</p>
                </div>
            </div>
            @if($team->description)
                <div class="mt-4">
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Açıklama</p>
                    <p class="text-gray-300 text-sm">{{ $team->description }}</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Players --}}
            <div class="lg:col-span-2">
                <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Oyuncular ({{ $team->players->count() }})</h2>
                        <a href="{{ route($routePrefix . '.players.create', ['team_id' => $team->id]) }}"
                           class="text-accent hover:text-accent-hover text-sm transition-colors">+ Oyuncu Ekle</a>
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3">No</th>
                                <th class="px-6 py-3">Ad Soyad</th>
                                <th class="px-6 py-3">Pozisyon</th>
                                <th class="px-6 py-3">Durum</th>
                                <th class="px-6 py-3 text-right">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($team->players as $player)
                                <tr class="hover:bg-surface-600 transition-colors">
                                    <td class="px-6 py-3 text-accent font-bold">{{ $player->jersey_number }}</td>
                                    <td class="px-6 py-3 text-white">{{ $player->first_name }} {{ $player->last_name }}</td>
                                    <td class="px-6 py-3 text-gray-300">{{ $player->position?->name ?? '-' }}</td>
                                    <td class="px-6 py-3">
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
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route($routePrefix . '.players.show', $player->id) }}"
                                           class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-500">Henüz oyuncu bulunmuyor.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Staff --}}
            <div class="space-y-6">
                {{-- Managers --}}
                @php
                    $teamManagers = $team->staff->where('role', 'manager');
                    $teamCoaches = $team->staff->where('role', 'coach');
                @endphp
                <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-border">
                        <h2 class="text-lg font-semibold text-white">Yöneticiler ({{ $teamManagers->count() }})</h2>
                    </div>
                    <div class="divide-y divide-border">
                        @forelse($teamManagers as $mgr)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="text-white text-sm font-medium">{{ $mgr->name }} {{ $mgr->surname }}</p>
                                    <p class="text-gray-500 text-xs">{{ $mgr->email }}</p>
                                </div>
                                @if(auth()->user()->isRole('super_admin'))
                                    <form method="POST" action="{{ route('super_admin.teams.remove-staff', [$team->id, $mgr->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Yöneticiyi takımdan çıkarmak istediğinize emin misiniz?')"
                                                class="text-red-400 hover:text-red-300 text-xs transition-colors">Kaldır</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-6 text-center text-gray-500 text-sm">Henüz yönetici atanmamış.</div>
                        @endforelse
                    </div>

                    @if(auth()->user()->isRole('super_admin'))
                        <div class="px-6 py-4 border-t border-border">
                            <form method="POST" action="{{ route('super_admin.teams.assign-staff', $team->id) }}">
                                @csrf
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Yönetici Ata</label>
                                <div class="flex gap-2">
                                    <select name="user_id"
                                            class="flex-1 px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                        <option value="">Yönetici seçin...</option>
                                        @foreach($managers as $mgr)
                                            @unless($team->staff->contains('id', $mgr->id))
                                                <option value="{{ $mgr->id }}">{{ $mgr->name }} {{ $mgr->surname }}</option>
                                            @endunless
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Ata
                                    </button>
                                </div>
                                @error('user_id') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Coaches --}}
                <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-border">
                        <h2 class="text-lg font-semibold text-white">Antrenörler ({{ $teamCoaches->count() }})</h2>
                    </div>
                    <div class="divide-y divide-border">
                        @forelse($teamCoaches as $coach)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="text-white text-sm font-medium">{{ $coach->name }} {{ $coach->surname }}</p>
                                    <p class="text-gray-500 text-xs">{{ $coach->email }}</p>
                                </div>
                                @if(auth()->user()->isRole('super_admin'))
                                    <form method="POST" action="{{ route('super_admin.teams.remove-staff', [$team->id, $coach->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Antrenörü takımdan çıkarmak istediğinize emin misiniz?')"
                                                class="text-red-400 hover:text-red-300 text-xs transition-colors">Kaldır</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-6 text-center text-gray-500 text-sm">Henüz antrenör atanmamış.</div>
                        @endforelse
                    </div>

                    @if(auth()->user()->isRole('super_admin'))
                        <div class="px-6 py-4 border-t border-border">
                            <form method="POST" action="{{ route('super_admin.teams.assign-staff', $team->id) }}">
                                @csrf
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Antrenör Ata</label>
                                <div class="flex gap-2">
                                    <select name="user_id"
                                            class="flex-1 px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                        <option value="">Antrenör seçin...</option>
                                        @foreach($coaches as $coach)
                                            @unless($team->staff->contains('id', $coach->id))
                                                <option value="{{ $coach->id }}">{{ $coach->name }} {{ $coach->surname }}</option>
                                            @endunless
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Ata
                                    </button>
                                </div>
                                @error('user_id') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
