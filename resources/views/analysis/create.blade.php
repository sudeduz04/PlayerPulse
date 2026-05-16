<x-layouts.app title="Yeni AI Analizi">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.analysis.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Analizlere Dön</a>
            <h1 class="text-3xl font-bold text-white">Yeni AI Analizi</h1>
            <p class="text-gray-500 text-sm mt-1">Oyuncu seç, AI son verilerine bakıp analiz raporu hazırlasın.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('error') }}</div>
        @endif

        @if(!$aiReady)
            <div class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 px-4 py-3 rounded-lg mb-6 text-sm">
                AI sağlayıcısı yapılandırılmamış. <code class="bg-surface-600 px-2 py-0.5 rounded">.env</code>'de <code class="bg-surface-600 px-2 py-0.5 rounded">OPENAI_API_KEY</code> veya <code class="bg-surface-600 px-2 py-0.5 rounded">GEMINI_API_KEY</code> ekle.
            </div>
        @else
            <div class="bg-purple-500/10 border border-purple-500/30 text-purple-300 px-4 py-3 rounded-lg mb-6 text-sm">
                Aktif AI sağlayıcı: <strong class="capitalize">{{ $aiProvider }}</strong>
            </div>
        @endif

        <form method="POST" action="{{ route($routePrefix . '.analysis.store') }}" class="bg-surface-700 border border-border rounded-xl p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Oyuncu *</label>
                    <select name="player_id" required {{ $aiReady ? '' : 'disabled' }}
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Oyuncu seç</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}">
                                {{ $player->first_name }} {{ $player->last_name }} — {{ $player->team?->name }} / {{ $player->position?->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Özel Odak (opsiyonel)</label>
                <textarea name="focus" rows="3" {{ $aiReady ? '' : 'disabled' }}
                          class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent"
                          placeholder="Örn: 'savunma performansına odaklan', 'son 5 maça yoğunlaş', 'gelişim alanlarını detaylı incele'..."></textarea>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" {{ $aiReady ? '' : 'disabled' }}
                        class="bg-accent hover:bg-accent-hover disabled:bg-surface-600 disabled:text-gray-500 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Analizi Başlat
                </button>
                <a href="{{ route($routePrefix . '.analysis.index') }}" class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
