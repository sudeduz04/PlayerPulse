<x-layouts.app title="Yeni Kadro">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.lineups.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Kadrolara Dön</a>
            <h1 class="text-3xl font-bold text-white">Yeni Kadro</h1>
            <p class="text-gray-500 text-sm mt-1">Maç ve diziliş seç, ardından 11 ilk 11 oyuncuyu belirle.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Step 1: Pick match (GET) --}}
        <form method="GET" action="{{ route($routePrefix . '.lineups.create') }}" class="bg-surface-700 border border-border rounded-xl p-6 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[260px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Maç *</label>
                    <select name="match_id" required onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Maç seç</option>
                        @foreach($matches as $match)
                            <option value="{{ $match->id }}" {{ $selectedMatchId === $match->id ? 'selected' : '' }}>
                                {{ $match->match_date?->format('d.m.Y') }} — {{ $match->team?->name }} vs {{ $match->opponent_team }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-surface-600 hover:bg-surface-700 border border-border text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">Devam</button>
            </div>
        </form>

        @if($selectedMatchId && $roster->isNotEmpty())
            {{-- Step 2: Fill the lineup (POST) --}}
            <form method="POST" action="{{ route($routePrefix . '.lineups.store') }}" class="bg-surface-700 border border-border rounded-xl p-6">
                @csrf
                <input type="hidden" name="match_id" value="{{ $selectedMatchId }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Diziliş *</label>
                        <select name="formation" required
                                class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                            @foreach(['4-4-2', '4-3-3', '4-2-3-1', '3-5-2', '5-3-2', '3-4-3', '4-5-1'] as $f)
                                <option value="{{ $f }}" {{ old('formation') === $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Not</label>
                        <input type="text" name="note" value="{{ old('note') }}"
                               class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent"
                               placeholder="Kadroyla ilgili not...">
                    </div>
                </div>

                <h2 class="text-sm font-medium text-gray-300 mb-3">İlk 11</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @for($i = 0; $i < 11; $i++)
                        <div class="flex items-center gap-2 bg-surface-600/40 border border-border rounded-lg p-3">
                            <span class="text-gray-500 text-xs w-6 text-center">{{ $i + 1 }}</span>
                            <select name="players[{{ $i }}][position_id]" required
                                    class="flex-1 px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                                <option value="">Pozisyon</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old("players.$i.position_id") == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="players[{{ $i }}][player_id]" required
                                    class="flex-[2] px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-accent">
                                <option value="">Oyuncu</option>
                                @foreach($roster as $player)
                                    <option value="{{ $player->id }}" {{ old("players.$i.player_id") == $player->id ? 'selected' : '' }}>
                                        #{{ $player->jersey_number }} {{ $player->first_name }} {{ $player->last_name }} ({{ $player->position?->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Kadroyu Kaydet
                    </button>
                    <a href="{{ route($routePrefix . '.lineups.index') }}" class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            </form>
        @elseif($selectedMatchId)
            <div class="bg-surface-700 border border-border rounded-xl p-6 text-center text-gray-400 text-sm">
                Bu takıma ait kayıtlı oyuncu yok. Önce kadroya oyuncu eklemelisin.
            </div>
        @endif
    </div>
</x-layouts.app>
