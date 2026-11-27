@extends('layouts.app')

@section('title', $destination->nama . ' — Detail Destinasi | Triply')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        padding: 4px;
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-6 md:px-12 py-12">
    <!-- Breadcrumb & Top Actions -->
    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('destinasi') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:underline">
            <span>←</span> Kembali ke Semua Destinasi
        </a>

        <div class="flex items-center gap-2">
            @auth
                @php $isFav = $destination->isFavoritedBy(Auth::user()); @endphp
                <form action="{{ route('destinations.favorite.toggle', $destination) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs {{ $isFav ? 'bg-pink-100 text-pink-700 border border-pink-200' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                        <span>{{ $isFav ? '❤️ Tersimpan di Favorit' : '🤍 Tambah ke Favorit' }}</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                    <span>🤍 Tambah ke Favorit</span>
                </a>
            @endauth

            @if(Auth::check() && Auth::user()->isAdmin())
                <a href="{{ route('admin.destinations.edit', $destination) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                    <span>⚙️ Edit Destinasi (Admin)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden mb-12">
        <!-- Hero Image -->
        <div class="relative h-[360px] md:h-[480px] w-full overflow-hidden">
            <img src="{{ $destination->image_url }}" 
                 alt="{{ $destination->nama }}"
                 class="w-full h-full object-cover">
            
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>

            <!-- Floating Badges -->
            <div class="absolute top-6 left-6 flex flex-wrap gap-2">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold text-white backdrop-blur-md {{ $destination->wilayah === 'domestik' ? 'bg-emerald-800/90' : 'bg-blue-800/90' }}">
                    {{ $destination->wilayah === 'domestik' ? '🇮🇩 Wisata Domestik' : '✈️ Wisata Mancanegara' }}
                </span>
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold text-white bg-slate-900/80 backdrop-blur-md capitalize">
                    Kategori: {{ $destination->kategori }}
                </span>
                @if($destination->is_popular)
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-bold text-slate-950 bg-amber-400 shadow-xs">
                        🔥 Destinasi Populer
                    </span>
                @endif
            </div>

            <!-- Rating & Title on Image -->
            <div class="absolute bottom-6 left-6 right-6 text-white">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-white border border-white/20">
                        ⭐ {{ $destination->rating }} / 5.0 Rating Wisatawan
                    </span>
                    <span class="text-sm font-semibold text-emerald-300">
                        📍 {{ $destination->lokasi }}
                    </span>
                </div>
                <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight">
                    {{ $destination->nama }}
                </h1>
            </div>
        </div>

        <!-- Detail Content Section -->
        <div class="p-8 md:p-12">
            <!-- Key Facts Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 mb-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Estimasi Biaya</span>
                    <span class="text-base md:text-lg font-extrabold text-emerald-700 mt-0.5 block">
                        {{ $destination->estimasi_biaya ?? 'Mulai Rp 1.500.000' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Rekomendasi Waktu</span>
                    <span class="text-base md:text-lg font-extrabold text-slate-800 mt-0.5 block">
                        {{ $destination->rekomendasi_durasi ?? '3 - 5 Hari' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Lokasi & Wilayah</span>
                    <span class="text-base md:text-lg font-extrabold text-slate-800 mt-0.5 block truncate">
                        {{ $destination->lokasi }}
                    </span>
                </div>
            </div>

            <div class="grid lg:grid-cols-12 gap-10">
                <!-- Main Info (8 cols) -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Deskripsi Lengkap -->
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span>📖</span> Tentang {{ $destination->nama }}
                        </h2>
                        <div class="text-slate-600 leading-relaxed space-y-4 text-base">
                            <p>{{ $destination->deskripsi }}</p>
                        </div>
                    </div>

                    <!-- Highlight Tempat Menarik -->
                    @if($destination->highlight)
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <span>✨</span> Tempat & Aktivitas Wajib (Highlights)
                            </h2>
                            <div class="flex flex-wrap gap-2.5">
                                @foreach(explode(',', $destination->highlight) as $item)
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-50 text-emerald-800 border border-emerald-100">
                                        <span>📍</span> {{ trim($item) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tips Perjalanan -->
                    @if($destination->tips)
                        <div class="bg-amber-50/80 border border-amber-200/80 rounded-2xl p-6">
                            <h3 class="text-base font-bold text-amber-900 mb-2 flex items-center gap-2">
                                <span>💡</span> Tips Berwisata:
                            </h3>
                            <p class="text-sm text-amber-800 leading-relaxed">
                                {{ $destination->tips }}
                            </p>
                        </div>
                    @endif

                    <!-- Peta Interaktif Leaflet OpenStreetMap -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                    <span>🗺️</span> Peta Lokasi & Titik Geografis
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Eksplorasi titik lokasi {{ $destination->nama }} secara interaktif via OpenStreetMap.</p>
                            </div>
                            <a href="https://www.google.com/maps?q={{ $destination->display_latitude }},{{ $destination->display_longitude }}" 
                               target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-xl transition">
                                <span>Buka Google Maps ↗</span>
                            </a>
                        </div>

                        <!-- Map Container -->
                        <div id="destinationMap" class="w-full h-80 sm:h-96 rounded-2xl border border-slate-200 overflow-hidden relative z-0"></div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500 pt-3 border-t border-slate-100">
                            <span class="flex items-center gap-1">
                                <span>📍</span> Koordinat: <strong class="text-slate-800">{{ number_format($destination->display_latitude, 5) }}, {{ number_format($destination->display_longitude, 5) }}</strong>
                            </span>
                            <span class="text-slate-400">Peta ditenagai oleh OpenStreetMap & Leaflet</span>
                        </div>
                    </div>
                </div>

                <!-- Call to Action Side Box (4 cols) -->
                <div class="lg:col-span-4">
                    <div class="bg-gradient-to-br from-emerald-900 to-slate-900 text-white p-7 rounded-3xl shadow-lg sticky top-28 space-y-6">
                        <div>
                            <span class="text-xs font-bold text-emerald-300 uppercase tracking-widest block mb-1">
                                RENCANAKAN SEKARANG
                            </span>
                            <h3 class="text-2xl font-bold text-white">
                                Mau Liburan ke {{ $destination->nama }}?
                            </h3>
                            <p class="text-emerald-100/80 text-xs mt-2 leading-relaxed">
                                Buat rencana perjalanan barumu sekarang. Catat tanggal, buat jadwal aktivitas harian, dan pantau anggarannya dengan Triply.
                            </p>
                        </div>

                        <div class="space-y-3 pt-2">
                            <a href="{{ route('trips.create', ['destinasi' => $destination->nama]) }}"
                               class="block text-center w-full bg-orange-400 hover:bg-orange-500 text-slate-950 font-bold py-3.5 rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                                + Buat Rencana Perjalanan →
                            </a>
                            <a href="{{ route('anggaran') }}" 
                               class="block text-center w-full bg-white/10 hover:bg-white/20 text-white font-semibold py-2.5 rounded-xl text-xs transition border border-white/20">
                                Cek Kalkulator Anggaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Destinations -->
    @if($relatedDestinations->isNotEmpty())
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🧭</span> Destinasi Menarik Lainnya
            </h2>
            <div class="grid sm:grid-cols-3 gap-6">
                @foreach($relatedDestinations as $related)
                    <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                        <div>
                            <div class="relative h-44 overflow-hidden">
                                <img src="{{ $related->image_url }}" alt="{{ $related->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <span class="absolute top-2.5 left-2.5 bg-slate-900/80 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">
                                    {{ $related->wilayah === 'domestik' ? '🇮🇩 Domestik' : '✈️ Luar Negeri' }}
                                </span>
                            </div>
                            <div class="p-5">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">📍 {{ $related->lokasi }}</p>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition mb-2">
                                    {{ $related->nama }}
                                </h3>
                                <p class="text-slate-500 text-xs line-clamp-2">{{ $related->deskripsi }}</p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <a href="{{ route('destinasi.show', $related) }}" class="block text-center w-full py-2 bg-slate-50 hover:bg-emerald-50 text-emerald-700 font-bold rounded-xl text-xs transition">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $destination->display_latitude }};
        const lng = {{ $destination->display_longitude }};
        const title = @json($destination->nama);
        const locationText = @json($destination->lokasi);

        const map = L.map('destinationMap', {
            scrollWheelZoom: false
        }).setView([lat, lng], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Custom marker icon using SVG
        const customIcon = L.divIcon({
            className: 'custom-map-pin',
            html: `
                <div style="background-color: #047857; color: white; border-radius: 9999px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2); border: 2px solid white; font-size: 16px;">
                    📍
                </div>
            `,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -36]
        });

        const popupContent = `
            <div style="padding: 6px; font-family: inherit;">
                <h4 style="font-weight: 800; font-size: 14px; color: #0f172a; margin-bottom: 2px;">${title}</h4>
                <p style="font-size: 12px; color: #64748b; margin-bottom: 6px;">📍 ${locationText}</p>
                <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" rel="noopener noreferrer" style="display: inline-block; font-size: 11px; font-weight: 700; color: #047857; text-decoration: underline;">
                    Buka Rute di Google Maps →
                </a>
            </div>
        `;

        L.marker([lat, lng], { icon: customIcon })
            .addTo(map)
            .bindPopup(popupContent)
            .openPopup();
    });
</script>
@endpush

