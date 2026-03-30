<x-layouts.app title="Kullanıcılar">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Kullanıcılar</h1>
                <p class="text-gray-500 text-sm mt-1">Tüm sistem kullanıcılarını görüntüle ve yönet.</p>
            </div>
            <a href="{{ route('super_admin.users.create') }}"
               class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                + Yeni Kullanıcı
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('super_admin.users.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Arama</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Ad, soyad veya e-posta ara..."
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Rol</label>
                    <select name="role"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option value="">Tümü</option>
                        <option value="super_admin" {{ ($filters['role'] ?? '') === 'super_admin' ? 'selected' : '' }}>Süper Yönetici</option>
                        <option value="manager" {{ ($filters['role'] ?? '') === 'manager' ? 'selected' : '' }}>Yönetici</option>
                        <option value="coach" {{ ($filters['role'] ?? '') === 'coach' ? 'selected' : '' }}>Antrenör</option>
                        <option value="player" {{ ($filters['role'] ?? '') === 'player' ? 'selected' : '' }}>Oyuncu</option>
                    </select>
                </div>
                <button type="submit"
                        class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters['search']) || !empty($filters['role']))
                    <a href="{{ route('super_admin.users.index') }}"
                       class="text-gray-400 hover:text-white px-3 py-2.5 text-sm transition-colors">
                        Temizle
                    </a>
                @endif
            </div>
        </form>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-accent/10 border border-accent/30 text-accent px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3">Ad Soyad</th>
                        <th class="px-6 py-3">E-posta</th>
                        <th class="px-6 py-3">Rol</th>
                        <th class="px-6 py-3">Durum</th>
                        <th class="px-6 py-3">Kayıt Tarihi</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($users as $user)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-6 py-4 font-medium text-white">{{ $user->name }} {{ $user->surname }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @switch($user->role)
                                    @case('super_admin')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-500/15 text-yellow-400">Süper Yönetici</span>
                                        @break
                                    @case('manager')
                                        <span class="px-2 py-1 text-xs rounded-full bg-purple-500/15 text-purple-400">Yönetici</span>
                                        @break
                                    @case('coach')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-500/15 text-blue-400">Antrenör</span>
                                        @break
                                    @case('player')
                                        <span class="px-2 py-1 text-xs rounded-full bg-accent/15 text-accent">Oyuncu</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status)
                                    <span class="px-2 py-1 text-xs rounded-full bg-accent/15 text-accent">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-500/15 text-red-400">Pasif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ $user->created_at?->format('d.m.Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('super_admin.users.show', $user->id) }}"
                                       class="text-accent hover:text-accent-hover text-sm transition-colors">Görüntüle</a>
                                    <a href="{{ route('super_admin.users.edit', $user->id) }}"
                                       class="text-gray-400 hover:text-white text-sm transition-colors">Düzenle</a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('super_admin.users.destroy', $user->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')"
                                                    class="text-red-400 hover:text-red-300 text-sm transition-colors">Sil</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Henüz kullanıcı bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="mt-6">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
