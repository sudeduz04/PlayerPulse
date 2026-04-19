<x-layouts.app title="Antrenman Düzenle">
    <div>
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.trainings.show', $training->id) }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Antrenman Detayına Dön</a>
            <h1 class="text-3xl font-bold text-white">Antrenman Düzenle</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $training->title }}</p>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl p-6">
            <form method="POST" action="{{ route($routePrefix . '.trainings.update', $training->id) }}">
                @csrf
                @method('PUT')

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
                                        <option value="{{ $team->id }}" {{ old('team_id', $training->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('team_id') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="training_type" class="block text-sm font-medium text-gray-300 mb-1.5">Tür <span class="text-red-400">*</span></label>
                            <input type="text" name="training_type" id="training_type" value="{{ old('training_type', $training->training_type) }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('training_type') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">Başlık <span class="text-red-400">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $training->title) }}" required
                               class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                        @error('title') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="training_date" class="block text-sm font-medium text-gray-300 mb-1.5">Tarih <span class="text-red-400">*</span></label>
                            <input type="date" name="training_date" id="training_date" value="{{ old('training_date', $training->training_date?->format('Y-m-d')) }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('training_date') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-300 mb-1.5">Süre (dk) <span class="text-red-400">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $training->duration_minutes) }}" required min="1" max="600"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('duration_minutes') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">Açıklama</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">{{ old('description', $training->description) }}</textarea>
                        @error('description') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="coach_note" class="block text-sm font-medium text-gray-300 mb-1.5">Antrenör Notu</label>
                        <textarea name="coach_note" id="coach_note" rows="2"
                                  class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">{{ old('coach_note', $training->coach_note) }}</textarea>
                        @error('coach_note') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-border">
                    <button type="submit"
                            class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Güncelle
                    </button>
                    <a href="{{ route($routePrefix . '.trainings.show', $training->id) }}"
                       class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
