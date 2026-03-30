<x-layouts.app title="Kullanıcı Düzenle">
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('super_admin.users.show', $user->id) }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Kullanıcıya Dön</a>
            <h1 class="text-3xl font-bold text-white">Kullanıcı Düzenle</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $user->name }} {{ $user->surname }}</p>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl p-6">
            <form method="POST" action="{{ route('super_admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    {{-- Name --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Ad <span class="text-red-400">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('name') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="surname" class="block text-sm font-medium text-gray-300 mb-1.5">Soyad <span class="text-red-400">*</span></label>
                            <input type="text" name="surname" id="surname" value="{{ old('surname', $user->surname) }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('surname') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Email & Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">E-posta <span class="text-red-400">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('email') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-1.5">Telefon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                            @error('phone') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Şifre</label>
                        <input type="password" name="password" id="password"
                               class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                               placeholder="Boş bırakırsanız değişmez">
                        @error('password') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Role & Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-300 mb-1.5">Rol <span class="text-red-400">*</span></label>
                            <select name="role" id="role" required
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Süper Yönetici</option>
                                <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Yönetici</option>
                                <option value="coach" {{ old('role', $user->role) === 'coach' ? 'selected' : '' }}>Antrenör</option>
                                <option value="player" {{ old('role', $user->role) === 'player' ? 'selected' : '' }}>Oyuncu</option>
                            </select>
                            @error('role') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">Durum <span class="text-red-400">*</span></label>
                            <select name="status" id="status" required
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="1" {{ old('status', $user->status ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status', $user->status ? '1' : '0') === '0' ? 'selected' : '' }}>Pasif</option>
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
                    <a href="{{ route('super_admin.users.show', $user->id) }}"
                       class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
