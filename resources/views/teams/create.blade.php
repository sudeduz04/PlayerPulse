<x-layouts.app title="Yeni Takım">
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.teams.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Takımlara Dön</a>
            <h1 class="text-3xl font-bold text-white">Yeni Takım Oluştur</h1>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl p-6">
            <form method="POST" action="{{ route($routePrefix . '.teams.store') }}">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Takım Adı <span class="text-red-400">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                               placeholder="Takım adını girin">
                        @error('name') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="age_category" class="block text-sm font-medium text-gray-300 mb-1.5">Yaş Kategorisi <span class="text-red-400">*</span></label>
                            <select name="age_category" id="age_category" required
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="">Seçin...</option>
                                @foreach(['U13', 'U14', 'U15', 'U16', 'U17', 'U19', 'Senior'] as $cat)
                                    <option value="{{ $cat }}" {{ old('age_category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('age_category') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="season" class="block text-sm font-medium text-gray-300 mb-1.5">Sezon <span class="text-red-400">*</span></label>
                            <input type="text" name="season" id="season" value="{{ old('season') }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="2025-2026">
                            @error('season') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">Açıklama</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                  placeholder="Takım hakkında kısa bir açıklama...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-border">
                    <button type="submit"
                            class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Takım Oluştur
                    </button>
                    <a href="{{ route($routePrefix . '.teams.index') }}"
                       class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
