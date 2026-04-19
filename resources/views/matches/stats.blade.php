<x-layouts.app title="İstatistik Giriş - vs {{ $match->opponent_team }}">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.matches.show', $match->id) }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Maç Detayına Dön</a>
            <h1 class="text-3xl font-bold text-white">Maç İstatistikleri Giriş</h1>
            <p class="text-gray-500 text-sm mt-1">vs {{ $match->opponent_team }} &middot; {{ $match->match_date?->format('d.m.Y') }} &middot; {{ $match->team?->name }}</p>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.matches.stats.update', $match->id) }}">
            @csrf
            @method('PUT')

            <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                            <tr>
                                <th class="px-3 py-3">No</th>
                                <th class="px-3 py-3">Oyuncu</th>
                                <th class="px-3 py-3">Poz.</th>
                                <th class="px-3 py-3 text-center">İlk 11</th>
                                <th class="px-3 py-3 text-center">Dk</th>
                                <th class="px-3 py-3 text-center">Gol</th>
                                <th class="px-3 py-3 text-center">Asist</th>
                                <th class="px-3 py-3 text-center">Şut</th>
                                <th class="px-3 py-3 text-center">B. Pas</th>
                                <th class="px-3 py-3 text-center">Pas %</th>
                                <th class="px-3 py-3 text-center">Top K.</th>
                                <th class="px-3 py-3 text-center">Kes.</th>
                                <th class="px-3 py-3 text-center">Çlm.</th>
                                <th class="px-3 py-3 text-center">Faul</th>
                                <th class="px-3 py-3 text-center">SK</th>
                                <th class="px-3 py-3 text-center">KK</th>
                                <th class="px-3 py-3 text-center">Puan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($teamPlayers as $index => $player)
                                @php $existing = $existingStats->get($player->id); @endphp
                                <tr class="hover:bg-surface-600 transition-colors">
                                    <td class="px-3 py-3 text-accent font-bold">{{ $player->jersey_number }}</td>
                                    <td class="px-3 py-3 text-white font-medium whitespace-nowrap">{{ $player->first_name }} {{ $player->last_name }}</td>
                                    <td class="px-3 py-3 text-gray-400 text-xs">{{ $player->position?->code }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <input type="hidden" name="players[{{ $index }}][player_id]" value="{{ $player->id }}">
                                        <input type="hidden" name="players[{{ $index }}][is_starting]" value="0">
                                        <input type="checkbox" name="players[{{ $index }}][is_starting]" value="1"
                                               {{ old("players.{$index}.is_starting", $existing?->is_starting) ? 'checked' : '' }}
                                               class="w-4 h-4 rounded border-border bg-surface-600 text-accent focus:ring-accent focus:ring-1">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" name="players[{{ $index }}][minutes_played]"
                                               value="{{ old("players.{$index}.minutes_played", $existing?->minutes_played ?? 0) }}"
                                               min="0" max="200"
                                               class="w-14 px-1.5 py-1.5 bg-surface-600 border border-border rounded text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-accent">
                                    </td>
                                    @foreach(['goals', 'assists', 'shots', 'successful_passes'] as $field)
                                        <td class="px-3 py-2">
                                            <input type="number" name="players[{{ $index }}][{{ $field }}]"
                                                   value="{{ old("players.{$index}.{$field}", $existing?->$field ?? 0) }}"
                                                   min="0"
                                                   class="w-12 px-1.5 py-1.5 bg-surface-600 border border-border rounded text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-accent">
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-2">
                                        <input type="number" name="players[{{ $index }}][pass_accuracy]"
                                               value="{{ old("players.{$index}.pass_accuracy", $existing?->pass_accuracy) }}"
                                               step="0.1" min="0" max="100"
                                               class="w-14 px-1.5 py-1.5 bg-surface-600 border border-border rounded text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-accent"
                                               placeholder="-">
                                    </td>
                                    @foreach(['tackles', 'interceptions', 'dribbles', 'fouls'] as $field)
                                        <td class="px-3 py-2">
                                            <input type="number" name="players[{{ $index }}][{{ $field }}]"
                                                   value="{{ old("players.{$index}.{$field}", $existing?->$field ?? 0) }}"
                                                   min="0"
                                                   class="w-12 px-1.5 py-1.5 bg-surface-600 border border-border rounded text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-accent">
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-2">
                                        <input type="number" name="players[{{ $index }}][yellow_cards]"
                                               value="{{ old("players.{$index}.yellow_cards", $existing?->yellow_cards ?? 0) }}"
                                               min="0" max="2"
                                               class="w-12 px-1.5 py-1.5 bg-surface-600 border border-border rounded text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-accent">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" name="players[{{ $index }}][red_cards]"
                                               value="{{ old("players.{$index}.red_cards", $existing?->red_cards ?? 0) }}"
                                               min="0" max="1"
                                               class="w-12 px-1.5 py-1.5 bg-surface-600 border border-border rounded text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-accent">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" name="players[{{ $index }}][match_rating]"
                                               value="{{ old("players.{$index}.match_rating", $existing?->match_rating) }}"
                                               step="0.1" min="0" max="10"
                                               class="w-14 px-1.5 py-1.5 bg-surface-600 border border-border rounded text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-accent"
                                               placeholder="-">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($teamPlayers->isEmpty())
                <div class="bg-surface-700 border border-border rounded-xl p-8 text-center text-gray-500 mt-4">
                    Bu takımda aktif oyuncu bulunmuyor.
                </div>
            @else
                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                            class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Kaydet
                    </button>
                    <a href="{{ route($routePrefix . '.matches.show', $match->id) }}"
                       class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            @endif
        </form>
    </div>
</x-layouts.app>
