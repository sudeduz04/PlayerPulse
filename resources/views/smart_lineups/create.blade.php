<x-layouts.app title="Akıllı Kadro Önerisi">
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Akıllı Kadro Önerisi</h1>
            <p class="text-gray-500 text-sm mt-1">Yapay zekâ, oyuncu istatistiklerine bakarak senin için ilk 11 önersin.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if(!$aiReady)
            <div class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 px-4 py-3 rounded-lg mb-6 text-sm">
                AI sağlayıcısı yapılandırılmamış. <code class="bg-surface-600 px-2 py-0.5 rounded">.env</code> dosyasında <code class="bg-surface-600 px-2 py-0.5 rounded">OPENAI_API_KEY</code> veya <code class="bg-surface-600 px-2 py-0.5 rounded">GEMINI_API_KEY</code> ayarla.
            </div>
        @else
            <div class="bg-purple-500/10 border border-purple-500/30 text-purple-300 px-4 py-3 rounded-lg mb-6 text-sm">
                Aktif AI sağlayıcı: <strong class="capitalize">{{ $aiProvider }}</strong>
            </div>
        @endif

        <form method="POST" action="{{ route($routePrefix . '.smart-squad.store') }}" class="bg-surface-700 border border-border rounded-xl p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Maç *</label>
                    <select name="match_id" required {{ $aiReady ? '' : 'disabled' }}
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Maç seç</option>
                        @foreach($matches as $match)
                            <option value="{{ $match->id }}">
                                {{ $match->match_date?->format('d.m.Y') }} — {{ $match->team?->name }} vs {{ $match->opponent_team }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Diziliş *</label>
                    <select name="formation" required {{ $aiReady ? '' : 'disabled' }}
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                        @foreach(['4-4-2', '4-3-3', '4-2-3-1', '3-5-2', '5-3-2', '3-4-3', '4-5-1'] as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Ek Not (opsiyonel)</label>
                <textarea name="note" rows="3" {{ $aiReady ? '' : 'disabled' }}
                          class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent"
                          placeholder="Özel istek (örn. sakat oyuncuları hariç tut, deneyimli oyuncuları öne çıkar)..."></textarea>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" {{ $aiReady ? '' : 'disabled' }}
                        class="bg-accent hover:bg-accent-hover disabled:bg-surface-600 disabled:text-gray-500 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    AI Önerisi Al
                </button>
                <a href="{{ route($routePrefix . '.lineups.index') }}" class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
