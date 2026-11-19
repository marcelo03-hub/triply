@extends('layouts.app')

@section('title', 'Triply — Plan Your Journey Domestik & Mancanegara')

@push('styles')
<style>
    .hero-bg {
        background: linear-gradient(90deg, rgba(15, 35, 30, .88), rgba(15, 35, 30, .45)),
        url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1800&q=85');
        background-size: cover;
        background-position: center;
    }
</style>
@endpush

@section('content')
    <!-- HERO SECTION -->
    <section class="hero-bg min-h-[540px] md:min-h-[620px] flex items-center px-6 md:px-12 py-20 relative">
        <div class="max-w-3xl text-white">
            <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md border border-white/30 rounded-full px-4 py-1.5 text-xs md:text-sm font-medium mb-6">
                <span>🌍</span> Liburan Domestik & Mancanegara
            </span>

            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight tracking-tight mb-6">
                Perjalanan Seru,<br>
                Rencana Lebih <span class="text-orange-400">Mudah.</span>
            </h1>

            <p class="text-white/85 text-base md:text-xl leading-relaxed max-w-xl mb-9">
                Susun destinasi impian di <strong>Indonesia</strong> maupun <strong>Luar Negeri (Jepang, Korea, Singapura, Swiss, dll)</strong>, atur jadwal perjalanan yang teratur, dan hitung anggaran liburanmu.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('trips.create') }}"
                   class="bg-orange-400 hover:bg-orange-500 text-slate-950 font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-orange-400/30 transition transform hover:-translate-y-0.5 active:translate-y-0 text-base">
                    Rencanakan Perjalanan →
                </a>
                <a href="{{ route('destinasi') }}"
                   class="bg-white/15 hover:bg-white/25 text-white font-semibold px-6 py-4 rounded-xl backdrop-blur-sm border border-white/30 transition">
                    Jelajahi Semua Destinasi
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION FITUR UTAMA TRIPLY (CLICKABLE FEATURES) -->
    <section id="fitur" class="px-6 md:px-12 py-20 max-w-7xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="inline-block bg-emerald-100 text-emerald-800 text-xs uppercase font-extrabold tracking-wider px-3.5 py-1.5 rounded-full mb-3">
                FITUR UTAMA TRIPLY
            </span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                Semua Rencana, Satu Tempat
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                Klik fitur di bawah untuk langsung mencoba dan mengelola perjalanan liburanmu.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- FITUR 1: DESTINASI FAVORIT -->
            <a href="{{ route('destinasi') }}" 
               class="group relative bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-emerald-500/50 transition-all duration-300 flex flex-col justify-between transform hover:-translate-y-1">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        🏕️
                    </div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Domestik & Luar Negeri</span>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1 mb-3 group-hover:text-emerald-700 transition">
                        Destinasi Favorit
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                        Pilihan tempat liburan terlengkap: keindahan alam Indonesia hingga kota modern luar negeri (Jepang, Korea, Singapura, Swiss, dll).
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-emerald-700 font-bold text-sm">
                    <span>Jelajahi Destinasi</span>
                    <span class="transform group-hover:translate-x-2 transition-transform">→</span>
                </div>
            </a>

            <!-- FITUR 2: JADWAL PERJALANAN -->
            <a href="{{ route('jadwal') }}" 
               class="group relative bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-emerald-500/50 transition-all duration-300 flex flex-col justify-between transform hover:-translate-y-1">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        🗓️
                    </div>
                    <span class="text-xs font-bold text-orange-600 uppercase tracking-wider">Manajemen Waktu</span>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1 mb-3 group-hover:text-orange-600 transition">
                        Jadwal Perjalanan
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                        Susun jadwal hari demi hari (itinerary) agar liburanmu tertata rapi, dari waktu keberangkatan pesawat hingga kepulangan.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-orange-600 font-bold text-sm">
                    <span>Atur Jadwal & Itinerary</span>
                    <span class="transform group-hover:translate-x-2 transition-transform">→</span>
                </div>
            </a>

            <!-- FITUR 3: ANGGARAN PERJALANAN -->
            <a href="{{ route('anggaran') }}" 
               class="group relative bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-emerald-500/50 transition-all duration-300 flex flex-col justify-between transform hover:-translate-y-1">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                        💰
                    </div>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Kalkulator Pintar</span>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1 mb-3 group-hover:text-blue-600 transition">
                        Anggaran Perjalanan
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                        Hitung estimasi pengeluaran per kategori (tiket pesawat, hotel, visa, kuliner) untuk liburan lokal maupun luar negeri.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-blue-600 font-bold text-sm">
                    <span>Hitung Anggaran</span>
                    <span class="transform group-hover:translate-x-2 transition-transform">→</span>
                </div>
            </a>
        </div>
    </section>

    <!-- SECTION DESTINASI REKOMENDASI -->
    <section class="py-20 px-6 md:px-12 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div>
                    <span class="text-emerald-700 font-bold text-sm uppercase tracking-wider">
                        PILIH PETUALANGANMU
                    </span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-1">
                        Destinasi Populer Favorit
                    </h2>
                    <p class="text-slate-500 mt-2 max-w-xl">
                        Temukan tempat menarik baik di dalam negeri maupun mancanegara dan buat rencana dengan 1-klik.
                    </p>
                </div>
                <a href="{{ route('destinasi') }}"
                   class="mt-4 md:mt-0 inline-flex items-center gap-1 font-bold text-emerald-700 hover:text-emerald-800">
                    Lihat Semua Destinasi <span>→</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- 1. Bali (Domestik) -->
                <div class="group bg-[#F8FAF9] rounded-3xl overflow-hidden border border-slate-100 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative overflow-hidden h-52">
                            <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80"
                                 alt="Pulau Bali" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-3 left-3 bg-emerald-800 text-white px-2.5 py-0.5 rounded-full text-xs font-bold shadow-xs">
                                🇮🇩 Bali
                            </span>
                        </div>
                        <div class="p-5">
                            <p class="text-xs font-bold text-emerald-700 tracking-wider mb-1">INDONESIA</p>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Pulau Bali</h3>
                            <p class="text-slate-600 text-xs mb-4 line-clamp-2">
                                Pantai eksotis Kuta, sunset memukau, dan sawah bertingkat Ubud.
                            </p>
                        </div>
                    </div>
                    <div class="p-5 pt-0">
                        <a href="{{ route('trips.create', ['destinasi' => 'Bali']) }}"
                           class="flex items-center justify-center gap-1.5 w-full bg-emerald-700 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-emerald-800 transition">
                            <span>Rencanakan</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- 2. Lombok (Domestik) -->
                <div class="group bg-[#F8FAF9] rounded-3xl overflow-hidden border border-slate-100 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative overflow-hidden h-52">
                            <img src="{{ asset('images/lombok.jpg') }}"
                                 alt="Pulau Lombok" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-3 left-3 bg-emerald-800 text-white px-2.5 py-0.5 rounded-full text-xs font-bold shadow-xs">
                                🇮🇩 Lombok
                            </span>
                        </div>
                        <div class="p-5">
                            <p class="text-xs font-bold text-emerald-700 tracking-wider mb-1">NUSA TENGGARA BARAT</p>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Pulau Lombok</h3>
                            <p class="text-slate-600 text-xs mb-4 line-clamp-2">
                                Pantai Tanjung Aan, trio Gili (Trawangan, Meno, Air), dan Gunung Rinjani.
                            </p>
                        </div>
                    </div>
                    <div class="p-5 pt-0">
                        <a href="{{ route('trips.create', ['destinasi' => 'Lombok']) }}"
                           class="flex items-center justify-center gap-1.5 w-full bg-emerald-700 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-emerald-800 transition">
                            <span>Rencanakan</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- 3. Tokyo, Jepang (Luar Negeri) -->
                <div class="group bg-[#F8FAF9] rounded-3xl overflow-hidden border border-slate-100 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative overflow-hidden h-52">
                            <img src="https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&w=800&q=80"
                                 alt="Tokyo Jepang" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-3 left-3 bg-red-700 text-white px-2.5 py-0.5 rounded-full text-xs font-bold shadow-xs">
                                ✈️ Jepang
                            </span>
                        </div>
                        <div class="p-5">
                            <p class="text-xs font-bold text-red-600 tracking-wider mb-1">ASIA TIMUR</p>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Tokyo & Fuji</h3>
                            <p class="text-slate-600 text-xs mb-4 line-clamp-2">
                                Bunga sakura, gemerlap Shibuya, Gunung Fuji, dan surga ramen.
                            </p>
                        </div>
                    </div>
                    <div class="p-5 pt-0">
                        <a href="{{ route('trips.create', ['destinasi' => 'Tokyo, Jepang']) }}"
                           class="flex items-center justify-center gap-1.5 w-full bg-red-700 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-red-800 transition">
                            <span>Rencanakan</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- 4. Singapura (Luar Negeri Bebas Visa) -->
                <div class="group bg-[#F8FAF9] rounded-3xl overflow-hidden border border-slate-100 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative overflow-hidden h-52">
                            <img src="https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=800&q=80"
                                 alt="Singapura" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-3 left-3 bg-blue-700 text-white px-2.5 py-0.5 rounded-full text-xs font-bold shadow-xs">
                                ✈️ Singapura
                            </span>
                        </div>
                        <div class="p-5">
                            <p class="text-xs font-bold text-blue-700 tracking-wider mb-1">ASIA TENGGARA</p>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Singapura</h3>
                            <p class="text-slate-600 text-xs mb-4 line-clamp-2">
                                Marina Bay Sands, wahana Universal Studios, dan belanja Orchard.
                            </p>
                        </div>
                    </div>
                    <div class="p-5 pt-0">
                        <a href="{{ route('trips.create', ['destinasi' => 'Singapura']) }}"
                           class="flex items-center justify-center gap-1.5 w-full bg-blue-700 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-blue-800 transition">
                            <span>Rencanakan</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION CTA FOOTER -->
    <section class="max-w-7xl mx-auto px-6 md:px-12 py-16">
        <div class="bg-gradient-to-r from-emerald-900 to-teal-900 text-white rounded-3xl p-10 md:p-16 text-center shadow-xl relative overflow-hidden">
            <div class="relative z-10 max-w-2xl mx-auto">
                <span class="text-orange-400 text-sm font-extrabold uppercase tracking-widest block mb-2">Jelajahi Dunia</span>
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4 leading-tight">
                    Petualanganmu Dimulai Dari Sini
                </h2>
                <p class="text-emerald-100/90 mb-8 text-base md:text-lg">
                    Rencanakan liburanmu sekarang — dari keindahan nusantara hingga petualangan keliling dunia.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('trips.create') }}"
                       class="inline-block bg-orange-400 text-slate-950 font-bold px-8 py-4 rounded-xl hover:bg-orange-500 shadow-md transition transform hover:-translate-y-0.5">
                        Buat Rencana Perjalanan Baru →
                    </a>
                    <a href="{{ route('destinasi') }}"
                       class="inline-block bg-white/10 hover:bg-white/20 text-white font-semibold px-6 py-4 rounded-xl backdrop-blur-sm border border-white/20 transition">
                        Pilih Destinasi Favorit
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
