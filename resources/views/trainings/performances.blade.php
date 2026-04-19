<x-layouts.app title="Performans Giriş - {{ $training->title }}">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.trainings.show', $training->id) }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Antrenman Detayına Dön</a>
            <h1 class="text-3xl font-bold text-white">Performans Giriş</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $training->title }} &middot; {{ $training->training_date?->format('d.m.Y') }} &middot; {{ $training->team?->name }}</p>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.trainings.performances.update', $training->id) }}">
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
                                <th class="px-3 py-3 text-center">Katılım</th>
                                <th class="px-3 py-3 text-center">Genel</th>
                                <th class="px-3 py-3 text-center">Hız</th>
                                <th class="px-3 py-3 text-center">Dayanıklılık</th>
                                <th class="px-3 py-3 text-center">Teknik</th>
                                <th class="px-3 py-3 text-center">Disiplin</th>
                                <th class="px-3 py-3">Yorum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($teamPlayers as $index => $player)
                                @php $existing = $existingPerformances->get($player->id); @endphp
                                <tr class="hover:bg-surface-600 transition-colors">
                                    <td class="px-3 py-3 text-accent font-bold">{{ $player->jersey_number }}</td>
                                    <td class="px-3 py-3 text-white font-medium whitespace-nowrap">{{ $player->first_name }} {{ $player->last_name }}</td>
                                    <td class="px-3 py-3 text-gray-400 text-xs">{{ $player->position?->code }}</td>
                                    <td class="px-3 py-2">
                                        <input type="hidden" name="players[{{ $index }}][player_id]" value="{{ $player->id }}">
                                        <select name="players[{{ $index }}][attendance_status]"
                                                class="w-full px-2 py-1.5 bg-surface-600 border border-border rounded text-white text-xs focus:outline-none focus:ring-1 focus:ring-accent">
                                            <option value="attended" {{ old("players.{$index}.attendance_status", $existing?->attendance_status) === 'attended' ? 'selected' : '' }}>Katıldı</option>
                                            <option value="absent" {{ old("players.{$index}.attendance_status", $existing?->attendance_status) === 'absent' ? 'selected' : '' }}>Gelmedi</option>
                                            <option value="excused" {{ old("players.{$index}.attendance_status", $existing?->attendance_status) === 'excused' ? 'selected' : '' }}>İzinli</option>
                                        </select>
                                    </td>
                                    @foreach(['performance_score', 'speed_score', 'endurance_score', 'technique_score', 'discipline_score'] as $score)
                                        <td class="px-3 py-2">
                                            <input type="number" name="players[{{ $index }}][{{ $score }}]"
                                                   value="{{ old("players.{$index}.{$score}", $existing?->$score) }}"
                                                   step="0.1" min="0" max="10"
                                                   class="w-16 px-2 py-1.5 bg-surface-600 border border-border rounded text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-accent"
                                                   placeholder="-">
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-2">
                                        <input type="text" name="players[{{ $index }}][coach_comment]"
                                               value="{{ old("players.{$index}.coach_comment", $existing?->coach_comment) }}"
                                               class="w-32 px-2 py-1.5 bg-surface-600 border border-border rounded text-white text-xs focus:outline-none focus:ring-1 focus:ring-accent"
                                               placeholder="Yorum...">
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
                    <a href="{{ route($routePrefix . '.trainings.show', $training->id) }}"
                       class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            @endif
        </form>
    </div>
</x-layouts.app>
