<x-layouts.app title="AI Analiz Detayı">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route($routePrefix . '.analysis.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Analizlere Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $analysis->player?->first_name }} {{ $analysis->player?->last_name }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $analysis->player?->team?->name }} · {{ $analysis->created_at?->format('d.m.Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 text-xs rounded-full {{ \App\Support\StatusLabels::badgeClasses($analysis->status) }}">{{ \App\Support\StatusLabels::analysis($analysis->status) }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">AI Skoru</p>
                <p class="text-3xl font-bold text-purple-400" id="analysis-score">{{ $analysis->score ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Oyuncu</p>
                <p class="text-white text-lg">{{ $analysis->player?->first_name }} {{ $analysis->player?->last_name }}</p>
                <p class="text-gray-400 text-xs mt-1">#{{ $analysis->player?->jersey_number }} · {{ $analysis->player?->team?->name }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Durum</p>
                <p class="text-white text-lg" id="analysis-status">{{ \App\Support\StatusLabels::analysis($analysis->status) }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ $analysis->created_at?->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Analiz</h2>
            <div id="analysis-body" class="analysis-markdown text-gray-200 text-sm leading-relaxed">
                @if($analysis->status === 'failed')
                    <p class="text-red-400">{{ $analysis->error_message }}</p>
                @elseif($analysis->status !== 'completed')
                    <p class="text-yellow-300">Analiz hazırlanıyor. Sonuç burada otomatik görünecek.</p>
                @else
                    {!! $analysisHtml !!}
                @endif
            </div>
        </div>
    </div>

    <style>
        .analysis-markdown h2 { color: #fff; font-size: 1rem; font-weight: 700; margin: 1.25rem 0 .5rem; }
        .analysis-markdown h2:first-child { margin-top: 0; }
        .analysis-markdown ul { list-style: disc; padding-left: 1.25rem; margin: .35rem 0 1rem; }
        .analysis-markdown li { margin: .2rem 0; }
        .analysis-markdown p { margin: .4rem 0 1rem; }
        .analysis-markdown strong { color: #fff; }
    </style>

    @if(in_array($analysis->status, ['queued', 'running'], true))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const statusUrl = @json(route($routePrefix . '.analysis.status', $analysis->id));
                const body = document.getElementById('analysis-body');
                const status = document.getElementById('analysis-status');
                const score = document.getElementById('analysis-score');
                const statusLabels = {
                    queued: 'Sıraya alındı',
                    running: 'İşleniyor',
                    completed: 'Hazır',
                    failed: 'Başarısız',
                };
                const timer = setInterval(async () => {
                    const response = await window.axios.get(statusUrl);
                    const data = response.data.data;
                    status.textContent = statusLabels[data.status] || data.status;
                    score.textContent = data.score ?? '-';
                    if (data.status === 'completed') {
                        body.innerHTML = data.reason_html;
                        clearInterval(timer);
                    }
                    if (data.status === 'failed') {
                        body.innerHTML = `<p class="text-red-400">${data.error_message || 'Analiz tamamlanamadı.'}</p>`;
                        clearInterval(timer);
                    }
                }, 2500);
            });
        </script>
    @endif
</x-layouts.app>
