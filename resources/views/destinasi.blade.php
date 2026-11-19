@extends('layouts.app')

@section('title', 'Destinasi Favorit Domestik & Mancanegara | Triply')

@section('content')
<div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
    <!-- Header -->
    <div class="max-w-3xl mb-10">
        <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full mb-3">
            <span>🌍</span> FITUR DESTINASI DOMESTIK & MANCANEGARA
        </div>
        <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
            Mau Liburan ke Mana?
        </h1>
        <p class="text-slate-600 text-base md:text-lg mt-3 leading-relaxed">
            Temukan inspirasi destinasi terbaik di <strong>Indonesia</strong> maupun <strong>Mancanegara (Luar Negeri)</strong>. Klik <strong>"Pilih Destinasi"</strong> untuk langsung menyusun jadwal dan anggarannya!
        </p>
    </div>

    <!-- Search & Region/Category Filter Controls -->
    <div class="bg-white p-5 md:p-6 rounded-3xl border border-slate-200/80 shadow-xs mb-10 space-y-4">
        <!-- Scope Tabs: Semua vs Domestik vs Luar Negeri -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl w-full sm:w-auto">
                <button type="button" onclick="filterScope('all', this)" class="scope-btn active flex-1 sm:flex-none px-5 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-900 shadow-xs transition">
                    🌐 Semua Destinasi
                </button>
                <button type="button" onclick="filterScope('domestik', this)" class="scope-btn flex-1 sm:flex-none px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 transition">
                    🇮🇩 Domestik (Indonesia)
                </button>
                <button type="button" onclick="filterScope('internasional', this)" class="scope-btn flex-1 sm:flex-none px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 transition">
                    ✈️ Luar Negeri (Internasional)
                </button>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" id="destSearch" placeholder="Cari kota, negara, atau wisata..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <!-- Category Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Kategori:</span>
            <button onclick="filterCategory('all', this)" class="category-btn active px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-700 text-white transition">
                Semua
            </button>
            <button onclick="filterCategory('pantai', this)" class="category-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                🏖️ Pantai & Bahari
            </button>
            <button onclick="filterCategory('alam', this)" class="category-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                ⛰️ Alam & Gunung
            </button>
            <button onclick="filterCategory('budaya', this)" class="category-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                🏛️ Budaya & Sejarah
            </button>
            <button onclick="filterCategory('kota', this)" class="category-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                🏙️ Kota & Belanja
            </button>
        </div>
    </div>

    <!-- Destinations Grid -->
    <div id="destGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        
        <!-- =================== DOMESTIK (INDONESIA) =================== -->

        <!-- 1. Bali -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="domestik" data-category="pantai" data-name="bali pulau indonesia kuta seminyak ubud">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80"
                         alt="Pulau Bali" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-emerald-800/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        🇮🇩 Domestik • Pantai
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.9
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">PROVINSI BALI, INDONESIA</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Pulau Bali</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Keindahan pantai Kuta & Canggu, pesona sawah terasering Ubud, serta kehangatan seni dan budaya pura yang magis.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 3-6 Juta</span>
                        <span>⏱️ Rekomendasi: 4-6 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Bali']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Destinasi Bali →
                </a>
            </div>
        </article>

        <!-- 2. Yogyakarta -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="domestik" data-category="budaya" data-name="yogyakarta jogja malioboro indonesia prambanan kraton">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80"
                         alt="Yogyakarta" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-orange-700/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        🇮🇩 Domestik • Budaya
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.8
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">D.I. YOGYAKARTA, INDONESIA</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Yogyakarta</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Suasana syahdu Jalan Malioboro, kemegahan Keraton, gudeg legendaris, serta seniman jalanan yang ramah di setiap sudut.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 1.5 - 3.5 Juta</span>
                        <span>⏱️ Rekomendasi: 3-4 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Yogyakarta']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Destinasi Jogja →
                </a>
            </div>
        </article>

        <!-- 3. Candi Borobudur -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="domestik" data-category="budaya" data-name="borobudur candi indonesia magelang jawa tengah sejarah">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1584810359583-96fc3448beaa?auto=format&fit=crop&w=800&q=80"
                         alt="Candi Borobudur" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-orange-700/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        🇮🇩 Domestik • Budaya
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.9
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">JAWA TENGAH, INDONESIA</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Candi Borobudur</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Mahakarya arsitektur Buddha terbesar di dunia, dikelilingi pemandangan perbukitan Menoreh dan matahari terbit spektakuler.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 1 - 2.5 Juta</span>
                        <span>⏱️ Rekomendasi: 2-3 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Borobudur']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Candi Borobudur →
                </a>
            </div>
        </article>

        <!-- 4. Lombok -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="domestik" data-category="pantai" data-name="lombok indonesia pantai gili trawangan rinjani mandalika ntb">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="{{ asset('images/lombok.jpg') }}"
                         alt="Pulau Lombok" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <span class="absolute top-3 left-3 bg-emerald-800/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        🇮🇩 Domestik • Pantai
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.8
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">NUSA TENGGARA BARAT, INDONESIA</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Pulau Lombok</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Eksotisnya trio Gili (Trawangan, Meno, Air), sirkuit kelas dunia Mandalika, dan petualangan mendaki Gunung Rinjani.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 3.5 - 7 Juta</span>
                        <span>⏱️ Rekomendasi: 4-5 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Lombok']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Destinasi Lombok →
                </a>
            </div>
        </article>

        <!-- 5. Bandung -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="domestik" data-category="kota" data-name="bandung indonesia dago ciwidey lembang jawa barat belanja kafe">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80"
                         alt="Bandung" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-purple-700/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        🇮🇩 Domestik • Kota
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.7
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-purple-700 uppercase tracking-wider mb-1">JAWA BARAT, INDONESIA</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Kota Bandung</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Wisata glamping Lembang, kawah belerang Ciwidey, kuliner khas, dan udara sejuk pegunungan yang menenangkan.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 1 - 2.5 Juta</span>
                        <span>⏱️ Rekomendasi: 2-3 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Bandung']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Destinasi Bandung →
                </a>
            </div>
        </article>

        <!-- 6. Labuan Bajo -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="domestik" data-category="alam" data-name="labuan bajo indonesia komodo padar pink beach ntt flores">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="{{ asset('images/labuan-bajo.jpg') }}"
                         alt="Labuan Bajo" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <span class="absolute top-3 left-3 bg-teal-700/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        🇮🇩 Domestik • Bahari
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 5.0
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-teal-700 uppercase tracking-wider mb-1">FLORES, NTT, INDONESIA</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Labuan Bajo</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Melihat satwa purba Komodo di habitat aslinya, mendaki puncak Pulau Padar, dan sailing trip phinisi mewah.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 5 - 12 Juta</span>
                        <span>⏱️ Rekomendasi: 3-5 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Labuan Bajo']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Labuan Bajo →
                </a>
            </div>
        </article>


        <!-- =================== LUAR NEGERI (INTERNASIONAL) =================== -->

        <!-- 7. Tokyo & Kyoto, Jepang -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="internasional" data-category="kota" data-name="jepang tokyo kyoto fuji sakura luar negeri shibuya akihabara">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&w=800&q=80"
                         alt="Tokyo Jepang" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-red-700/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        ✈️ Luar Negeri • Jepang
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 5.0 (Favorit)
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">ASIA TIMUR • JEPANG</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Tokyo & Kyoto, Jepang</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Pemandangan sakura mekar, kemegahan Gunung Fuji, distrik Shibuya & Shinjuku yang modern, serta kuil tradisional Kyoto.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 15 - 25 Juta</span>
                        <span>⏱️ Rekomendasi: 5-7 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Tokyo, Jepang']) }}"
                   class="block text-center w-full bg-red-700 text-white rounded-xl py-3 font-semibold hover:bg-red-800 shadow-xs transition">
                    Pilih Tokyo, Jepang →
                </a>
            </div>
        </article>

        <!-- 8. Seoul, Korea Selatan -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="internasional" data-category="kota" data-name="korea selatan seoul nami island k-pop myeongdong luar negeri">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1538485399081-7191377e8241?auto=format&fit=crop&w=800&q=80"
                         alt="Seoul Korea" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-blue-700/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        ✈️ Luar Negeri • Korea
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.9
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">ASIA TIMUR • KOREA SELATAN</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Seoul & Pulau Nami</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Foto mengenakan Hanbok di Istana Gyeongbokgung, hunting street food Myeongdong, dan wisata romantis di Pulau Nami.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 12 - 20 Juta</span>
                        <span>⏱️ Rekomendasi: 5-6 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Seoul, Korea Selatan']) }}"
                   class="block text-center w-full bg-blue-700 text-white rounded-xl py-3 font-semibold hover:bg-blue-800 shadow-xs transition">
                    Pilih Seoul, Korea →
                </a>
            </div>
        </article>

        <!-- 9. Singapura -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="internasional" data-category="kota" data-name="singapura singapore marina bay sentosa uss universal orchard luar negeri">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=800&q=80"
                         alt="Singapura" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-red-600/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        ✈️ Bebas Visa • Singapura
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.8
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ASIA TENGGARA • SINGAPURA</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Marina Bay & Sentosa</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Pertunjukan air mancur Marina Bay Sands, rimbunnya Gardens by the Bay, wahana Universal Studios, dan wisata belanja Orchard.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 5 - 9 Juta</span>
                        <span>⏱️ Rekomendasi: 3-4 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Singapura']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Singapura →
                </a>
            </div>
        </article>

        <!-- 10. Bangkok & Phuket, Thailand -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="internasional" data-category="pantai" data-name="thailand bangkok phuket wat arun chatuchak phi phi luar negeri">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=800&q=80"
                         alt="Bangkok Thailand" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-amber-700/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        ✈️ Bebas Visa • Thailand
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.8
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">ASIA TENGGARA • THAILAND</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Bangkok & Phuket</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Kuil megah Wat Arun di tepi sungai Chao Phraya, belanja murah pasar Chatuchak, serta keindahan pulau Phi Phi di Phuket.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 5 - 10 Juta</span>
                        <span>⏱️ Rekomendasi: 4-5 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Bangkok, Thailand']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Bangkok, Thailand →
                </a>
            </div>
        </article>

        <!-- 11. Kuala Lumpur, Malaysia -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="internasional" data-category="kota" data-name="malaysia kuala lumpur klcc petronas batu caves penang luar negeri">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=800&q=80"
                         alt="Kuala Lumpur Malaysia" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-indigo-700/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        ✈️ Bebas Visa • Malaysia
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 4.7
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-indigo-700 uppercase tracking-wider mb-1">ASIA TENGGARA • MALAYSIA</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Kuala Lumpur & Penang</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Spot foto Menara Kembar Petronas, tangga warna-warni Batu Caves, kuliner nasi lemak, dan warisan sejarah UNESCO George Town.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 3.5 - 7 Juta</span>
                        <span>⏱️ Rekomendasi: 3-4 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Kuala Lumpur, Malaysia']) }}"
                   class="block text-center w-full bg-emerald-700 text-white rounded-xl py-3 font-semibold hover:bg-emerald-800 shadow-xs transition">
                    Pilih Malaysia →
                </a>
            </div>
        </article>

        <!-- 12. Cappadocia & Istanbul, Turki -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="internasional" data-category="budaya" data-name="turki turkey cappadocia istanbul hagia sophia balon udara luar negeri">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?auto=format&fit=crop&w=800&q=80"
                         alt="Cappadocia Turki" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-teal-800/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        ✈️ Luar Negeri • Turki
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 5.0
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">EROPA & ASIA • TURKI</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Istanbul & Cappadocia</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Sensasi menaiki balon udara di atas lembah batu Cappadocia, kemegahan Hagia Sophia, dan pelayaran di Selat Bosphorus.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 16 - 28 Juta</span>
                        <span>⏱️ Rekomendasi: 7-9 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Istanbul & Cappadocia, Turki']) }}"
                   class="block text-center w-full bg-teal-800 text-white rounded-xl py-3 font-semibold hover:bg-teal-900 shadow-xs transition">
                    Pilih Turki →
                </a>
            </div>
        </article>

        <!-- 13. Swiss / Alpen -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="internasional" data-category="alam" data-name="swiss switzerland zermatt zurich matterhorn alpen salju eropa luar negeri">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1530122037265-a5f1f91d3b99?auto=format&fit=crop&w=800&q=80"
                         alt="Swiss Alpen" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-slate-900/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        ✈️ Luar Negeri • Swiss
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 5.0
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">EROPA TENGAH • SWISS</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Zermatt & Pegunungan Alpen</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Pemandangan ikonik puncak Matterhorn, udara segar pegunungan Alpen bersalju, serta desa-desa dongeng tanpa polusi kendaraan.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 30 - 55 Juta</span>
                        <span>⏱️ Rekomendasi: 7-10 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Zermatt, Swiss']) }}"
                   class="block text-center w-full bg-slate-900 text-white rounded-xl py-3 font-semibold hover:bg-slate-800 shadow-xs transition">
                    Pilih Swiss →
                </a>
            </div>
        </article>

        <!-- 14. Mekkah & Madinah, Arab Saudi -->
        <article class="dest-card bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                 data-scope="internasional" data-category="budaya" data-name="arab saudi mekkah madinah umrah haram nabawi luar negeri">
            <div>
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fit=crop&w=800&q=80"
                         alt="Makkah Arab Saudi" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-emerald-900/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs">
                        ✈️ Wisata Religi • Arab Saudi
                    </span>
                    <span class="absolute top-3 right-3 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                        ⭐ 5.0
                    </span>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-1">TIMUR TENGAH • ARAB SAUDI</p>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Mekkah & Madinah (Umrah)</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        Perjalanan ibadah spiritual ke Ka'bah di Masjidil Haram dan ziarah Makam Rasulullah di Masjid Nabawi yang menenangkan kalbu.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-slate-500 py-2 border-y border-slate-100">
                        <span>💰 Est: Rp 28 - 45 Juta</span>
                        <span>⏱️ Rekomendasi: 9-12 Hari</span>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <a href="{{ route('trips.create', ['destinasi' => 'Mekkah & Madinah, Arab Saudi']) }}"
                   class="block text-center w-full bg-emerald-800 text-white rounded-xl py-3 font-semibold hover:bg-emerald-900 shadow-xs transition">
                    Pilih Mekkah & Madinah →
                </a>
            </div>
        </article>

    </div>

    <!-- Empty State if search finds nothing -->
    <div id="noResults" class="hidden text-center py-16 bg-white rounded-3xl border border-dashed border-slate-200 mt-6">
        <p class="text-4xl mb-3">🔍</p>
        <h3 class="text-lg font-bold text-slate-800">Destinasi Tidak Ditemukan</h3>
        <p class="text-slate-500 text-sm mt-1">Coba kata kunci lain atau masukkan destinasi kustom pilihanmu sendiri.</p>
        <a href="{{ route('trips.create') }}" class="inline-block mt-4 bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-emerald-800">
            Tulis Destinasi Sendiri →
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeScope = 'all';
    let activeCategory = 'all';

    function filterScope(scope, button) {
        activeScope = scope;
        document.querySelectorAll('.scope-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
            btn.classList.add('text-slate-600');
        });
        button.classList.remove('text-slate-600');
        button.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
        applyFilters();
    }

    function filterCategory(category, button) {
        activeCategory = category;
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.classList.remove('bg-emerald-700', 'text-white', 'font-bold');
            btn.classList.add('bg-slate-100', 'text-slate-700', 'font-semibold');
        });
        button.classList.remove('bg-slate-100', 'text-slate-700', 'font-semibold');
        button.classList.add('bg-emerald-700', 'text-white', 'font-bold');
        applyFilters();
    }

    const searchInput = document.getElementById('destSearch');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    function applyFilters() {
        const query = (document.getElementById('destSearch').value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.dest-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardScope = card.getAttribute('data-scope');
            const cardCat = card.getAttribute('data-category');
            const cardName = card.getAttribute('data-name').toLowerCase();

            const matchScope = (activeScope === 'all' || cardScope === activeScope);
            const matchCat = (activeCategory === 'all' || cardCat === activeCategory);
            const matchSearch = (!query || cardName.includes(query));

            if (matchScope && matchCat && matchSearch) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const noResults = document.getElementById('noResults');
        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    }
</script>
@endpush
