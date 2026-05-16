<x-layouts.app title="AI Analizler">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">AI Analizler</h1>
                <p class="text-gray-500 text-sm mt-1">Oyuncularına yapay zekâ tabanlı analiz raporu oluştur.</p>
            </div>
            <a href="{{ route($routePrefix . '.analysis.create') }}"
               class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                + Yeni Analiz
            </a>
        </div>

        @if(session('success'))
            <div class="bg-accent/10 border border-accent/30 text-accent px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
        @endif
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

        <form method="GET" action="{{ route($routePrefix . '.analysis.index') }}" class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="min-w-[240px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Oyuncu</label>
                    <select name="player_id" class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Tümü</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}" {{ ($filters['player_id'] ?? '') == $player->id ? 'selected' : '' }}>
                                {{ $player->first_name }} {{ $player->last_name }} — {{ $player->team?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">Filtrele</button>
                @if(!empty($filters))
                    <a href="{{ route($routePrefix . '.analysis.index') }}" class="text-gray-400 hover:text-white px-3 py-2.5 text-sm transition-colors">Temizle</a>
                @endif
            </div>
        </form>

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Oyuncu</th>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3">Tip</th>
                        <th class="px-4 py-3 text-center">AI Skoru</th>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($analyses as $a)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-4 py-3 text-white font-medium">{{ $a->player?->first_name }} {{ $a->player?->last_name }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $a->player?->team?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300 text-xs">{{ $a->recommendation_type }}</td>
                            <td class="px-4 py-3 text-center text-purple-400 font-bold">{{ $a->score ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $a->created_at?->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.analysis.show', $a->id) }}" class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    <form method="POST" action="{{ route($routePrefix . '.analysis.destroy', $a->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Bu analizi silmek istediğinize emin misiniz?')"
                                                class="text-red-400 hover:text-red-300 text-sm transition-colors">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Henüz analiz yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($analyses->hasPages())
            <div class="mt-6">{{ $analyses->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app>
