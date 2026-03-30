<x-layouts.app title="Oyuncu Paneli">
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Oyuncu Paneli</h1>
            <p class="text-gray-500 text-sm mt-1">Kişisel bilgilerin ve takım durumun.</p>
        </div>

        @if($player)
            {{-- Player Profile --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Profile Card --}}
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
                    @if($player->team)
                        <div class="mt-4 pt-4 border-t border-border">
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Takım</p>
                            <p class="text-white font-medium">{{ $player->team->name }}</p>
                            <p class="text-gray-500 text-xs">{{ $player->team->age_category }} &middot; {{ $player->team->season }}</p>
                        </div>
                    @endif
                </div>

                {{-- Personal Info --}}
                <div class="lg:col-span-2 bg-surface-700 border border-border rounded-xl p-6">
                    <h2 class="text-sm font-semibold text-white mb-4">Kişisel Bilgiler</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Doğum Tarihi</p>
                            <p class="text-white text-sm">{{ $player->birth_date?->format('d.m.Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Forma No</p>
                            <p class="text-white text-sm font-bold">{{ $player->jersey_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Uyruk</p>
                            <p class="text-white text-sm">{{ $player->nationality ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Boy</p>
                            <p class="text-white text-sm">{{ $player->height ? $player->height . ' cm' : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Kilo</p>
                            <p class="text-white text-sm">{{ $player->weight ? $player->weight . ' kg' : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Baskın Ayak</p>
                            <p class="text-white text-sm">
                                @switch($player->dominant_foot)
                                    @case('left') Sol @break
                                    @case('right') Sağ @break
                                    @case('both') Her İkisi @break
                                    @default - @break
                                @endswitch
                            </p>
                        </div>
                    </div>

                    {{-- Placeholder Stats --}}
                    <div class="mt-6 pt-6 border-t border-border">
                        <h3 class="text-sm font-semibold text-white mb-3">İstatistikler</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-surface-600 rounded-lg p-4 text-center">
                                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Antrenman Katılımı</p>
                                <p class="text-2xl font-bold text-accent">--</p>
                                <p class="text-gray-500 text-[10px]">Yakında aktif olacak</p>
                            </div>
                            <div class="bg-surface-600 rounded-lg p-4 text-center">
                                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Maç Sayısı</p>
                                <p class="text-2xl font-bold text-blue-400">--</p>
                                <p class="text-gray-500 text-[10px]">Yakında aktif olacak</p>
                            </div>
                            <div class="bg-surface-600 rounded-lg p-4 text-center">
                                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Genel Puan</p>
                                <p class="text-2xl font-bold text-purple-400">--</p>
                                <p class="text-gray-500 text-[10px]">Yakında aktif olacak</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Teammates --}}
            @if($teamPlayers->isNotEmpty())
                <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-border">
                        <h2 class="text-sm font-semibold text-white">Takım Arkadaşlarım</h2>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-2.5 text-left">No</th>
                                <th class="px-4 py-2.5 text-left">Ad Soyad</th>
                                <th class="px-4 py-2.5 text-left">Pozisyon</th>
                                <th class="px-4 py-2.5 text-center">Durum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($teamPlayers as $teammate)
                                <tr class="hover:bg-surface-600 transition-colors">
                                    <td class="px-4 py-3 text-accent font-bold">{{ $teammate->jersey_number }}</td>
                                    <td class="px-4 py-3 text-white font-medium">{{ $teammate->first_name }} {{ $teammate->last_name }}</td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $teammate->position?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @switch($teammate->status)
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
            @endif

        @else
            {{-- No Player Profile Found --}}
            <div class="bg-surface-700 border border-border rounded-xl p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-500/15 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">Oyuncu Profili Bulunamadı</h2>
                <p class="text-gray-500 text-sm">Hesabınıza bağlı bir oyuncu profili henüz oluşturulmamış. Lütfen yöneticiniz ile iletişime geçin.</p>
            </div>
        @endif
    </div>
</x-layouts.app>
