<x-layouts.app title="Kadro Detayı">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route($routePrefix . '.lineups.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Kadrolara Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $lineup->match?->homeTeam?->name ?? $lineup->match?->team?->name }} vs {{ $lineup->match?->awayTeam?->name ?? $lineup->match?->opponent_team }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $lineup->match?->match_date?->format('d.m.Y') }} · Diziliş: <span class="text-accent font-semibold">{{ $lineup->formation }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs rounded-full {{ $lineup->status === 'completed' ? 'bg-green-500/15 text-green-400' : ($lineup->status === 'failed' ? 'bg-red-500/15 text-red-400' : 'bg-yellow-500/15 text-yellow-300') }}">{{ $lineup->status }}</span>
                @if($lineup->is_ai_generated)
                    <span class="px-3 py-1 text-xs rounded-full bg-purple-500/15 text-purple-400">AI Üretti</span>
                @endif
            </div>
        </div>

        @if($lineup->status !== 'completed')
            <div id="lineup-status-panel" class="bg-surface-700 border border-border rounded-xl p-4 mb-6 text-sm">
                @if($lineup->status === 'failed')
                    <p class="text-red-400">{{ $lineup->error_message }}</p>
                @else
                    <p class="text-yellow-300">Kadro hazırlanıyor. Sonuç otomatik güncellenecek.</p>
                @endif
            </div>
        @endif

        @if($lineup->note)
            <div class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Not</p>
                <p class="text-gray-300 text-sm whitespace-pre-line">{{ $lineup->note }}</p>
            </div>
        @endif

        <div class="relative bg-emerald-900/70 border border-emerald-500/30 rounded-xl min-h-[650px] overflow-hidden mb-6">
            <div class="absolute inset-4 border border-white/20 rounded-lg"></div>
            <div class="absolute left-1/2 top-4 bottom-4 border-l border-white/20"></div>
            <div class="absolute left-1/2 top-1/2 w-32 h-32 -ml-16 -mt-16 rounded-full border border-white/20"></div>
            @foreach($lineup->players as $row)
                <div class="absolute w-44 bg-surface-800/95 border border-white/10 rounded-lg p-3 shadow-lg text-center" style="left: {{ $row->field_x ?? 50 }}%; top: {{ $row->field_y ?? 50 }}%; transform: translate(-50%, -50%);">
                    <div class="text-xs text-accent font-semibold">{{ $row->slot_key ?? $row->position?->code }}</div>
                    <div class="text-white text-sm font-semibold mt-1">#{{ $row->player?->jersey_number }} {{ $row->player?->first_name }} {{ $row->player?->last_name }}</div>
                    <div class="text-gray-400 text-xs mt-1">{{ $row->position?->name }}</div>
                    @if($lineup->is_ai_generated)
                        <div class="text-purple-300 text-xs mt-1">AI: {{ $row->recommendation_score ?? '-' }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Slot</th>
                        <th class="px-4 py-3">Oyuncu</th>
                        <th class="px-4 py-3">Pozisyon</th>
                        @if($lineup->is_ai_generated)<th class="px-4 py-3">AI Skoru</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($lineup->players as $row)
                        <tr>
                            <td class="px-4 py-3 text-accent">{{ $row->slot_key }}</td>
                            <td class="px-4 py-3 text-white">#{{ $row->player?->jersey_number }} {{ $row->player?->first_name }} {{ $row->player?->last_name }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $row->position?->name }}</td>
                            @if($lineup->is_ai_generated)<td class="px-4 py-3 text-purple-300">{{ $row->recommendation_score ?? '-' }}</td>@endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($lineup->is_ai_generated && in_array($lineup->status, ['queued', 'running'], true))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const statusUrl = @json(route($routePrefix . '.smart-squad.status', $lineup->id));
                const timer = setInterval(async () => {
                    const response = await window.axios.get(statusUrl);
                    const data = response.data.data;
                    if (data.status === 'completed') {
                        clearInterval(timer);
                        window.location.href = data.show_url;
                    }
                    if (data.status === 'failed') {
                        clearInterval(timer);
                        document.getElementById('lineup-status-panel').innerHTML = `<p class="text-red-400">${data.error_message || 'Kadro hazırlanamadı.'}</p>`;
                    }
                }, 2500);
            });
        </script>
    @endif
</x-layouts.app>
