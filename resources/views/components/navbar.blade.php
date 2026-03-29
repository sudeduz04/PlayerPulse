@props(['title' => ''])

<header class="h-16 bg-surface-900 border-b border-border flex items-center justify-between px-6 shrink-0">
    {{-- Left: Page title / breadcrumb --}}
    <div class="flex items-center gap-2 text-sm">
        @if($title)
            <span class="text-gray-400">{{ $title }}</span>
        @endif
    </div>

    {{-- Right: Actions --}}
    <div class="flex items-center gap-3">
        {{-- Notification bell --}}
        <button class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-surface-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </button>

        {{-- Settings --}}
        <button class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-surface-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>

        {{-- User avatar with dropdown --}}
        <div class="relative" id="user-dropdown-wrapper">
            <button onclick="document.getElementById('user-dropdown').classList.toggle('hidden')"
                    class="w-9 h-9 rounded-full bg-accent/20 border border-accent/30 flex items-center justify-center text-sm font-bold text-accent hover:bg-accent hover:text-white transition-colors">
                {{ substr(auth()->user()->name, 0, 1) }}
            </button>

            {{-- Dropdown menu --}}
            <div id="user-dropdown"
                 class="hidden absolute right-0 mt-2 w-56 bg-surface-700 border border-border rounded-xl shadow-lg shadow-black/30 z-50 overflow-hidden">
                {{-- User info --}}
                <div class="px-4 py-3 border-b border-border">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }} {{ auth()->user()->surname }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1.5 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded-full bg-accent/15 text-accent">
                        {{ auth()->user()->role }}
                    </span>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-3 text-left text-sm text-gray-400 hover:text-red-400 hover:bg-surface-600 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Çıkış Yap
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- Close dropdown when clicking outside --}}
<script>
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('user-dropdown-wrapper');
        const dropdown = document.getElementById('user-dropdown');
        if (wrapper && !wrapper.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
