@php($selectedTeams = collect(old('team_ids', $league?->teams?->pluck('id')->all() ?? []))->map(fn($id) => (int) $id)->all())
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-1.5">Lig Adı *</label>
        <input name="name" value="{{ old('name', $league?->name) }}" required class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-1.5">Sezon *</label>
        <input name="season" value="{{ old('season', $league?->season) }}" required class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="2026-2027">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-300 mb-1.5">Açıklama</label>
        <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm">{{ old('description', $league?->description) }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-300 mb-2">Lig Takımları *</label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 max-h-72 overflow-y-auto bg-surface-600/40 border border-border rounded-lg p-3">
            @foreach($teams as $team)
                <label class="flex items-center gap-2 text-sm text-gray-300">
                    <input type="checkbox" name="team_ids[]" value="{{ $team->id }}" @checked(in_array($team->id, $selectedTeams, true)) class="rounded bg-surface-600 border-border">
                    <span>{{ $team->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
