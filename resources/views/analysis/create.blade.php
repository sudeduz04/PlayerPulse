<x-layouts.app title="Yeni AI Analizi">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.analysis.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Analizlere Dön</a>
            <h1 class="text-3xl font-bold text-white">Yeni AI Analizi</h1>
            <p class="text-gray-500 text-sm mt-1">Oyuncu seç, analiz arka planda hazırlansın.</p>
        </div>

        @if(!$aiReady)
            <div class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 px-4 py-3 rounded-lg mb-6 text-sm">
                AI sağlayıcısı yapılandırılmamış. .env içinde OPENAI_API_KEY veya GEMINI_API_KEY ekle.
            </div>
        @endif

        <div id="analysis-job" class="hidden bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <p class="text-white font-medium" id="analysis-job-title">Analiz sıraya alındı</p>
            <p class="text-gray-400 text-sm mt-1" id="analysis-job-text">Sonuç bekleniyor...</p>
            <a id="analysis-job-link" href="#" class="hidden text-accent text-sm mt-3 inline-block">Analizi aç</a>
        </div>

        <form id="analysis-form" method="POST" action="{{ route($routePrefix . '.analysis.store') }}" class="bg-surface-700 border border-border rounded-xl p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Oyuncu *</label>
                    <select name="player_id" required {{ $aiReady ? '' : 'disabled' }} class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm">
                        <option value="">Oyuncu seç</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}">{{ $player->first_name }} {{ $player->last_name }} — {{ $player->team?->name }} / {{ $player->position?->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Özel Odak</label>
                <textarea name="focus" rows="3" {{ $aiReady ? '' : 'disabled' }} class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Savunma performansı, son 5 maç, gelişim alanları..."></textarea>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button id="analysis-submit" type="submit" {{ $aiReady ? '' : 'disabled' }} class="bg-accent hover:bg-accent-hover disabled:bg-surface-600 disabled:text-gray-500 text-white px-6 py-2.5 rounded-lg text-sm font-medium">Analizi Başlat</button>
                <a href="{{ route($routePrefix . '.analysis.index') }}" class="text-gray-400 hover:text-white px-4 py-2.5 text-sm">İptal</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('analysis-form');
            const panel = document.getElementById('analysis-job');
            const title = document.getElementById('analysis-job-title');
            const text = document.getElementById('analysis-job-text');
            const link = document.getElementById('analysis-job-link');
            const submit = document.getElementById('analysis-submit');

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                submit.disabled = true;
                panel.classList.remove('hidden');
                title.textContent = 'Analiz sıraya alındı';
                text.textContent = 'Arka planda hazırlanıyor...';

                try {
                    const response = await window.axios.post(form.action, new FormData(form));
                    const data = response.data.data;
                    link.href = data.show_url;
                    link.classList.remove('hidden');

                    const statusLabels = {
                        queued: 'Sıraya alındı',
                        running: 'İşleniyor',
                        completed: 'Hazır',
                        failed: 'Başarısız',
                    };
                    const timer = setInterval(async () => {
                        const status = await window.axios.get(data.status_url);
                        const job = status.data.data;
                        title.textContent = statusLabels[job.status] || 'Hazırlanıyor';
                        if (job.status === 'completed') {
                            text.textContent = 'Sonuç hazırlandı, sayfaya yönlendiriliyorsun.';
                            clearInterval(timer);
                            setTimeout(() => { window.location.href = data.show_url; }, 600);
                        }
                        if (job.status === 'failed') {
                            text.textContent = job.error_message || 'Analiz tamamlanamadı.';
                            clearInterval(timer);
                            submit.disabled = false;
                        }
                    }, 2500);
                } catch (error) {
                    title.textContent = 'Analiz başlatılamadı';
                    text.textContent = error.response?.data?.message || 'Bir hata oluştu.';
                    submit.disabled = false;
                }
            });
        });
    </script>
</x-layouts.app>
