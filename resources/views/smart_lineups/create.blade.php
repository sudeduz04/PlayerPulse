<x-layouts.app title="Akıllı Kadro Önerisi">
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Akıllı Kadro Önerisi</h1>
            <p class="text-gray-500 text-sm mt-1">Yapay zekâ, oyuncu istatistiklerine bakarak ilk 11 önerisini arka planda hazırlasın.</p>
        </div>

        @if(!$aiReady)
            <div class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 px-4 py-3 rounded-lg mb-6 text-sm">
                AI sağlayıcısı yapılandırılmamış. .env dosyasında OPENAI_API_KEY veya GEMINI_API_KEY ayarla.
            </div>
        @endif

        <div id="smart-job" class="hidden bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <p class="text-white font-medium" id="smart-job-title">Kadro sıraya alındı</p>
            <p class="text-gray-400 text-sm mt-1" id="smart-job-text">AI önerisi hazırlanıyor...</p>
            <a id="smart-job-link" href="#" class="hidden text-accent text-sm mt-3 inline-block">Kadroyu aç</a>
        </div>

        <form id="smart-form" method="POST" action="{{ route($routePrefix . '.smart-squad.store') }}" class="bg-surface-700 border border-border rounded-xl p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Maç *</label>
                    <select name="match_id" required {{ $aiReady ? '' : 'disabled' }} class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm">
                        <option value="">Maç seç</option>
                        @foreach($matches as $match)
                            <option value="{{ $match->id }}">{{ $match->match_date?->format('d.m.Y') }} — {{ $match->homeTeam?->name ?? $match->team?->name }} vs {{ $match->awayTeam?->name ?? $match->opponent_team }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Diziliş *</label>
                    <select name="formation" required {{ $aiReady ? '' : 'disabled' }} class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm">
                        @foreach(['4-4-2', '4-3-3', '4-2-3-1', '3-5-2', '5-3-2', '3-4-3', '4-5-1'] as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Ek Not</label>
                <textarea name="note" rows="3" {{ $aiReady ? '' : 'disabled' }} class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Sakat oyuncuları hariç tut, deneyimli oyuncuları öne çıkar..."></textarea>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button id="smart-submit" type="submit" {{ $aiReady ? '' : 'disabled' }} class="bg-accent hover:bg-accent-hover disabled:bg-surface-600 disabled:text-gray-500 text-white px-6 py-2.5 rounded-lg text-sm font-medium">AI Önerisi Al</button>
                <a href="{{ route($routePrefix . '.lineups.index') }}" class="text-gray-400 hover:text-white px-4 py-2.5 text-sm">İptal</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('smart-form');
            const panel = document.getElementById('smart-job');
            const title = document.getElementById('smart-job-title');
            const text = document.getElementById('smart-job-text');
            const link = document.getElementById('smart-job-link');
            const submit = document.getElementById('smart-submit');

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                submit.disabled = true;
                panel.classList.remove('hidden');
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
                            text.textContent = 'Kadro hazır, sayfaya yönlendiriliyorsun.';
                            clearInterval(timer);
                            setTimeout(() => { window.location.href = data.show_url; }, 600);
                        }
                        if (job.status === 'failed') {
                            text.textContent = job.error_message || 'AI kadro önerisi tamamlanamadı.';
                            clearInterval(timer);
                            submit.disabled = false;
                        }
                    }, 2500);
                } catch (error) {
                    title.textContent = 'AI önerisi başlatılamadı';
                    text.textContent = error.response?.data?.message || 'Bir hata oluştu.';
                    submit.disabled = false;
                }
            });
        });
    </script>
</x-layouts.app>
