@extends('layouts.app')

@section('title', $trip->nama_perjalanan . ' | Triply')

@section('content')
<div class="max-w-6xl mx-auto px-6 md:px-12 py-10">
    <!-- Breadcrumb & Action Bar -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:underline">
            <span>←</span> Kembali ke Perjalanan Saya
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('trips.edit', $trip) }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs md:text-sm font-bold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span>Edit Rencana</span>
            </a>

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

            <!-- Total Budget Tag -->
            <div class="bg-emerald-50 border border-emerald-100 p-5 rounded-2xl md:text-right shrink-0">
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">Total Anggaran</span>
                <span class="text-2xl md:text-3xl font-extrabold text-emerald-700">
                    Rp {{ number_format($trip->anggaran, 0, ',', '.') }}
                </span>
                <span class="text-xs text-emerald-600 block mt-0.5">
                    Biaya Terjadwal: Rp {{ number_format($totalItineraryCost, 0, ',', '.') }}
                </span>
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

    <!-- Main Content: Itinerary (7 cols) & Cost Estimation (5 cols) -->
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
@endsection
