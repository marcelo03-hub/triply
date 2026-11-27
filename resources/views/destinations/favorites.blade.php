@extends('layouts.app')

@section('title', 'Destinasi Favorit Saya | Triply')

@section('content')
<div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="inline-flex items-center gap-2 bg-pink-100 text-pink-800 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full mb-3">
                <span>❤️</span> DAFTAR FAVORIT SAYA
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                Destinasi Tersimpan
            </h1>
            <p class="text-slate-600 text-base md:text-lg mt-3 leading-relaxed">
                Koleksi tempat wisata impian yang telah kamu simpan untuk rencana liburan berikutnya.
            </p>
        </div>

        <div>
            <a href="{{ route('destinasi') }}" 
               class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold shadow-xs transition">
                <span>🔍 Jelajahi Destinasi Lainnya</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <span class="text-lg">✅</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Favorites Grid or Empty State -->
    @if($favorites->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200 p-8">
            <div class="text-6xl mb-4">🤍</div>
            <h3 class="text-xl font-bold text-slate-800">Belum Ada Destinasi Favorit</h3>
            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">
                Kamu belum menyimpan destinasi manapun. Tekan ikon hati pada halaman katalog destinasi untuk menyimpannya di sini.
            </p>
            <a href="{{ route('destinasi') }}" class="inline-block mt-6 bg-emerald-700 text-white px-6 py-3 rounded-2xl text-sm font-bold hover:bg-emerald-800 shadow-xs transition">
                Mulai Eksplorasi Destinasi →
            </a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($favorites as $fav)
                @php $dest = $fav->destination; @endphp
                @if($dest)
                    <article class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <!-- Destination Image & Favorite Action -->
                            <div class="relative h-56 overflow-hidden">
                                <img src="{{ $dest->image_url }}"
                                     alt="{{ $dest->nama }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                
                                <!-- Region Badge -->
                                <span class="absolute top-3 left-3 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-xs {{ $dest->wilayah === 'domestik' ? 'bg-emerald-800/90' : 'bg-blue-800/90' }}">
                                    {{ $dest->wilayah === 'domestik' ? '🇮🇩 Domestik' : '✈️ Luar Negeri' }} • {{ ucfirst($dest->kategori) }}
                                </span>

                                <!-- Remove Favorite Button -->
                                <form action="{{ route('destinations.favorite.toggle', $dest) }}" method="POST" class="absolute top-3 right-3 z-10">
                                    @csrf
                                    <button type="submit" 
                                            class="w-9 h-9 rounded-full bg-white/95 text-red-500 shadow-md flex items-center justify-center hover:scale-110 transition"
                                            title="Hapus dari favorit">
                                        <svg class="w-5 h-5 fill-current text-red-500" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </button>
                                </form>
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

                                <p class="text-slate-600 text-sm leading-relaxed mb-4 line-clamp-2">
                                    {{ $dest->deskripsi }}
                                </p>

                                <div class="flex items-center justify-between text-xs text-slate-500 py-3 border-y border-slate-100">
                                    <span>💰 {{ $dest->estimasi_biaya ?? 'Biaya bervariasi' }}</span>
                                    <span>⏱️ {{ $dest->rekomendasi_durasi ?? '3-5 Hari' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="p-6 pt-0 flex items-center gap-3">
                            <a href="{{ route('destinasi.show', $dest) }}" 
                               class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold py-2.5 rounded-xl transition">
                                Detail Tempat
                            </a>
                            <a href="{{ route('trips.create', ['destinasi' => $dest->nama]) }}" 
                               class="flex-1 text-center bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold py-2.5 rounded-xl shadow-2xs transition">
                                + Buat Trip
                            </a>
                        </div>
                    </article>
                @endif
            @endforeach
        </div>

        <div class="mt-10">
            {{ $favorites->links() }}
        </div>
    @endif
</div>
@endsection
