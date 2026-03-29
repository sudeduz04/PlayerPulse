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
    </div>
</x-layouts.app>
