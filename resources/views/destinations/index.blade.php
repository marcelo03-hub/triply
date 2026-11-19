@extends('layouts.app')

@section('title', 'Daftar Destinasi Wisata | Triply')

@section('content')
<div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full mb-3">
                <span>🌍</span> DAFTAR DESTINASI WISATA
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                Pilihan Destinasi Wisata
            </h1>
            <p class="text-slate-600 text-base md:text-lg mt-3 leading-relaxed">
                Jelajahi berbagai destinasi impian di <strong>Indonesia</strong> maupun <strong>Mancanegara</strong> lengkap dengan informasi, deskripsi, estimasi biaya, dan tips perjalanan.
            </p>
        </div>

        @if(Auth::check() && Auth::user()->isAdmin())
            <div>
                <a href="{{ route('admin.destinations.create') }}" 
                   class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold shadow-md transition">
                    <span>⚙️ + Tambah Destinasi (Admin)</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-5 md:p-6 rounded-3xl border border-slate-200/80 shadow-xs mb-10 space-y-4">
        <!-- Wilayah Tabs -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl w-full sm:w-auto overflow-x-auto">
                <a href="{{ route('destinasi', array_merge(request()->except(['wilayah', 'page']), ['wilayah' => 'all'])) }}" 
                   class="px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition whitespace-nowrap {{ (!request('wilayah') || request('wilayah') === 'all') ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    🌐 Semua Destinasi ({{ $stats['total'] }})
                </a>
                <a href="{{ route('destinasi', array_merge(request()->except(['wilayah', 'page']), ['wilayah' => 'domestik'])) }}" 
                   class="px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition whitespace-nowrap {{ request('wilayah') === 'domestik' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    🇮🇩 Domestik ({{ $stats['domestik'] }})
                </a>
                <a href="{{ route('destinasi', array_merge(request()->except(['wilayah', 'page']), ['wilayah' => 'internasional'])) }}" 
                   class="px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition whitespace-nowrap {{ request('wilayah') === 'internasional' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    ✈️ Luar Negeri ({{ $stats['internasional'] }})
                </a>
            </div>

            <!-- Search Input Form -->
            <form action="{{ route('destinasi') }}" method="GET" class="relative w-full sm:w-80">
                @if(request('wilayah'))
                    <input type="hidden" name="wilayah" value="{{ request('wilayah') }}">
                @endif
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari destinasi atau lokasi..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </form>
        </div>

        <!-- Category Filter Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Kategori:</span>
            <a href="{{ route('destinasi', array_merge(request()->except(['kategori', 'page']), ['kategori' => 'all'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ (!request('kategori') || request('kategori') === 'all') ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('destinasi', array_merge(request()->except(['kategori', 'page']), ['kategori' => 'pantai'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ request('kategori') === 'pantai' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                🏖️ Pantai & Bahari
            </a>
            <a href="{{ route('destinasi', array_merge(request()->except(['kategori', 'page']), ['kategori' => 'alam'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ request('kategori') === 'alam' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                ⛰️ Alam & Gunung
            </a>
            <a href="{{ route('destinasi', array_merge(request()->except(['kategori', 'page']), ['kategori' => 'budaya'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ request('kategori') === 'budaya' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                🏛️ Budaya & Sejarah
            </a>
            <a href="{{ route('destinasi', array_merge(request()->except(['kategori', 'page']), ['kategori' => 'kota'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ request('kategori') === 'kota' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                🏙️ Kota & Belanja
            </a>
            <a href="{{ route('destinasi', array_merge(request()->except(['kategori', 'page']), ['kategori' => 'religi'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ request('kategori') === 'religi' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                🕌 Wisata Religi
            </a>
        </div>
    </div>

    <!-- Destinations Grid -->
    @if($destinations->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-200">
            <p class="text-4xl mb-3">🔍</p>
            <h3 class="text-lg font-bold text-slate-800">Tidak Ada Destinasi yang Cocok</h3>
            <p class="text-slate-500 text-sm mt-1">Coba gunakan kata kunci pencarian lain atau reset filter kategori.</p>
            <a href="{{ route('destinasi') }}" class="inline-block mt-4 bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-emerald-800">
                Reset Semua Filter →
            </a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($destinations as $dest)
                <article class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Destination Image & Badges -->
                        <div class="relative h-56 overflow-hidden">
                            <img src="{{ $dest->image_url }}"
                                 alt="{{ $dest->nama }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            
                            <!-- Region Badge -->
                            <span class="absolute top-3 left-3 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs {{ $dest->wilayah === 'domestik' ? 'bg-emerald-800/90' : 'bg-blue-800/90' }}">
                                {{ $dest->wilayah === 'domestik' ? '🇮🇩 Domestik' : '✈️ Luar Negeri' }} • {{ ucfirst($dest->kategori) }}
                            </span>

                            <!-- Rating Badge -->
                            <span class="absolute top-3 right-3 bg-white/95 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-full shadow-xs">
                                ⭐ {{ $dest->rating }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-wider mb-1 {{ $dest->wilayah === 'domestik' ? 'text-emerald-700' : 'text-blue-700' }}">
                                📍 {{ $dest->lokasi }}
                            </p>
                            
                            <h2 class="text-2xl font-bold text-slate-900 mb-2 group-hover:text-emerald-700 transition">
                                <a href="{{ route('destinasi.show', $dest) }}">
                                    {{ $dest->nama }}
                                </a>
                            </h2>

                            <p class="text-slate-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                {{ $dest->deskripsi }}
                            </p>

                            <div class="flex items-center justify-between text-xs text-slate-500 py-3 border-y border-slate-100">
                                <span>💰 {{ $dest->estimasi_biaya ?? 'Biaya bervariasi' }}</span>
                                <span>⏱️ {{ $dest->rekomendasi_durasi ?? '3-5 Hari' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-6 pt-0 flex items-center gap-2">
                        <a href="{{ route('destinasi.show', $dest) }}"
                           class="flex-1 text-center py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-xs transition">
                            Detail Lengkap
                        </a>
                        <a href="{{ route('trips.create', ['destinasi' => $dest->nama]) }}"
                           class="flex-1 text-center py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-xs transition">
                            Pilih & Rencanakan →
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $destinations->links() }}
        </div>
    @endif
</div>
@endsection

