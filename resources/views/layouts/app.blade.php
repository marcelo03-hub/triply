<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Triply — Plan Your Journey')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8FAF9] text-slate-800 min-h-screen flex flex-col antialiased">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-xs">
        <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 md:px-12 py-4">
            <a href="{{ route('home') }}" class="text-2xl font-extrabold text-emerald-700 tracking-tight flex items-center gap-1 group">
                <span class="text-3xl transition-transform group-hover:rotate-12">🧭</span>
                <span>Triply<span class="text-orange-500">.</span></span>
            </a>

            <!-- Desktop Menu Links -->
            <div class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                <a href="{{ route('home') }}" 
                   class="transition py-1 {{ request()->routeIs('home') ? 'text-emerald-700 font-bold border-b-2 border-emerald-600' : 'hover:text-emerald-700' }}">
                   Beranda
                </a>
                <a href="{{ route('destinasi') }}" 
                   class="transition py-1 {{ request()->routeIs('destinasi') ? 'text-emerald-700 font-bold border-b-2 border-emerald-600' : 'hover:text-emerald-700' }}">
                   🏕️ Destinasi
                </a>
                <a href="{{ route('jadwal') }}" 
                   class="transition py-1 {{ request()->routeIs('jadwal') ? 'text-emerald-700 font-bold border-b-2 border-emerald-600' : 'hover:text-emerald-700' }}">
                   🗓️ Jadwal
                </a>
                <a href="{{ route('anggaran') }}" 
                   class="transition py-1 {{ request()->routeIs('anggaran') ? 'text-emerald-700 font-bold border-b-2 border-emerald-600' : 'hover:text-emerald-700' }}">
                   💰 Anggaran
                </a>
                <a href="{{ route('ai.index') }}" 
                   class="transition py-1 {{ request()->routeIs('ai.*') ? 'text-emerald-700 font-bold border-b-2 border-emerald-600' : 'hover:text-emerald-700' }}">
                   ✨ Asisten AI
                </a>
                <a href="{{ route('trips.index') }}" 
                   class="transition py-1 {{ request()->routeIs('trips.*') && !request()->routeIs('trips.create*') ? 'text-emerald-700 font-bold border-b-2 border-emerald-600' : 'hover:text-emerald-700' }}">
                   Perjalanan Saya
                </a>
                @auth
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.destinations.index') }}" 
                           class="transition py-1 {{ request()->routeIs('admin.*') ? 'text-purple-700 font-bold border-b-2 border-purple-600' : 'text-purple-600 hover:text-purple-800' }}">
                           🛡️ Admin Panel
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Desktop Auth & Action Buttons -->
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('trips.create') }}"
                   class="inline-flex items-center gap-1.5 bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs md:text-sm font-semibold hover:bg-emerald-800 shadow-xs hover:shadow transition transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Rencana Baru</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" 
                       class="text-xs md:text-sm font-semibold text-slate-700 hover:text-emerald-700 px-3 py-2 rounded-xl hover:bg-slate-50 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" 
                       class="text-xs md:text-sm font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-xl transition">
                        Daftar
                    </a>
                @endguest

                @auth
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                        <a href="{{ route('favorites.index') }}" 
                           class="p-2 rounded-xl text-slate-500 hover:text-pink-600 hover:bg-pink-50 transition"
                           title="Destinasi Favorit">
                            <span class="text-base">❤️</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-emerald-700 bg-slate-100 hover:bg-emerald-50 px-3 py-1.5 rounded-xl transition"
                           title="Lihat profil">
                            <span>👤</span>
                            <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="text-xs font-semibold text-slate-500 hover:text-red-600 px-2.5 py-1.5 rounded-xl hover:bg-red-50 transition"
                                    title="Keluar dari akun">
                                Keluar
                            </button>
                        </form>
                    </div>
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <button id="mobileMenuBtn" type="button" class="md:hidden p-2 text-slate-600 hover:text-emerald-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="menuIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </nav>

        <!-- Mobile Menu Dropdown -->
        <div id="mobileMenu" class="hidden md:hidden px-6 pt-2 pb-5 border-t border-slate-100 bg-white">
            <div class="flex flex-col gap-2.5 text-sm font-medium text-slate-700">
                <a href="{{ route('home') }}" class="py-2 px-3 rounded-lg hover:bg-emerald-50 {{ request()->routeIs('home') ? 'bg-emerald-50 text-emerald-700 font-bold' : '' }}">Beranda</a>
                <a href="{{ route('destinasi') }}" class="py-2 px-3 rounded-lg hover:bg-emerald-50 {{ request()->routeIs('destinasi') ? 'bg-emerald-50 text-emerald-700 font-bold' : '' }}">🏕️ Destinasi Favorit</a>
                <a href="{{ route('jadwal') }}" class="py-2 px-3 rounded-lg hover:bg-emerald-50 {{ request()->routeIs('jadwal') ? 'bg-emerald-50 text-emerald-700 font-bold' : '' }}">🗓️ Jadwal Perjalanan</a>
                <a href="{{ route('anggaran') }}" class="py-2 px-3 rounded-lg hover:bg-emerald-50 {{ request()->routeIs('anggaran') ? 'bg-emerald-50 text-emerald-700 font-bold' : '' }}">💰 Anggaran Perjalanan</a>
                <a href="{{ route('ai.index') }}" class="py-2 px-3 rounded-lg hover:bg-emerald-50 {{ request()->routeIs('ai.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : '' }}">✨ Asisten AI</a>
                <a href="{{ route('trips.index') }}" class="py-2 px-3 rounded-lg hover:bg-emerald-50 {{ request()->routeIs('trips.*') && !request()->routeIs('trips.create*') ? 'bg-emerald-50 text-emerald-700 font-bold' : '' }}">🧳 Perjalanan Saya</a>
                @auth
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.destinations.index') }}" class="py-2 px-3 rounded-lg hover:bg-purple-50 text-purple-700 font-bold">🛡️ Admin Panel</a>
                    @endif
                @endauth

                <div class="pt-3 mt-2 border-t border-slate-100 flex flex-col gap-2">
                    <a href="{{ route('trips.create') }}" class="block text-center bg-emerald-700 text-white py-2.5 rounded-xl font-semibold hover:bg-emerald-800">
                        + Rencana Baru
                    </a>

                    @guest
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <a href="{{ route('login') }}" class="text-center py-2 border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="text-center py-2 bg-emerald-50 text-emerald-800 rounded-xl font-bold hover:bg-emerald-100">
                                Daftar
                            </a>
                        </div>
                    @endguest

                    @auth
                        <div class="flex flex-col gap-1.5 pt-1">
                            <a href="{{ route('favorites.index') }}" class="py-2 px-3 rounded-lg hover:bg-pink-50 text-pink-700 font-bold flex items-center gap-2">
                                <span>❤️</span> Destinasi Favorit
                            </a>
                            <a href="{{ route('profile.edit') }}" class="py-2 px-3 rounded-lg hover:bg-emerald-50 text-emerald-800 font-bold flex items-center gap-2">
                                <span>👤</span> Profil & Pengaturan Akun
                            </a>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 mt-1">
                            <span class="text-xs font-bold text-slate-700 truncate">
                                👤 {{ Auth::user()->name }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-red-600 hover:underline">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @if (session('success'))
        <div class="max-w-7xl mx-auto w-full px-6 md:px-12 mt-4">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-5 py-4 rounded-2xl text-sm font-medium flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <span class="p-1 bg-emerald-200 text-emerald-800 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-lg">&times;</button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto w-full px-6 md:px-12 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-900 px-5 py-4 rounded-2xl text-sm font-medium flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <span class="p-1 bg-red-200 text-red-800 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </span>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 font-bold text-lg">&times;</button>
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 mt-16">
        <div class="max-w-7xl mx-auto px-6 md:px-12 py-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <a href="{{ route('home') }}" class="text-2xl font-extrabold text-emerald-700 tracking-tight flex items-center gap-1">
                        <span>🧭 Triply<span class="text-orange-500">.</span></span>
                    </a>
                    <p class="text-slate-500 text-sm mt-1">Platform Perencanaan Liburan & Perjalanan Indonesia.</p>
                </div>

                <div class="flex flex-wrap gap-6 text-sm font-medium text-slate-600">
                    <a href="{{ route('destinasi') }}" class="hover:text-emerald-700">Destinasi</a>
                    <a href="{{ route('jadwal') }}" class="hover:text-emerald-700">Jadwal Perjalanan</a>
                    <a href="{{ route('anggaran') }}" class="hover:text-emerald-700">Anggaran</a>
                    <a href="{{ route('trips.index') }}" class="hover:text-emerald-700">Perjalanan Saya</a>
                    @guest
                        <a href="{{ route('login') }}" class="hover:text-emerald-700">Masuk Akun</a>
                    @endguest
                </div>
            </div>
            <div class="border-t border-slate-100 mt-8 pt-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Triply. Plan your journey, your way. Dibuat untuk petualang Indonesia.
            </div>
        </div>
    </footer>

    <script>
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Global helper for form submit loading state
        function handleFormSubmit(form) {
            const btn = form.querySelector('.submit-btn');
            if (!btn) return;
            const spinner = btn.querySelector('.btn-spinner');
            const text = btn.querySelector('.btn-text');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            if (spinner) spinner.classList.remove('hidden');
            if (text && !text.dataset.originalText) {
                text.dataset.originalText = text.innerText;
                text.innerText = 'Menyimpan...';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
