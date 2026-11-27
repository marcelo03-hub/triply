@extends('layouts.app')

@section('title', $trip->nama_perjalanan . ' | Triply')

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
<div class="max-w-6xl mx-auto px-6 md:px-12 py-10">
    <!-- Breadcrumb & Action Bar -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:underline">
            <span>←</span> Kembali ke Perjalanan Saya
        </a>

        <!-- Top Actions -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Download PDF Button -->
            <a href="{{ route('trips.pdf', $trip) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs md:text-sm font-bold shadow-xs transition transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Download PDF</span>
            </a>

            <!-- Edit Button -->
            <a href="{{ route('trips.edit', $trip) }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs md:text-sm font-bold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span>Edit Rencana</span>
            </a>

            <!-- Delete Form -->
            <form action="{{ route('trips.destroy', $trip) }}" method="POST" 
                  onsubmit="return confirm('Apakah kamu yakin ingin menghapus rencana liburan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl text-xs md:text-sm font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <span class="text-lg">✅</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Main Trip Card Header -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 md:p-10 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div>
                @php
                    $badgeStyles = [
                        'planned' => 'bg-orange-100 text-orange-800 border-orange-200',
                        'ongoing' => 'bg-emerald-100 text-emerald-800 border-emerald-200 animate-pulse',
                        'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                    ];
                    $badgeText = [
                        'planned' => '⏳ Direncanakan',
                        'ongoing' => '✈️ Sedang Berlangsung',
                        'completed' => '✅ Telah Selesai',
                    ];
                @endphp
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-bold border {{ $badgeStyles[$trip->status] ?? 'bg-slate-100' }}">
                        {{ $badgeText[$trip->status] ?? ucfirst($trip->status) }}
                    </span>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">
                        📍 {{ $trip->destinasi }}
                    </span>
                    @if($trip->user)
                        <span class="text-xs text-slate-500 bg-slate-50 px-2.5 py-0.5 rounded-full">
                            👤 {{ $trip->user->name }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    {{ $trip->nama_perjalanan }}
                </h1>
            </div>

            <!-- Stats Overview Badges -->
            <div class="flex flex-wrap items-center gap-3 md:text-right">
                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl">
                    <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Target Anggaran</span>
                    <span class="text-xl md:text-2xl font-extrabold text-emerald-700 block">
                        Rp {{ number_format($trip->anggaran, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-emerald-600 block mt-0.5">
                        Biaya Terjadwal: Rp {{ number_format($totalItineraryCost, 0, ',', '.') }}
                    </span>
                </div>

                <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Pengeluaran Aktual</span>
                    <span class="text-xl md:text-2xl font-extrabold text-slate-900 block">
                        Rp {{ number_format($totalActualExpense, 0, ',', '.') }}
                    </span>
                    @php
                        $diff = $trip->anggaran - $totalActualExpense;
                    @endphp
                    <span class="text-[11px] font-bold block mt-0.5 {{ $diff < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                        {{ $diff < 0 ? 'Melebihi Rp ' . number_format(abs($diff), 0, ',', '.') : 'Sisa Rp ' . number_format($diff, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Schedule Dates & Duration Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 text-sm">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai</p>
                <p class="font-bold text-slate-800 text-base mt-0.5">
                    {{ \Carbon\Carbon::parse($trip->tanggal_mulai)->format('d F Y') }}
                </p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Selesai</p>
                <p class="font-bold text-slate-800 text-base mt-0.5">
                    {{ \Carbon\Carbon::parse($trip->tanggal_selesai)->format('d F Y') }}
                </p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Durasi Liburan</p>
                <p class="font-bold text-emerald-700 text-base mt-0.5">
                    {{ $durationDays }} Hari Penuh
                </p>
            </div>
        </div>

        @if($trip->catatan)
            <div class="mt-6 pt-6 border-t border-slate-100 bg-slate-50 p-5 rounded-2xl">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">📝 Catatan & Preferensi:</p>
                <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">{{ $trip->catatan }}</p>
            </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 mb-8 border-b border-slate-200 overflow-x-auto pb-1">
        <button type="button" onclick="switchTab('itinerary')" id="tabBtn-itinerary"
                class="tab-btn px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition flex items-center gap-2 whitespace-nowrap bg-emerald-700 text-white shadow-xs">
            <span>🗓️</span>
            <span>Jadwal Itinerary</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-extrabold">{{ $trip->itineraries->count() }}</span>
        </button>

        <button type="button" onclick="switchTab('expenses')" id="tabBtn-expenses"
                class="tab-btn px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition flex items-center gap-2 whitespace-nowrap bg-white text-slate-600 hover:text-slate-900 border border-transparent hover:border-slate-200">
            <span>💰</span>
            <span>Expense Tracker</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">{{ $expenses->count() }}</span>
        </button>

        <button type="button" onclick="switchTab('packing')" id="tabBtn-packing"
                class="tab-btn px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition flex items-center gap-2 whitespace-nowrap bg-white text-slate-600 hover:text-slate-900 border border-transparent hover:border-slate-200">
            <span>🧳</span>
            <span>Packing Checklist</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 font-extrabold">{{ $packingPercentage }}%</span>
        </button>

        <button type="button" onclick="switchTab('map')" id="tabBtn-map"
                class="tab-btn px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition flex items-center gap-2 whitespace-nowrap bg-white text-slate-600 hover:text-slate-900 border border-transparent hover:border-slate-200">
            <span>🗺️</span>
            <span>Peta Rute Perjalanan</span>
        </button>
    </div>

    <!-- TAB 1: ITINERARY -->
    <div id="tabContent-itinerary" class="tab-content">
        <div class="grid lg:grid-cols-12 gap-8">
            <!-- Itinerary List & Activity Manager (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <span>🗓️</span> Susunan Itinerary Kegiatan
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Susun jadwal berdasarkan hari, waktu, lokasi & estimasi biaya</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                        {{ $trip->itineraries->count() }} Kegiatan
                    </span>
                </div>

                <!-- Form Tambah Kegiatan Cepat -->
                <div class="bg-white p-5 rounded-3xl border border-emerald-100 shadow-xs">
                    <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-1.5">
                        <span class="text-emerald-700 font-extrabold">+</span> Tambah Kegiatan ke Itinerary
                    </h3>
                    <form action="{{ route('trips.itineraries.store', $trip) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Hari Ke</label>
                                <select name="hari_ke" class="w-full border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500">
                                    @for($d = 1; $d <= max($durationDays, 1); $d++)
                                        <option value="{{ $d }}">Hari ke-{{ $d }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Waktu</label>
                                <input type="text" name="waktu" placeholder="09:00 / Pagi"
                                       class="w-full border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Estimasi Biaya (Rp)</label>
                                <input type="number" name="estimasi_biaya" placeholder="50000" min="0" step="5000"
                                       class="w-full border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                                <input type="text" name="kegiatan" required placeholder="Contoh: Snorkeling di Gili Trawangan"
                                       class="w-full border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1">Lokasi Tempat</label>
                                <input type="text" name="lokasi" placeholder="Contoh: Dermaga Utama Gili"
                                       class="w-full border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <input type="text" name="catatan" placeholder="Catatan kecil / tips tambahan (opsional)..."
                                   class="w-2/3 border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500">
                            <button type="submit" 
                                    class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-4 py-1.5 rounded-xl text-xs transition shadow-xs">
                                + Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Grouped Itineraries by Day -->
                <div class="space-y-4">
                    @for($day = 1; $day <= max($durationDays, 1); $day++)
                        @php
                            $currentDate = \Carbon\Carbon::parse($trip->tanggal_mulai)->addDays($day - 1);
                            $dayItems = $itinerariesByDay->get($day, collect());
                        @endphp
                        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center">
                                        H{{ $day }}
                                    </span>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm">
                                            Hari ke-{{ $day }}
                                        </h3>
                                        <span class="text-[11px] text-slate-400">
                                            {{ $currentDate->format('l, d M Y') }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">
                                    {{ $dayItems->count() }} Kegiatan
                                </span>
                            </div>

                            @if($dayItems->isEmpty())
                                <p class="text-xs text-slate-400 italic py-2">
                                    Belum ada agenda kegiatan untuk hari ke-{{ $day }}. Tambahkan kegiatan di form atas.
                                </p>
                            @else
                                <div class="space-y-2.5">
                                    @foreach($dayItems as $item)
                                        <div class="p-3 rounded-2xl border {{ $item->status === 'completed' ? 'bg-slate-50/70 border-slate-200 text-slate-400' : 'bg-white border-slate-100 shadow-2xs' }} flex items-start justify-between gap-3 transition">
                                            <div class="flex items-start gap-2.5 flex-1 min-w-0">
                                                <!-- Toggle Status -->
                                                <form action="{{ route('trips.itineraries.toggle', [$trip, $item]) }}" method="POST" class="pt-0.5">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="w-5 h-5 rounded-md border flex items-center justify-center transition {{ $item->status === 'completed' ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 hover:border-emerald-500 bg-white' }}"
                                                            title="Tandai selesai">
                                                        @if($item->status === 'completed')
                                                            ✓
                                                        @endif
                                                    </button>
                                                </form>

                                                <div class="flex-1 min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        @if($item->waktu)
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                                                ⏰ {{ $item->waktu }}
                                                            </span>
                                                        @endif
                                                        <span class="font-bold text-sm {{ $item->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-800' }}">
                                                            {{ $item->kegiatan }}
                                                        </span>
                                                    </div>

                                                    @if($item->lokasi)
                                                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                                            <span>📍</span> <span>{{ $item->lokasi }}</span>
                                                        </p>
                                                    @endif

                                                    @if($item->catatan)
                                                        <p class="text-xs text-slate-400 italic mt-0.5">
                                                            💬 {{ $item->catatan }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3 shrink-0">
                                                @if($item->estimasi_biaya > 0)
                                                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                                        Rp {{ number_format($item->estimasi_biaya, 0, ',', '.') }}
                                                    </span>
                                                @endif

                                                <form action="{{ route('trips.itineraries.destroy', [$trip, $item]) }}" method="POST"
                                                      onsubmit="return confirm('Hapus kegiatan ini dari jadwal?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-400 hover:text-red-600 text-xs p-1" title="Hapus">
                                                        ✕
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Cost Estimation & Allocation (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Cost Comparison Box -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                            <span>💰</span> Estimasi & Realisasi Biaya
                        </h3>
                        <a href="{{ route('anggaran') }}" class="text-xs font-bold text-emerald-700 hover:underline">
                            Kalkulator ↗
                        </a>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500">Target Anggaran Trip</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($trip->anggaran, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500">Total Biaya Kegiatan Terjadwal</span>
                            <span class="font-bold text-emerald-700">Rp {{ number_format($totalItineraryCost, 0, ',', '.') }}</span>
                        </div>

                        @php
                            $sisa = $trip->anggaran - $totalItineraryCost;
                            $percentUsed = $trip->anggaran > 0 ? min(100, round(($totalItineraryCost / $trip->anggaran) * 100)) : 0;
                        @endphp

                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden mt-1">
                            <div class="h-2.5 rounded-full {{ $sisa < 0 ? 'bg-red-500' : 'bg-emerald-600' }}" style="width: {{ $percentUsed }}%"></div>
                        </div>

                        <div class="flex justify-between items-center text-xs pt-1">
                            <span class="font-bold {{ $sisa < 0 ? 'text-red-600' : 'text-slate-700' }}">
                                {{ $sisa < 0 ? 'Melebihi Anggaran:' : 'Sisa Alokasi Anggaran:' }}
                            </span>
                            <span class="font-extrabold text-sm {{ $sisa < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                                Rp {{ number_format(abs($sisa), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Smart Budget Breakdown -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                        <span>📊</span> Rekomendasi Alokasi Dana
                    </h3>

                    <div class="space-y-3">
                        @foreach($budgetBreakdown as $key => $item)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                        <span>{{ $item['icon'] }}</span>
                                        <span>{{ $item['name'] }}</span>
                                    </span>
                                    <span class="font-extrabold text-slate-900">
                                        Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                        <span class="text-slate-400 font-normal">({{ $item['percentage'] }}%)</span>
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-slate-100 text-xs text-slate-400 leading-relaxed">
                        💡 Rekomendasi pembagian alokasi dana disusun berdasarkan rata-rata biaya standar akomodasi, konsumsi, tiket & transportasi.
                    </div>
                </div>

                <!-- AI Travel Assistant Widget Box -->
                <div class="bg-gradient-to-br from-emerald-600 to-teal-800 text-white p-6 rounded-3xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">✨</span>
                        <h3 class="font-extrabold text-base">Asisten AI Triply</h3>
                    </div>
                    <p class="text-xs text-emerald-100 leading-relaxed mb-4">
                        Butuh ide tambahan itinerary atau rekomendasi tempat hidden-gem di {{ $trip->destinasi }}?
                    </p>
                    <a href="{{ route('ai.index', ['trip_id' => $trip->id]) }}" 
                       class="inline-flex items-center justify-center gap-2 w-full bg-white text-emerald-800 font-bold py-2.5 px-4 rounded-xl text-xs hover:bg-emerald-50 shadow-xs transition">
                        <span>Konsultasi Itinerary dengan AI →</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: EXPENSE TRACKER -->
    <div id="tabContent-expenses" class="tab-content hidden space-y-8">
        <!-- Budget vs Actual Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Target Anggaran</span>
                <span class="text-2xl font-extrabold text-slate-800 block mt-1">
                    Rp {{ number_format($trip->anggaran, 0, ',', '.') }}
                </span>
                <span class="text-xs text-slate-500 mt-1 block">Batas pagu yang direncanakan</span>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pengeluaran Aktual</span>
                <span class="text-2xl font-extrabold text-emerald-700 block mt-1">
                    Rp {{ number_format($totalActualExpense, 0, ',', '.') }}
                </span>
                <span class="text-xs text-slate-500 mt-1 block">{{ $expenses->count() }} transaksi tercatat</span>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status Saldo Anggaran</span>
                @php
                    $expenseDiff = $trip->anggaran - $totalActualExpense;
                    $expenseRatio = $trip->anggaran > 0 ? round(($totalActualExpense / $trip->anggaran) * 100) : 0;
                @endphp
                <span class="text-2xl font-extrabold block mt-1 {{ $expenseDiff < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                    Rp {{ number_format(abs($expenseDiff), 0, ',', '.') }}
                </span>
                <span class="text-xs font-semibold block mt-1 {{ $expenseDiff < 0 ? 'text-red-500' : 'text-slate-500' }}">
                    {{ $expenseDiff < 0 ? '⚠️ Overbudget (' . $expenseRatio . '%)' : '✅ Sisa dana aman (' . $expenseRatio . '% terpakai)' }}
                </span>
            </div>
        </div>

        <!-- Expense Category Breakdown Progress -->
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
            <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <span>📊</span> Rincian Pengeluaran Berdasarkan Kategori
            </h3>

            @php
                $catIcons = [
                    'transportasi' => '🚗',
                    'akomodasi' => '🏨',
                    'makanan' => '🍜',
                    'tiket wisata' => '🎟️',
                    'tiket' => '🎟️',
                    'belanja' => '🛍️',
                    'lainnya' => '📦',
                ];
                $allCats = [
                    'transportasi' => 'Transportasi',
                    'akomodasi' => 'Akomodasi',
                    'makanan' => 'Makanan',
                    'tiket wisata' => 'Tiket Wisata',
                    'belanja' => 'Belanja',
                    'lainnya' => 'Lainnya',
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($allCats as $key => $catName)
                    @php
                        $catTotal = $expensesByCategory->get($key, 0);
                        if ($key === 'tiket wisata') {
                            $catTotal += $expensesByCategory->get('tiket', 0);
                        }
                        $catPct = $totalActualExpense > 0 ? round(($catTotal / $totalActualExpense) * 100) : 0;
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                <span>{{ $catIcons[$key] ?? '🏷️' }}</span>
                                <span>{{ $catName }}</span>
                            </span>
                            <span class="font-extrabold text-slate-900">
                                Rp {{ number_format($catTotal, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $catPct }}%"></div>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block text-right">{{ $catPct }}% dari total belanja</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Add Expense Form -->
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-emerald-100 shadow-xs">
            <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <span>➕</span> Catat Pengeluaran Baru
            </h3>
            <form action="{{ route('trips.expenses.store', $trip) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama / Judul Pengeluaran <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="judul" required placeholder="Contoh: Tiket Pesawat Pulang-Pergi"
                               class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nominal (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="nominal" required min="1" step="1000" placeholder="Contoh: 750000"
                               class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori" required class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-semibold">
                            <option value="transportasi">🚗 Transportasi</option>
                            <option value="akomodasi">🏨 Akomodasi</option>
                            <option value="makanan">🍜 Makanan</option>
                            <option value="tiket wisata">🎟️ Tiket Wisata</option>
                            <option value="belanja">🛍️ Belanja</option>
                            <option value="lainnya">📦 Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Pengeluaran <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                               class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-semibold">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan Tambahan (Opsional)
                        </label>
                        <div class="flex gap-3">
                            <input type="text" name="catatan" placeholder="Keterangan transaksi, nota, atau tempat pembelian..."
                                   class="flex-1 border border-slate-200 rounded-2xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500">
                            <button type="submit" 
                                    class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-6 py-2.5 rounded-2xl text-xs transition shadow-xs whitespace-nowrap">
                                + Simpan Pengeluaran
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Expense History Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>🧾</span> Riwayat Pengeluaran
                </h3>
                <span class="text-xs font-bold text-slate-500">{{ $expenses->count() }} Data</span>
            </div>

            @if($expenses->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-4xl mb-3">💸</p>
                    <h4 class="text-base font-bold text-slate-800">Belum Ada Pengeluaran yang Dicatat</h4>
                    <p class="text-slate-400 text-xs mt-1">Gunakan form di atas untuk mencatat pengeluaran harian selama perjalanan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="p-4 pl-6">Tanggal</th>
                                <th class="p-4">Pengeluaran</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Catatan</th>
                                <th class="p-4 text-right">Nominal</th>
                                <th class="p-4 pr-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($expenses as $exp)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="p-4 pl-6 text-slate-500 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($exp->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="p-4 font-bold text-slate-900">
                                        {{ $exp->judul }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            <span>{{ $catIcons[$exp->kategori] ?? '🏷️' }}</span>
                                            <span>{{ $exp->kategori }}</span>
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-500 max-w-xs truncate">
                                        {{ $exp->catatan ?: '-' }}
                                    </td>
                                    <td class="p-4 text-right font-extrabold text-emerald-700 whitespace-nowrap">
                                        Rp {{ number_format($exp->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 pr-6 text-center whitespace-nowrap">
                                        <form action="{{ route('trips.expenses.destroy', [$trip, $exp]) }}" method="POST"
                                              onsubmit="return confirm('Hapus catatan pengeluaran ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 p-1" title="Hapus">
                                                ✕
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-extrabold text-slate-900 border-t border-slate-200">
                            <tr>
                                <td colspan="4" class="p-4 pl-6 text-right uppercase tracking-wider">Total Pengeluaran Aktual:</td>
                                <td class="p-4 text-right text-emerald-800 text-sm">
                                    Rp {{ number_format($totalActualExpense, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: PACKING CHECKLIST -->
    <div id="tabContent-packing" class="tab-content hidden space-y-8">
        <!-- Progress Bar Card -->
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-3">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🧳</span> Persiapan Barang Bawaan (Packing)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pantau perlengkapan yang sudah masuk ke koper dan tas perjalananmu.</p>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-emerald-700">{{ $packedCount }}/{{ $totalPacking }}</span>
                    <span class="text-xs text-slate-400 font-semibold block">Barang Disiapkan ({{ $packingPercentage }}%)</span>
                </div>
            </div>

            <div class="w-full bg-slate-100 rounded-full h-3.5 overflow-hidden">
                <div class="bg-emerald-600 h-3.5 rounded-full transition-all duration-500" style="width: {{ $packingPercentage }}%"></div>
            </div>
        </div>

        <!-- Add Packing Item Form -->
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-blue-100 shadow-xs">
            <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <span>➕</span> Tambah Barang ke Checklist
            </h3>
            <form action="{{ route('trips.packing.store', $trip) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_barang" required placeholder="Contoh: Paspor / KTP, Sunblock, Charger HP..."
                               class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori" required class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-semibold">
                            <option value="Dokumen & Uang">📄 Dokumen & Uang</option>
                            <option value="Pakaian">👕 Pakaian</option>
                            <option value="Gadget & Elektronik">🔌 Gadget & Elektronik</option>
                            <option value="Obat & Toiletries">💊 Obat & Toiletries</option>
                            <option value="Aksesoris & Lainnya">🎒 Aksesoris & Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jumlah
                        </label>
                        <div class="flex gap-2">
                            <input type="number" name="jumlah" value="1" min="1" max="99"
                                   class="w-20 border border-slate-200 rounded-2xl px-3 py-2.5 text-xs text-center font-bold focus:ring-2 focus:ring-emerald-500">
                            <button type="submit" 
                                    class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 px-3 rounded-2xl text-xs transition shadow-xs">
                                + Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Packing Items Grouped by Category -->
        <div class="space-y-6">
            @php
                $packingCats = ['Dokumen & Uang', 'Pakaian', 'Gadget & Elektronik', 'Obat & Toiletries', 'Aksesoris & Lainnya'];
                $catBadgeIcons = [
                    'Dokumen & Uang' => '📄',
                    'Pakaian' => '👕',
                    'Gadget & Elektronik' => '🔌',
                    'Obat & Toiletries' => '💊',
                    'Aksesoris & Lainnya' => '🎒',
                ];
            @endphp

            @if($packingItems->isEmpty())
                <div class="bg-white p-12 text-center rounded-3xl border border-dashed border-slate-200">
                    <p class="text-4xl mb-3">🧳</p>
                    <h4 class="text-base font-bold text-slate-800">Daftar Bawaan Masih Kosong</h4>
                    <p class="text-slate-400 text-xs mt-1">Tambahkan barang-barang penting menggunakan form di atas agar tidak ada yang tertinggal.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($packingCats as $cat)
                        @php
                            $itemsInCat = $packingByCategory->get($cat, collect());
                        @endphp
                        @if($itemsInCat->isNotEmpty())
                            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                                    <h4 class="font-extrabold text-sm text-slate-900 flex items-center gap-1.5">
                                        <span>{{ $catBadgeIcons[$cat] ?? '🏷️' }}</span>
                                        <span>{{ $cat }}</span>
                                    </h4>
                                    <span class="text-xs text-slate-400 font-semibold">
                                        {{ $itemsInCat->where('is_packed', true)->count() }}/{{ $itemsInCat->count() }} Siap
                                    </span>
                                </div>

                                <div class="space-y-2.5">
                                    @foreach($itemsInCat as $item)
                                        <div class="p-3 rounded-2xl border {{ $item->is_packed ? 'bg-emerald-50/40 border-emerald-100 text-slate-400' : 'bg-slate-50 border-slate-100' }} flex items-center justify-between gap-3 transition">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <form action="{{ route('trips.packing.toggle', [$trip, $item]) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="w-5 h-5 rounded-md border flex items-center justify-center transition {{ $item->is_packed ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 hover:border-emerald-500 bg-white' }}">
                                                        @if($item->is_packed) ✓ @endif
                                                    </button>
                                                </form>

                                                <span class="text-xs font-bold {{ $item->is_packed ? 'line-through text-slate-400' : 'text-slate-800' }} truncate">
                                                    {{ $item->nama_barang }}
                                                </span>

                                                @if($item->jumlah > 1)
                                                    <span class="px-2 py-0.5 rounded-md bg-slate-200 text-slate-600 text-[10px] font-extrabold">
                                                        x{{ $item->jumlah }}
                                                    </span>
                                                @endif
                                            </div>

                                            <form action="{{ route('trips.packing.destroy', [$trip, $item]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus barang ini dari daftar?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-red-600 text-xs p-1" title="Hapus">
                                                    ✕
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 4: INTERACTIVE ROUTE MAP -->
    <div id="tabContent-map" class="tab-content hidden space-y-6">
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🗺️</span> Peta Rute & Titik Kunjungan Liburan
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Visualisasi rute kegiatan harian dan titik kunjungan di {{ $trip->destinasi }}.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        📍 {{ $trip->itineraries->count() }} Titik Terjadwal
                    </span>
                </div>
            </div>

            <!-- Route Map Canvas -->
            <div id="routeMap" class="w-full h-[450px] sm:h-[520px] rounded-2xl border border-slate-200 overflow-hidden relative z-0"></div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 pt-3 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-700 inline-block"></span>
                    <span>Destinasi Utama</span>
                    <span class="w-3 h-3 rounded-full bg-blue-600 inline-block ml-2"></span>
                    <span>Jadwal Kegiatan (Waypoints)</span>
                </div>
                <span class="text-slate-400">Peta ditenagai oleh Leaflet & OpenStreetMap</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Tab switching logic
    let routeMapInstance = null;

    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-emerald-700', 'text-white', 'shadow-xs');
            btn.classList.add('bg-white', 'text-slate-600', 'border-transparent');
        });

        const selectedContent = document.getElementById('tabContent-' + tabName);
        const selectedBtn = document.getElementById('tabBtn-' + tabName);

        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
        if (selectedBtn) {
            selectedBtn.classList.remove('bg-white', 'text-slate-600', 'border-transparent');
            selectedBtn.classList.add('bg-emerald-700', 'text-white', 'shadow-xs');
        }

        // Initialize or invalidate route map on tab switch
        if (tabName === 'map') {
            setTimeout(initRouteMap, 150);
        }
    }

    // Leaflet Route Map initialization
    function initRouteMap() {
        const mapContainer = document.getElementById('routeMap');
        if (!mapContainer) return;

        if (routeMapInstance) {
            routeMapInstance.invalidateSize();
            return;
        }

        @php
            $baseLat = $destination ? $destination->display_latitude : -8.409518;
            $baseLng = $destination ? $destination->display_longitude : 115.188919;
            $waypoints = [];
            foreach($trip->itineraries as $idx => $itn) {
                // Generate slight offset coordinates if item doesn't have exact lat/lng
                $latOffset = ($itn->latitude !== null) ? (float)$itn->latitude : $baseLat + (($idx % 4 + 1) * 0.02 * (($idx % 2 === 0) ? 1 : -1));
                $lngOffset = ($itn->longitude !== null) ? (float)$itn->longitude : $baseLng + (($idx % 3 + 1) * 0.02 * (($idx % 2 !== 0) ? 1 : -1));

                $waypoints[] = [
                    'id' => $itn->id,
                    'hari' => $itn->hari_ke,
                    'waktu' => $itn->waktu ?: 'Jadwal Hari ke-' . $itn->hari_ke,
                    'kegiatan' => $itn->kegiatan,
                    'lokasi' => $itn->lokasi ?: $trip->destinasi,
                    'biaya' => (float) $itn->estimasi_biaya,
                    'lat' => (float) $latOffset,
                    'lng' => (float) $lngOffset,
                ];
            }
        @endphp

        const baseLat = {{ $baseLat }};
        const baseLng = {{ $baseLng }};
        const destinationName = @json($trip->destinasi);
        const waypoints = @json($waypoints);

        routeMapInstance = L.map('routeMap', {
            scrollWheelZoom: false
        }).setView([baseLat, baseLng], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(routeMapInstance);

        const bounds = [];
        bounds.push([baseLat, baseLng]);

        // Main destination marker
        const destIcon = L.divIcon({
            className: 'dest-pin',
            html: `
                <div style="background-color: #047857; color: white; border-radius: 9999px; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3); border: 3px solid white; font-size: 18px;">
                    📍
                </div>
            `,
            iconSize: [38, 38],
            iconAnchor: [19, 38],
            popupAnchor: [0, -38]
        });

        L.marker([baseLat, baseLng], { icon: destIcon })
            .addTo(routeMapInstance)
            .bindPopup(`
                <div style="padding: 4px; font-family: inherit;">
                    <span style="font-size: 10px; font-weight: 800; color: #047857; text-transform: uppercase;">Destinasi Utama</span>
                    <h4 style="font-weight: 800; font-size: 14px; color: #0f172a; margin-top: 2px;">${destinationName}</h4>
                </div>
            `);

        // Waypoints markers & polyline path
        const polylineCoords = [[baseLat, baseLng]];

        waypoints.forEach((wp, index) => {
            bounds.push([wp.lat, wp.lng]);
            polylineCoords.push([wp.lat, wp.lng]);

            const wpIcon = L.divIcon({
                className: 'wp-pin',
                html: `
                    <div style="background-color: #2563eb; color: white; border-radius: 9999px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); border: 2px solid white; font-size: 11px; font-weight: 800;">
                        ${wp.hari}
                    </div>
                `,
                iconSize: [30, 30],
                iconAnchor: [15, 30],
                popupAnchor: [0, -30]
            });

            L.marker([wp.lat, wp.lng], { icon: wpIcon })
                .addTo(routeMapInstance)
                .bindPopup(`
                    <div style="padding: 4px; font-family: inherit;">
                        <span style="font-size: 10px; font-weight: 800; color: #2563eb;">HARI KE-${wp.hari} • ${wp.waktu}</span>
                        <h4 style="font-weight: 800; font-size: 13px; color: #0f172a; margin-top: 2px;">${wp.kegiatan}</h4>
                        <p style="font-size: 11px; color: #64748b; margin-top: 2px;">📍 ${wp.lokasi}</p>
                    </div>
                `);
        });

        // Draw connecting route polyline
        if (polylineCoords.length > 1) {
            L.polyline(polylineCoords, {
                color: '#059669',
                weight: 4,
                opacity: 0.8,
                dashArray: '8, 8',
                lineJoin: 'round'
            }).addTo(routeMapInstance);
        }

        if (bounds.length > 1) {
            routeMapInstance.fitBounds(bounds, { padding: [40, 40] });
        }
    }

    // Auto-select tab if present in query param or URL hash
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || (window.location.hash ? window.location.hash.substring(1) : null);
        if (tabParam && ['itinerary', 'expenses', 'packing', 'map'].includes(tabParam)) {
            switchTab(tabParam);
        }
    });
</script>
@endpush
