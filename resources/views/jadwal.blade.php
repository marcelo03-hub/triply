@extends('layouts.app')

@section('title', 'Jadwal Perjalanan & Itinerary | Triply')

@section('content')
<div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="inline-flex items-center gap-2 bg-orange-100 text-orange-800 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full mb-3">
                <span>🗓️</span> FITUR JADWAL PERJALANAN
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                Jadwal & Timeline Liburan
            </h1>
            <p class="text-slate-600 text-base md:text-lg mt-2 max-w-2xl leading-relaxed">
                Pantau timeline perjalananmu, hitung mundur tanggal keberangkatan, dan susun aktivitas harian agar liburanmu teratur dan berkesan.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('trips.create') }}" 
               class="inline-flex items-center gap-2 bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-800 shadow-sm transition">
                <span>+ Rencanakan Jadwal Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-2xl">
                ⏳
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Akan Datang</p>
                <p class="text-2xl font-extrabold text-slate-900">{{ $plannedTrips->count() }} Rencana</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-2xl">
                ✈️
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sedang Berjalan</p>
                <p class="text-2xl font-extrabold text-emerald-700">{{ $ongoingTrips->count() }} Perjalanan</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-2xl">
                ✅
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Telah Selesai</p>
                <p class="text-2xl font-extrabold text-slate-700">{{ $completedTrips->count() }} Liburan</p>
            </div>
        </div>
    </div>

    <!-- Active & Upcoming Schedules Section -->
    <div class="grid lg:grid-cols-3 gap-8 mb-16">
        <!-- Main Timeline (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <span>📅</span> Timeline Jadwal Perjalanan
                </h2>
                <a href="{{ route('trips.index') }}" class="text-sm font-semibold text-emerald-700 hover:underline">
                    Kelola Semua ({{ $trips->count() }}) →
                </a>
            </div>

            @if($trips->isEmpty())
                <div class="bg-white p-12 rounded-3xl border border-dashed border-slate-300 text-center">
                    <p class="text-4xl mb-3">🗓️</p>
                    <h3 class="text-lg font-bold text-slate-800">Belum Ada Jadwal Liburan</h3>
                    <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">
                        Mulailah menyusun jadwal pertamamu sekarang untuk melihat timeline aktivitas harian.
                    </p>
                    <a href="{{ route('trips.create') }}" class="inline-block mt-5 bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-emerald-800 transition">
                        Buat Jadwal Perjalanan Pertama →
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($trips as $trip)
                        @php
                            $start = \Carbon\Carbon::parse($trip->tanggal_mulai);
                            $end = \Carbon\Carbon::parse($trip->tanggal_selesai);
                            $days = max(1, $start->diffInDays($end) + 1);
                            $now = \Carbon\Carbon::today();

                            if ($now->lt($start)) {
                                $diff = (int) $now->diffInDays($start);
                                $countdown = $diff === 0 ? "Mulai Besok!" : "Kurang $diff hari lagi";
                                $badgeClass = "bg-orange-100 text-orange-800 border-orange-200";
                                $statusLabel = "Akan Datang";
                            } elseif ($now->gt($end)) {
                                $countdown = "Telah selesai";
                                $badgeClass = "bg-slate-100 text-slate-700 border-slate-200";
                                $statusLabel = "Selesai";
                            } else {
                                $countdown = "Sedang Berlangsung!";
                                $badgeClass = "bg-emerald-100 text-emerald-800 border-emerald-200 animate-pulse";
                                $statusLabel = "Berlangsung";
                            }
                        @endphp

                        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex flex-col items-center justify-center shrink-0">
                                        <span class="text-xs font-bold">{{ $start->format('M') }}</span>
                                        <span class="text-base font-extrabold leading-none">{{ $start->format('d') }}</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border {{ $badgeClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                            <span class="text-xs text-slate-500 font-medium">
                                                ⏱️ {{ $countdown }}
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-900 mt-1">
                                            <a href="{{ route('trips.show', $trip) }}" class="hover:text-emerald-700 transition">
                                                {{ $trip->nama_perjalanan }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-slate-500 mt-0.5 flex items-center gap-2">
                                            <span>📍 {{ $trip->destinasi }}</span>
                                            <span>•</span>
                                            <span>{{ $start->format('d M Y') }} s/d {{ $end->format('d M Y') }} ({{ $days }} Hari)</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('trips.show', $trip) }}" 
                                       class="px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
                                        Lihat Jadwal →
                                    </a>
                                </div>
                            </div>

                            @if($trip->catatan)
                                <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl flex items-start gap-2">
                                    <span class="text-slate-400 font-bold shrink-0">📝 Catatan:</span>
                                    <span class="line-clamp-2">{{ $trip->catatan }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Interactive Quick Itinerary Planner Tool -->
        <div class="bg-gradient-to-br from-emerald-900 to-slate-900 text-white p-7 rounded-3xl shadow-lg flex flex-col justify-between">
            <div>
                <span class="inline-block bg-white/20 text-emerald-300 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-3">
                    ✨ Template Jadwal
                </span>
                <h3 class="text-2xl font-bold mb-2">Simulasi Itinerary 3 Hari</h3>
                <p class="text-emerald-100/80 text-sm mb-6 leading-relaxed">
                    Contoh pembagian waktu ideal untuk perjalanan liburan akhir pekan:
                </p>

                <div class="space-y-4 text-xs">
                    <!-- Day 1 -->
                    <div class="bg-white/10 p-3.5 rounded-2xl border border-white/10 backdrop-blur-xs">
                        <div class="font-bold text-orange-300 flex items-center justify-between mb-1.5">
                            <span>HARI 1 — Kedatangan & Santai</span>
                            <span>🌅</span>
                        </div>
                        <ul class="space-y-1 text-slate-200">
                            <li>• 09:00 - Tiba di bandara / stasiun</li>
                            <li>• 12:00 - Check-in hotel & makan siang lokal</li>
                            <li>• 16:30 - Sunset santai di pantai / spot ikonik</li>
                            <li>• 19:30 - Makan malam kuliner malam</li>
                        </ul>
                    </div>

                    <!-- Day 2 -->
                    <div class="bg-white/10 p-3.5 rounded-2xl border border-white/10 backdrop-blur-xs">
                        <div class="font-bold text-emerald-300 flex items-center justify-between mb-1.5">
                            <span>HARI 2 — Petualangan Utama</span>
                            <span>🗺️</span>
                        </div>
                        <ul class="space-y-1 text-slate-200">
                            <li>• 08:00 - Menuju destinasi wisata alam/budaya</li>
                            <li>• 11:30 - Aktivitas outdoor & foto-foto</li>
                            <li>• 14:00 - Wisata edukasi / museum / candi</li>
                            <li>• 18:00 - Café hopping & belanja oleh-oleh</li>
                        </ul>
                    </div>

                    <!-- Day 3 -->
                    <div class="bg-white/10 p-3.5 rounded-2xl border border-white/10 backdrop-blur-xs">
                        <div class="font-bold text-teal-300 flex items-center justify-between mb-1.5">
                            <span>HARI 3 — Relaksasi & Pulang</span>
                            <span>✈️</span>
                        </div>
                        <ul class="space-y-1 text-slate-200">
                            <li>• 08:30 - Sarapan santai & packing</li>
                            <li>• 11:00 - Checkout & beli cenderamata terakhir</li>
                            <li>• 14:00 - Menuju bandara untuk kepulangan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="pt-6 mt-6 border-t border-white/10 text-center">
                <a href="{{ route('trips.create') }}" 
                   class="block w-full bg-orange-400 hover:bg-orange-500 text-slate-950 font-bold py-3 rounded-xl transition shadow-md">
                    Buat Jadwalmu Sendiri →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

