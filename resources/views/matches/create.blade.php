<x-layouts.app title="Yeni Maç">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.matches.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Maçlara Dön</a>
            <h1 class="text-3xl font-bold text-white">Yeni Maç Ekle</h1>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl p-6">
            <form method="POST" action="{{ route($routePrefix . '.matches.store') }}">
                @csrf

                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Takım <span class="text-red-400">*</span></label>
                            @if($teams->count() === 1)
                                <input type="hidden" name="team_id" value="{{ $teams->first()->id }}">
                                <p class="px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm">{{ $teams->first()->name }}</p>
                            @else
                                <select name="team_id" id="team_id" required
                                        class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                    <option value="">Seçin...</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('team_id') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="match_type" class="block text-sm font-medium text-gray-300 mb-1.5">Maç Türü <span class="text-red-400">*</span></label>
                            <input type="text" name="match_type" id="match_type" value="{{ old('match_type') }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="ör: Lig, Kupa, Hazırlık">
                            @error('match_type') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="opponent_team" class="block text-sm font-medium text-gray-300 mb-1.5">Rakip Takım <span class="text-red-400">*</span></label>
                            <input type="text" name="opponent_team" id="opponent_team" value="{{ old('opponent_team') }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="Rakip takım adı">
                            @error('opponent_team') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-300 mb-1.5">Konum</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="ör: RAMS Park, Deplasman">
                            @error('location') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="match_date" class="block text-sm font-medium text-gray-300 mb-1.5">Maç Tarihi <span class="text-red-400">*</span></label>
                            <input type="date" name="match_date" id="match_date" value="{{ old('match_date') }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('match_date') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="result" class="block text-sm font-medium text-gray-300 mb-1.5">Sonuç</label>
                            <input type="text" name="result" id="result" value="{{ old('result') }}"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="ör: Galibiyet, Mağlubiyet, Beraberlik">
                            @error('result') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="goals_for" class="block text-sm font-medium text-gray-300 mb-1.5">Atılan Gol <span class="text-red-400">*</span></label>
                            <input type="number" name="goals_for" id="goals_for" value="{{ old('goals_for', 0) }}" required min="0"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('goals_for') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="goals_against" class="block text-sm font-medium text-gray-300 mb-1.5">Yenilen Gol <span class="text-red-400">*</span></label>
                            <input type="number" name="goals_against" id="goals_against" value="{{ old('goals_against', 0) }}" required min="0"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('goals_against') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="coach_note" class="block text-sm font-medium text-gray-300 mb-1.5">Antrenör Notu</label>
                        <textarea name="coach_note" id="coach_note" rows="3"
                                  class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                  placeholder="Maç hakkında notlar...">{{ old('coach_note') }}</textarea>
                        @error('coach_note') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-border">
                    <button type="submit"
                            class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Maç Ekle
                    </button>
                    <a href="{{ route($routePrefix . '.matches.index') }}"
                       class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
