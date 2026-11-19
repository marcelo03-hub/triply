@extends('layouts.app')

@section('title', 'Anggaran Perjalanan Domestik & Mancanegara | Triply')

@section('content')
<div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full mb-3">
                <span>💰</span> FITUR ANGGARAN PERJALANAN
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                Kalkulator & Pelacak Anggaran
            </h1>
            <p class="text-slate-600 text-base md:text-lg mt-2 max-w-2xl leading-relaxed">
                Kendalikan pengeluaran liburanmu baik untuk wisata <strong>Domestik</strong> maupun <strong>Mancanegara (Luar Negeri)</strong>.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('trips.create') }}" 
               class="inline-flex items-center gap-2 bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-800 shadow-sm transition">
                <span>+ Catat Anggaran Trip</span>
            </a>
        </div>
    </div>

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Anggaran Terdaftar</span>
                <span class="text-2xl">💳</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900">
                Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
            </p>
            <p class="text-xs text-slate-500 mt-1">Dari {{ $trips->count() }} rencana perjalanan</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Rata-Rata per Liburan</span>
                <span class="text-2xl">📊</span>
            </div>
            <p class="text-3xl font-extrabold text-emerald-700">
                Rp {{ number_format($averageAnggaran, 0, ',', '.') }}
            </p>
            <p class="text-xs text-slate-500 mt-1">Estimasi biaya rata-rata tiap trip</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Trip Terbesar</span>
                <span class="text-2xl">🏆</span>
            </div>
            <p class="text-3xl font-extrabold text-blue-700">
                @if($maxTrip)
                    Rp {{ number_format($maxTrip->anggaran, 0, ',', '.') }}
                @else
                    Rp 0
                @endif
            </p>
            <p class="text-xs text-slate-500 mt-1 truncate">
                {{ $maxTrip ? $maxTrip->nama_perjalanan : 'Belum ada data' }}
            </p>
        </div>
    </div>

    <!-- Interactive Budget Estimator (Kalkulator Anggaran Interaktif) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-8 mb-16">
        <div class="max-w-2xl mb-8">
            <span class="inline-block bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                ⚡ KALKULATOR ESTIMASI PINTAR (DOMESTIK & LUAR NEGERI)
            </span>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                Hitung Estimasi Biaya Liburan
            </h2>
            <p class="text-slate-600 text-sm mt-1">
                Pilih tujuan (dalam negeri atau luar negeri), durasi hari, jumlah orang, dan gaya liburan untuk menghitung alokasi biaya secara real-time.
            </p>
        </div>

        <div class="grid lg:grid-cols-12 gap-8 items-start">
            <!-- Controls Form (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Destinasi Selection with Domestic & International -->
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">1. Pilih Destinasi Tujuan</label>
                    
                    <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2">🇮🇩 Domestik (Indonesia):</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
                        <button type="button" onclick="setCalcDest('Bali', 750000, this)" class="dest-calc-btn active p-2.5 rounded-xl border text-center transition border-emerald-600 bg-emerald-50 text-emerald-900 font-bold text-xs">
                            🏝️ Bali
                        </button>
                        <button type="button" onclick="setCalcDest('Yogyakarta', 450000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🏛️ Yogyakarta
                        </button>
                        <button type="button" onclick="setCalcDest('Bandung', 400000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🍃 Bandung
                        </button>
                        <button type="button" onclick="setCalcDest('Lombok', 700000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🌊 Lombok
                        </button>
                    </div>

                    <p class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-2">✈️ Luar Negeri (Mancanegara):</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button type="button" onclick="setCalcDest('Singapura', 1400000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🇸🇬 Singapura
                        </button>
                        <button type="button" onclick="setCalcDest('Bangkok, Thailand', 1100000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🇹🇭 Thailand
                        </button>
                        <button type="button" onclick="setCalcDest('Kuala Lumpur, Malaysia', 950000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🇲🇾 Malaysia
                        </button>
                        <button type="button" onclick="setCalcDest('Tokyo, Jepang', 2800000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🇯🇵 Jepang
                        </button>
                        <button type="button" onclick="setCalcDest('Seoul, Korea Selatan', 2500000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🇰🇷 Korea Selatan
                        </button>
                        <button type="button" onclick="setCalcDest('Zermatt, Swiss', 4500000, this)" class="dest-calc-btn p-2.5 rounded-xl border text-center transition border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50">
                            🇨🇭 Swiss (Eropa)
                        </button>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <!-- Durasi -->
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">2. Durasi Hari</label>
                        <div class="flex items-center gap-3">
                            <input type="range" id="calcDays" min="1" max="14" value="4" 
                                   class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-600"
                                   oninput="updateCalc()">
                            <span id="calcDaysLabel" class="w-16 font-extrabold text-slate-900 text-right text-base shrink-0">4 Hari</span>
                        </div>
                    </div>

                    <!-- Jumlah Orang -->
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">3. Jumlah Orang</label>
                        <div class="flex items-center gap-3">
                            <input type="range" id="calcPersons" min="1" max="10" value="2" 
                                   class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-600"
                                   oninput="updateCalc()">
                            <span id="calcPersonsLabel" class="w-16 font-extrabold text-slate-900 text-right text-base shrink-0">2 Orang</span>
                        </div>
                    </div>
                </div>

                <!-- Gaya Perjalanan -->
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">4. Gaya Liburan</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative flex flex-col p-4 rounded-2xl border border-slate-200 cursor-pointer has-checked:border-emerald-600 has-checked:bg-emerald-50/60 transition">
                            <input type="radio" name="travel_style" value="0.75" class="sr-only" onchange="updateCalc()">
                            <span class="text-xl mb-1">🎒</span>
                            <span class="font-bold text-xs sm:text-sm text-slate-900">Hemat / Backpacker</span>
                            <span class="text-xs text-slate-500 mt-0.5">Hostel & public transport</span>
                        </label>
                        <label class="relative flex flex-col p-4 rounded-2xl border border-slate-200 cursor-pointer has-checked:border-emerald-600 has-checked:bg-emerald-50/60 transition">
                            <input type="radio" name="travel_style" value="1.0" checked class="sr-only" onchange="updateCalc()">
                            <span class="text-xl mb-1">✨</span>
                            <span class="font-bold text-xs sm:text-sm text-slate-900">Standar / Nyaman</span>
                            <span class="text-xs text-slate-500 mt-0.5">Hotel bintang 3 & kuliner enak</span>
                        </label>
                        <label class="relative flex flex-col p-4 rounded-2xl border border-slate-200 cursor-pointer has-checked:border-emerald-600 has-checked:bg-emerald-50/60 transition">
                            <input type="radio" name="travel_style" value="1.8" class="sr-only" onchange="updateCalc()">
                            <span class="text-xl mb-1">👑</span>
                            <span class="font-bold text-xs sm:text-sm text-slate-900">Sultan / Mewah</span>
                            <span class="text-xs text-slate-500 mt-0.5">Hotel bintang 5 & private trip</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Live Calculation Results (5 cols) -->
            <div class="lg:col-span-5 bg-slate-900 text-white p-7 rounded-3xl shadow-lg">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">ESTIMASI TOTAL ANGGARAN</span>
                <p id="calcTotal" class="text-3xl sm:text-4xl font-extrabold text-orange-400 mb-6">
                    Rp 0
                </p>

                <!-- Detailed Breakdown -->
                <div class="space-y-3 text-xs mb-8">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-300 flex items-center gap-1.5">
                            <span>✈️</span> Tiket Pesawat & Transportasi
                        </span>
                        <span id="calcTransport" class="font-bold text-slate-100">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-300 flex items-center gap-1.5">
                            <span>🏨</span> Penginapan / Hotel
                        </span>
                        <span id="calcHotel" class="font-bold text-slate-100">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-300 flex items-center gap-1.5">
                            <span>🍜</span> Makan & Kuliner Lokal
                        </span>
                        <span id="calcFood" class="font-bold text-slate-100">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-300 flex items-center gap-1.5">
                            <span>🎟️</span> Tiket Atraksi & Wisata
                        </span>
                        <span id="calcTickets" class="font-bold text-slate-100">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-300 flex items-center gap-1.5">
                            <span>🛡️</span> Asuransi, Visa & Dana Darurat
                        </span>
                        <span id="calcEmergency" class="font-bold text-slate-100">Rp 0</span>
                    </div>
                </div>

                <a id="btnApplyBudget" href="{{ route('trips.create') }}" 
                   class="block text-center w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-2xl transition shadow-md">
                    Gunakan Estimasi Ini untuk Buat Rencana →
                </a>
            </div>
        </div>
    </div>

    <!-- Trip Budget Records List -->
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <span>📋</span> Rincian Anggaran Perjalanan Anda
        </h2>

        @if($trips->isEmpty())
            <div class="bg-white p-10 rounded-3xl border border-slate-200 text-center">
                <p class="text-slate-500">Belum ada anggaran perjalanan yang disimpan.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($trips as $trip)
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800">
                                📍 {{ $trip->destinasi }}
                            </span>
                            <span class="text-xs text-slate-400 capitalize">
                                {{ $trip->status }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">
                            <a href="{{ route('trips.show', $trip) }}" class="hover:text-emerald-700">
                                {{ $trip->nama_perjalanan }}
                            </a>
                        </h3>
                        <p class="text-2xl font-extrabold text-emerald-700 my-3">
                            Rp {{ number_format($trip->anggaran, 0, ',', '.') }}
                        </p>
                        <div class="text-xs text-slate-500 space-y-1 pt-3 border-t border-slate-100">
                            <div>🗓️ {{ \Carbon\Carbon::parse($trip->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($trip->tanggal_selesai)->format('d M Y') }}</div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('trips.show', $trip) }}" class="block text-center w-full py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-bold transition">
                                Lihat Rincian Alokasi →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedDestName = 'Bali';
    let baseDailyCost = 750000;

    function setCalcDest(name, baseCost, btn) {
        selectedDestName = name;
        baseDailyCost = baseCost;

        document.querySelectorAll('.dest-calc-btn').forEach(b => {
            b.classList.remove('border-emerald-600', 'bg-emerald-50', 'text-emerald-900', 'font-bold');
            b.classList.add('border-slate-200', 'bg-white', 'text-slate-700', 'font-semibold');
        });

        btn.classList.remove('border-slate-200', 'bg-white', 'text-slate-700', 'font-semibold');
        btn.classList.add('border-emerald-600', 'bg-emerald-50', 'text-emerald-900', 'font-bold');

        updateCalc();
    }

    function updateCalc() {
        const days = parseInt(document.getElementById('calcDays').value) || 1;
        const persons = parseInt(document.getElementById('calcPersons').value) || 1;

        document.getElementById('calcDaysLabel').textContent = days + ' Hari';
        document.getElementById('calcPersonsLabel').textContent = persons + ' Orang';

        const styleRadio = document.querySelector('input[name="travel_style"]:checked');
        const styleMultiplier = styleRadio ? parseFloat(styleRadio.value) : 1.0;

        // Formula: baseDailyCost * days * persons * styleMultiplier
        const total = Math.round(baseDailyCost * days * (1 + (persons - 1) * 0.7) * styleMultiplier);

        const transport = Math.round(total * 0.35);
        const hotel = Math.round(total * 0.30);
        const food = Math.round(total * 0.18);
        const tickets = Math.round(total * 0.12);
        const emergency = Math.round(total * 0.05);

        const fmt = (num) => 'Rp ' + num.toLocaleString('id-ID');

        document.getElementById('calcTotal').textContent = fmt(total);
        document.getElementById('calcTransport').textContent = fmt(transport);
        document.getElementById('calcHotel').textContent = fmt(hotel);
        document.getElementById('calcFood').textContent = fmt(food);
        document.getElementById('calcTickets').textContent = fmt(tickets);
        document.getElementById('calcEmergency').textContent = fmt(emergency);

        // Update link to create trip with prefilled data
        const btnApply = document.getElementById('btnApplyBudget');
        if (btnApply) {
            btnApply.href = `{{ route('trips.create') }}?destinasi=${encodeURIComponent(selectedDestName)}&anggaran=${total}`;
        }
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', updateCalc);
</script>
@endpush
