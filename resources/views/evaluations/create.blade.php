<x-layouts.app title="Yeni Değerlendirme">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.evaluations.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Değerlendirmelere Dön</a>
            <h1 class="text-3xl font-bold text-white">Yeni Değerlendirme</h1>
            <p class="text-gray-500 text-sm mt-1">Oyuncu gelişimini teknik, fiziksel, taktik ve mental başlıklarda değerlendir.</p>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.evaluations.store') }}" class="bg-surface-700 border border-border rounded-xl p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Oyuncu *</label>
                    <select name="player_id" required
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Oyuncu seç</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}" {{ old('player_id') == $player->id ? 'selected' : '' }}>
                                {{ $player->first_name }} {{ $player->last_name }} - {{ $player->team?->name }} / {{ $player->position?->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('player_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Rapor Tarihi *</label>
                    <input type="date" name="report_date" required value="{{ old('report_date', date('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                    @error('report_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-5">
                @foreach([
                    'technical_development' => 'Teknik',
                    'physical_development' => 'Fiziksel',
                    'tactical_development' => 'Taktik',
                    'mental_development' => 'Mental',
                    'overall_score' => 'Genel',
                ] as $field => $label)
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ $label }} (0-10)</label>
                        <input type="number" name="{{ $field }}" value="{{ old($field) }}" step="0.1" min="0" max="10"
                               class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent"
                               placeholder="-">
                        @error($field) <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Güçlü Yönler</label>
                    <textarea name="strengths" rows="5"
                              class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-accent"
                              placeholder="Oyuncunun öne çıkan güçlü yönleri...">{{ old('strengths') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Gelişim Alanları</label>
                    <textarea name="weaknesses" rows="5"
                              class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-accent"
                              placeholder="Geliştirilmesi gereken yönler...">{{ old('weaknesses') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Öneriler</label>
                    <textarea name="recommendations" rows="5"
                              class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-accent"
                              placeholder="Antrenman ve gelişim önerileri...">{{ old('recommendations') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Kaydet
                </button>
                <a href="{{ route($routePrefix . '.evaluations.index') }}" class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
