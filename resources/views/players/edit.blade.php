<x-layouts.app title="Oyuncu Düzenle">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.players.show', $player->id) }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Oyuncuya Dön</a>
            <h1 class="text-3xl font-bold text-white">Oyuncu Düzenle</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $player->first_name }} {{ $player->last_name }}</p>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl p-6">
            <form method="POST" action="{{ route($routePrefix . '.players.update', $player->id) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    {{-- Team & Position --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="team_id" class="block text-sm font-medium text-gray-300 mb-1.5">Takım <span class="text-red-400">*</span></label>
                            <select name="team_id" id="team_id" required
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="">Seçin...</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id', $player->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_id') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-300 mb-1.5">Pozisyon <span class="text-red-400">*</span></label>
                            <select name="position_id" id="position_id" required
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="">Seçin...</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id', $player->position_id) == $position->id ? 'selected' : '' }}>{{ $position->name }} ({{ $position->code }})</option>
                                @endforeach
                            </select>
                            @error('position_id') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Name --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-300 mb-1.5">Ad <span class="text-red-400">*</span></label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $player->first_name) }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('first_name') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-300 mb-1.5">Soyad <span class="text-red-400">*</span></label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $player->last_name) }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('last_name') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Birth date & Jersey --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-300 mb-1.5">Doğum Tarihi <span class="text-red-400">*</span></label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $player->birth_date?->format('Y-m-d')) }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('birth_date') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="jersey_number" class="block text-sm font-medium text-gray-300 mb-1.5">Forma Numarası <span class="text-red-400">*</span></label>
                            <input type="number" name="jersey_number" id="jersey_number" value="{{ old('jersey_number', $player->jersey_number) }}" required min="1" max="99"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('jersey_number') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Physical --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-300 mb-1.5">Boy (cm)</label>
                            <input type="number" name="height" id="height" value="{{ old('height', $player->height) }}" step="0.01" min="0"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('height') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-300 mb-1.5">Kilo (kg)</label>
                            <input type="number" name="weight" id="weight" value="{{ old('weight', $player->weight) }}" step="0.01" min="0"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('weight') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="dominant_foot" class="block text-sm font-medium text-gray-300 mb-1.5">Baskın Ayak <span class="text-red-400">*</span></label>
                            <select name="dominant_foot" id="dominant_foot" required
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="">Seçin...</option>
                                <option value="right" {{ old('dominant_foot', $player->dominant_foot) === 'right' ? 'selected' : '' }}>Sağ</option>
                                <option value="left" {{ old('dominant_foot', $player->dominant_foot) === 'left' ? 'selected' : '' }}>Sol</option>
                                <option value="both" {{ old('dominant_foot', $player->dominant_foot) === 'both' ? 'selected' : '' }}>Her İkisi</option>
                            </select>
                            @error('dominant_foot') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Extra --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-300 mb-1.5">Uyruk</label>
                            <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $player->nationality) }}"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('nationality') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">Durum</label>
                            <select name="status" id="status"
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="active" {{ old('status', $player->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $player->status) === 'inactive' ? 'selected' : '' }}>Pasif</option>
                                <option value="injured" {{ old('status', $player->status) === 'injured' ? 'selected' : '' }}>Sakatlanmış</option>
                            </select>
                            @error('status') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-border">
                    <button type="submit"
                            class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Güncelle
                    </button>
                    <a href="{{ route($routePrefix . '.players.show', $player->id) }}"
                       class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
