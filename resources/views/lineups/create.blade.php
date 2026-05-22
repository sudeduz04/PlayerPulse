<x-layouts.app title="Yeni Kadro">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.lineups.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Kadrolara Dön</a>
            <h1 class="text-3xl font-bold text-white">Yeni Kadro</h1>
            <p class="text-gray-500 text-sm mt-1">Dizilişi seç, oyuncuları saha üzerinde slotlara yerleştir.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="GET" action="{{ route($routePrefix . '.lineups.create') }}" class="bg-surface-700 border border-border rounded-xl p-6 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[260px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Maç *</label>
                    <select name="match_id" required onchange="this.form.submit()" class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm">
                        <option value="">Maç seç</option>
                        @foreach($matches as $match)
                            <option value="{{ $match->id }}" {{ $selectedMatchId === $match->id ? 'selected' : '' }}>
                                {{ $match->match_date?->format('d.m.Y') }} — {{ $match->homeTeam?->name ?? $match->team?->name }} vs {{ $match->awayTeam?->name ?? $match->opponent_team }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        @if($selectedMatchId && $roster->isNotEmpty())
            <form method="POST" action="{{ route($routePrefix . '.lineups.store') }}" class="bg-surface-700 border border-border rounded-xl p-6">
                @csrf
                <input type="hidden" name="match_id" value="{{ $selectedMatchId }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Diziliş *</label>
                        <select id="formation-select" name="formation" required class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm">
                            @foreach(['4-4-2', '4-3-3', '4-2-3-1', '3-5-2', '5-3-2', '3-4-3', '4-5-1'] as $f)
                                <option value="{{ $f }}" {{ old('formation', '4-4-2') === $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Not</label>
                        <input type="text" name="note" value="{{ old('note') }}" class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Kadroyla ilgili not...">
                    </div>
                </div>

                <div id="lineup-field" class="relative bg-emerald-900/70 border border-emerald-500/30 rounded-xl min-h-[620px] overflow-hidden">
                    <div class="absolute inset-4 border border-white/20 rounded-lg"></div>
                    <div class="absolute left-1/2 top-4 bottom-4 border-l border-white/20"></div>
                    <div class="absolute left-1/2 top-1/2 w-28 h-28 -ml-14 -mt-14 rounded-full border border-white/20"></div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium">Kadroyu Kaydet</button>
                    <a href="{{ route($routePrefix . '.lineups.index') }}" class="text-gray-400 hover:text-white px-4 py-2.5 text-sm">İptal</a>
                </div>
            </form>
        @elseif($selectedMatchId)
            <div class="bg-surface-700 border border-border rounded-xl p-6 text-center text-gray-400 text-sm">Bu takıma ait kayıtlı oyuncu yok.</div>
        @endif
    </div>

    @if($selectedMatchId && $roster->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const formations = {
                    '4-4-2': [['GK'], ['LB','LCB','RCB','RB'], ['LM','LCM','RCM','RM'], ['LST','RST']],
                    '4-3-3': [['GK'], ['LB','LCB','RCB','RB'], ['LCM','CM','RCM'], ['LW','ST','RW']],
                    '4-2-3-1': [['GK'], ['LB','LCB','RCB','RB'], ['LDM','RDM'], ['LAM','CAM','RAM'], ['ST']],
                    '3-5-2': [['GK'], ['LCB','CB','RCB'], ['LWB','LCM','CM','RCM','RWB'], ['LST','RST']],
                    '5-3-2': [['GK'], ['LWB','LCB','CB','RCB','RWB'], ['LCM','CM','RCM'], ['LST','RST']],
                    '3-4-3': [['GK'], ['LCB','CB','RCB'], ['LM','LCM','RCM','RM'], ['LW','ST','RW']],
                    '4-5-1': [['GK'], ['LB','LCB','RCB','RB'], ['LM','LCM','CM','RCM','RM'], ['ST']],
                };
                const roster = @json($roster->map(fn($p) => ['id' => $p->id, 'label' => '#'.$p->jersey_number.' '.$p->first_name.' '.$p->last_name.' ('.$p->position?->code.')'])->values());
                const positions = @json($positions->map(fn($p) => ['id' => $p->id, 'label' => $p->name])->values());
                const field = document.getElementById('lineup-field');
                const select = document.getElementById('formation-select');

                function render() {
                    field.querySelectorAll('.slot-card').forEach(el => el.remove());
                    const lines = formations[select.value] || formations['4-4-2'];
                    const lineCount = lines.length;
                    let index = 0;
                    lines.forEach((line, lineIndex) => {
                        line.forEach((slot, slotIndex) => {
                            const x = Math.round(((slotIndex + 1) / (line.length + 1)) * 100);
                            const y = Math.round(((lineIndex + 1) / (lineCount + 1)) * 100);
                            const card = document.createElement('div');
                            card.className = 'slot-card absolute w-44 bg-surface-800/95 border border-white/10 rounded-lg p-2 shadow-lg';
                            card.style.left = `${x}%`;
                            card.style.top = `${y}%`;
                            card.style.transform = 'translate(-50%, -50%)';
                            card.innerHTML = `
                                <input type="hidden" name="players[${index}][slot_key]" value="${slot}">
                                <input type="hidden" name="players[${index}][field_x]" value="${x}">
                                <input type="hidden" name="players[${index}][field_y]" value="${y}">
                                <div class="text-xs text-accent font-semibold mb-1">${slot}</div>
                                <select name="players[${index}][position_id]" required class="w-full mb-1 px-2 py-1 bg-surface-600 border border-border rounded text-white text-xs">
                                    <option value="">Pozisyon</option>${positions.map(p => `<option value="${p.id}">${p.label}</option>`).join('')}
                                </select>
                                <select name="players[${index}][player_id]" required class="player-select w-full px-2 py-1 bg-surface-600 border border-border rounded text-white text-xs">
                                    <option value="">Oyuncu</option>${roster.map(p => `<option value="${p.id}">${p.label}</option>`).join('')}
                                </select>
                            `;
                            field.appendChild(card);
                            index++;
                        });
                    });
                    bindDuplicateGuard();
                }

                function bindDuplicateGuard() {
                    const selects = [...field.querySelectorAll('.player-select')];
                    selects.forEach(playerSelect => {
                        playerSelect.addEventListener('change', () => {
                            const used = selects.map(s => s.value).filter(Boolean);
                            selects.forEach(s => {
                                [...s.options].forEach(option => {
                                    option.disabled = option.value && s.value !== option.value && used.includes(option.value);
                                });
                            });
                        });
                    });
                }

                select.addEventListener('change', render);
                render();
            });
        </script>
    @endif
</x-layouts.app>
