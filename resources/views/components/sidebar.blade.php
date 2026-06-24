@php
    $currentRoute = Route::currentRouteName();
@endphp

<div x-data="{
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        darkMode: localStorage.getItem('darkMode') === 'true',
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebarCollapsed', this.collapsed);
            this.$dispatch('sidebar-toggled', { collapsed: this.collapsed });
        },
        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
     }"
     x-init="$dispatch('sidebar-toggled', { collapsed: collapsed })"
     @keydown.window.ctrl.b.prevent="toggle()"
     @keydown.window.ctrl.shift.d.prevent="toggleDark()">

    {{-- ── SIDEBAR ── --}}
    <aside
        :class="collapsed ? 'w-[72px]' : 'w-64'"
        class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out hidden lg:flex"
        style="background-color: var(--sidebar-bg); border-right: 1px solid var(--sidebar-border);">

        {{-- Header / Logo --}}
        <div class="h-20 flex items-center px-4 shrink-0" style="border-bottom: 1px solid var(--sidebar-border);">
            {{-- Radiohead Wallet Logo --}}
            <svg class="w-8 h-8 text-[#386650] shrink-0" viewBox="0 0 44 44" fill="none">
                <!-- Radio waves -->
                <path d="M 22 4 C 12 4 4 12 4 22 M 22 8 C 14.5 8 8.5 14.5 8.5 22" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.4"/>
                <path d="M 22 4 C 32 4 40 12 40 22 M 22 8 C 29.5 8 35.5 14.5 35.5 22" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.4"/>
                
                <!-- Head / Robot face -->
                <rect x="12" y="16" width="20" height="18" rx="4" stroke="currentColor" stroke-width="2.5"/>
                
                <!-- Eyes -->
                <circle cx="17" cy="22" r="1.5" fill="currentColor"/>
                <circle cx="27" cy="22" r="1.5" fill="currentColor"/>
                
                <!-- Mouth/Wallet slot -->
                <line x1="16" y1="28" x2="28" y2="28" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                
                <!-- Antennas -->
                <line x1="14" y1="16" x2="10" y2="10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="30" y1="16" x2="34" y2="10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="10" cy="10" r="1.5" fill="currentColor"/>
                <circle cx="34" cy="10" r="1.5" fill="currentColor"/>
            </svg>
            {{-- App name: visible only when expanded --}}
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-100"
                  x-transition:enter-start="opacity-0 -translate-x-2"
                  x-transition:enter-end="opacity-100 translate-x-0"
                  x-transition:leave="transition ease-in duration-100"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="ml-3 text-lg font-bold text-gray-900 tracking-tight whitespace-nowrap">Radiohead Wallet</span>
        </div>

        {{-- Wallet Switcher --}}
        @php
            $wallets = auth()->check() ? auth()->user()->wallets : collect();
            $activeWalletId = session('active_wallet_id') ?? ($wallets->first()->id ?? null);
            $activeWallet = $wallets->firstWhere('id', $activeWalletId);
        @endphp
        @if($wallets->count() > 0 && $activeWallet)
        <div class="px-3 pb-2 pt-2 shrink-0 relative" style="border-bottom: 1px solid var(--sidebar-border);" x-data="{ openWalletMenu: false }">
            <button @click="openWalletMenu = !openWalletMenu" @click.away="openWalletMenu = false" 
                    class="flex items-center w-full py-2 px-3 text-sm font-semibold rounded-xl transition-colors bg-[#386650]/10 text-gray-700 dark:text-gray-300 hover:bg-[#386650]/20 border border-[#386650]/20"
                    :class="collapsed ? 'justify-center px-0' : ''"
                    :title="collapsed ? '{{ $activeWallet->name }}' : ''">
                <div class="w-6 h-6 rounded flex items-center justify-center shrink-0" style="background-color: {{ $activeWallet->color ?? '#386650' }}; color: white; font-size: 11px;">
                    {{ strtoupper(substr($activeWallet->name, 0, 1)) }}
                </div>
                <span x-show="!collapsed" class="ml-2 truncate text-left flex-1" x-transition>{{ $activeWallet->name }}</span>
                <svg x-show="!collapsed" class="w-4 h-4 ml-1 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            
            {{-- Dropdown Menu --}}
            <div x-show="openWalletMenu" 
                 x-transition
                 style="display: none; background-color: var(--card-bg); border: 1px solid var(--sidebar-border);"
                 class="absolute left-3 right-3 mt-1 rounded-xl shadow-lg z-50 overflow-hidden"
                 :class="collapsed ? 'w-48 left-16 top-0 mt-0' : ''">
                @foreach($wallets as $wallet)
                    <form method="POST" action="{{ route('wallets.switch') }}">
                        @csrf
                        <input type="hidden" name="wallet_id" value="{{ $wallet->id }}">
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center gap-2 {{ $activeWalletId == $wallet->id ? 'font-bold text-[#386650] bg-[#386650]/5' : 'text-gray-700 dark:text-gray-300' }}">
                            <div class="w-3.5 h-3.5 rounded-sm flex items-center justify-center shrink-0" style="background-color: {{ $wallet->color ?? '#386650' }};"></div>
                            <span class="truncate">{{ $wallet->name }}</span>
                        </button>
                    </form>
                @endforeach
                <div class="border-t border-gray-100 dark:border-gray-800 my-1"></div>
                <button onclick="document.getElementById('walletModal').classList.remove('hidden')" class="w-full text-left px-4 py-2.5 text-sm text-[#386650] font-medium hover:bg-[#386650]/5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Kelola Dompet
                </button>
            </div>
        </div>
        @endif

        {{-- Navigation Links --}}
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto overflow-x-hidden">
            {{-- Section Label --}}
            <p x-show="!collapsed"
               x-transition:enter="transition ease-out duration-200 delay-100"
               x-transition:enter-start="opacity-0"
               x-transition:enter-end="opacity-100"
               x-transition:leave="transition ease-in duration-75"
               x-transition:leave-start="opacity-100"
               x-transition:leave-end="opacity-0"
               class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 whitespace-nowrap">Menu Utama</p>

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               :title="collapsed ? 'Dashboard' : ''"
               class="flex items-center py-3 text-sm font-semibold rounded-xl transition-colors group {{ $currentRoute === 'dashboard' ? 'bg-[#386650]/10 text-[#386650]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="collapsed ? 'justify-center px-0' : 'px-4'">
                <svg class="w-5 h-5 shrink-0 {{ $currentRoute === 'dashboard' ? 'text-[#386650]' : 'text-gray-400 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="!collapsed"
                      x-transition:enter="transition ease-out duration-200 delay-100"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="ml-3 whitespace-nowrap">Dashboard</span>
            </a>

            {{-- Kategori --}}
            <a href="{{ route('categories.index') }}"
               :title="collapsed ? 'Kategori' : ''"
               class="flex items-center py-3 text-sm font-semibold rounded-xl transition-colors group {{ str_starts_with($currentRoute, 'categories') ? 'bg-[#386650]/10 text-[#386650]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="collapsed ? 'justify-center px-0' : 'px-4'">
                <svg class="w-5 h-5 shrink-0 {{ str_starts_with($currentRoute, 'categories') ? 'text-[#386650]' : 'text-gray-400 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span x-show="!collapsed"
                      x-transition:enter="transition ease-out duration-200 delay-100"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="ml-3 whitespace-nowrap">Kategori</span>
            </a>

            {{-- Transaksi --}}
            <a href="{{ route('transactions.index') }}"
               :title="collapsed ? 'Transaksi' : ''"
               class="flex items-center py-3 text-sm font-semibold rounded-xl transition-colors group {{ str_starts_with($currentRoute, 'transactions') ? 'bg-[#386650]/10 text-[#386650]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="collapsed ? 'justify-center px-0' : 'px-4'">
                <svg class="w-5 h-5 shrink-0 {{ str_starts_with($currentRoute, 'transactions') ? 'text-[#386650]' : 'text-gray-400 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="!collapsed"
                      x-transition:enter="transition ease-out duration-200 delay-100"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="ml-3 whitespace-nowrap">Transaksi</span>
            </a>
        </nav>

        {{-- Dark Mode Toggle --}}
        <div class="px-3 pb-2 shrink-0">
            <button @click="toggleDark()"
                    :title="collapsed ? (darkMode ? 'Light Mode' : 'Dark Mode') : ''"
                    class="flex items-center py-2.5 text-sm font-semibold rounded-xl transition-colors w-full text-gray-500 hover:bg-gray-50 hover:text-gray-900"
                    :class="collapsed ? 'justify-center px-0' : 'px-4'">
                {{-- Moon icon (shown in light mode) --}}
                <svg x-show="!darkMode" class="w-5 h-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                {{-- Sun icon (shown in dark mode) --}}
                <svg x-show="darkMode" class="w-5 h-5 shrink-0 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
                <span x-show="!collapsed"
                      x-transition:enter="transition ease-out duration-200 delay-100"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="ml-3 whitespace-nowrap" x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
            </button>
        </div>

        {{-- Profile Section at bottom --}}
        <div class="p-3 shrink-0" style="border-top: 1px solid var(--sidebar-border);">
            <div class="flex items-center rounded-xl p-2" :class="collapsed ? 'justify-center' : ''" style="background-color: var(--hover-bg);">
                {{-- Avatar --}}
                <a href="{{ route('profile.edit') }}" 
                   :title="collapsed ? '{{ Auth::user()->name ?? 'Profil' }}' : ''"
                   class="w-9 h-9 rounded-full bg-[#DCE8DE] flex items-center justify-center text-[#386650] font-bold shrink-0 hover:ring-2 hover:ring-[#386650]/30 transition-all text-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </a>
                {{-- User info: visible only when expanded --}}
                <div x-show="!collapsed"
                     x-transition:enter="transition ease-out duration-200 delay-100"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="ml-2 overflow-hidden flex-1">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-[11px] font-medium text-gray-500 truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>
                {{-- Logout button: visible only when expanded --}}
                <form x-show="!collapsed" method="POST" action="{{ route('logout') }}" class="shrink-0 ml-1"
                      x-transition:enter="transition ease-out duration-200 delay-100"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0">
                    @csrf
                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Log Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- Toggle Button: outside aside to avoid overflow clipping --}}
    <button @click="toggle()"
            :class="collapsed ? 'left-[56px]' : 'left-[240px]'"
            class="hidden lg:flex fixed top-[74px] z-[60] w-8 h-8 bg-[#386650] border-2 border-white rounded-full shadow-lg items-center justify-center text-white hover:bg-[#2d5241] hover:scale-110 focus:outline-none focus:ring-2 focus:ring-[#386650]/50 transition-all duration-300">
        <svg class="w-4 h-4 transition-transform duration-300" :class="collapsed ? 'rotate-0' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    {{-- Dynamic main content pusher using CSS variable --}}
    <style>
        .lg\:ml-64 { margin-left: 16rem; transition: margin-left 0.3s ease; }
        .lg\:ml-\[72px\] { margin-left: 72px; transition: margin-left 0.3s ease; }
    </style>

    {{-- Sync main content margin with sidebar state --}}
    <script>
        (function() {
            const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            document.querySelectorAll('.lg\\:ml-64').forEach(el => {
                if (collapsed) {
                    el.style.marginLeft = '72px';
                }
            });
        })();
    </script>

    {{-- React to sidebar toggle --}}
    <div x-data x-on:sidebar-toggled.window="
        const isCollapsed = $event.detail.collapsed;
        document.querySelectorAll('[class*=\'lg:ml-64\']').forEach(el => {
            el.style.marginLeft = isCollapsed ? '72px' : '16rem';
        });
    "></div>

    {{-- Include Wallet Modal globally with the sidebar --}}
    @include('components.wallet-modal')
</div>
