@extends('layouts.app')

@section('title', 'AI Travel Assistant — Susun Itinerary Otomatis | Triply')

@section('content')
<div class="max-w-5xl mx-auto px-6 md:px-12 py-10">
    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-linear-to-r from-emerald-100 to-teal-100 text-emerald-800 border border-emerald-200 shadow-2xs mb-3">
            <span>✨</span> Triply Smart AI Engine
        </span>
        <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
            Asisten Perjalanan AI
        </h1>
        <p class="text-slate-600 text-sm md:text-base mt-2">
            Ketik kota tujuan dan preferensi gaya liburanmu, AI kami akan menyusunkan jadwal itinerary harian, rekomendasi spot terbaik, serta rincian estimasi biaya secara otomatis.
        </p>
    </div>

    <!-- AI Generator Input Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-6 md:p-10 mb-10">
        <form id="aiTripForm" class="space-y-6">
            @csrf
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Destinasi -->
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Destinasi Impian <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="aiDestinasi" name="destinasi" required
                           value="{{ $selectedTrip ? $selectedTrip->destinasi : request('destinasi', 'Bali') }}"
                           placeholder="Contoh: Bali, Tokyo, Lombok, Swiss..."
                           class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                </div>

                <!-- Durasi Hari -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Durasi Liburan <span class="text-red-500">*</span>
                    </label>
                    <select id="aiDurasi" name="durasi_hari" class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                        <option value="1">1 Hari (Day Trip)</option>
                        <option value="2">2 Hari (Weekend Getaway)</option>
                        <option value="3" selected>3 Hari 2 Malam (Populer)</option>
                        <option value="4">4 Hari 3 Malam</option>
                        <option value="5">5 Hari 4 Malam</option>
                        <option value="7">7 Hari (1 Minggu Penuh)</option>
                        <option value="10">10 Hari (Long Trip)</option>
                    </select>
                </div>

                <!-- Gaya Liburan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Gaya / Tipe Liburan <span class="text-red-500">*</span>
                    </label>
                    <select id="aiTipe" name="tipe_liburan" class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                        <option value="santai" selected>🌴 Santai & Healing</option>
                        <option value="petualang">⛰️ Petualang & Outdoor</option>
                        <option value="kuliner">🍜 Kuliner & Hunting Makanan</option>
                        <option value="keluarga">👨‍👩‍👧‍👦 Keluarga & Ramah Anak</option>
                        <option value="hemat">🎒 Backpacker Hemat</option>
                        <option value="mewah">💎 Liburan Mewah & Nyaman</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Budget Anggaran (Opsional)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 font-bold text-slate-400 text-sm pointer-events-none">
                            Rp
                        </span>
                        <input type="number" id="aiAnggaran" name="anggaran" min="0" step="100000"
                               value="{{ $selectedTrip ? (int)$selectedTrip->anggaran : '' }}"
                               placeholder="Contoh: 5000000"
                               class="w-full border border-slate-200 rounded-2xl pl-12 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                    </div>
                </div>

                <div>
                    <button type="submit" id="generateBtn"
                            class="w-full bg-linear-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold py-3.5 px-6 rounded-2xl shadow-md transition transform active:scale-98 flex items-center justify-center gap-2 text-sm md:text-base">
                        <span id="btnIcon">✨</span>
                        <span id="btnText">Generate Itinerary Otomatis Sekarang</span>
                    </button>
                </div>
            </div>

            <!-- Quick destination chips -->
            <div class="pt-2 flex flex-wrap items-center gap-2 text-xs">
                <span class="text-slate-400 font-semibold">Coba cepat:</span>
                @foreach(['Bali', 'Lombok', 'Yogyakarta', 'Tokyo, Jepang', 'Swiss', 'Labuan Bajo'] as $suggested)
                    <button type="button" onclick="setQuickDest('{{ $suggested }}')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-600 transition font-medium">
                        {{ $suggested }}
                    </button>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Loading State (hidden initially) -->
    <div id="aiLoading" class="hidden text-center py-16 bg-white rounded-3xl border border-slate-200/80 shadow-xs mb-10">
        <div class="inline-block animate-spin text-4xl mb-4">🪄</div>
        <h3 class="text-xl font-extrabold text-slate-800">Menyusun Itinerary Terbaik...</h3>
        <p class="text-slate-500 text-sm mt-1">AI sedang menganalisis rute, rekomendasi jam terbaik, dan estimasi biaya.</p>
    </div>

    <!-- AI Output Container (hidden initially) -->
    <div id="aiResult" class="hidden space-y-8">
        <!-- Summary Box -->
        <div class="bg-linear-to-br from-emerald-900 via-teal-900 to-slate-900 text-white p-8 md:p-10 rounded-3xl shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md text-emerald-300 border border-white/10">
                        <span>✨</span> Rekomendasi Rencana AI
                    </span>
                    <span id="resTipeBadge" class="text-xs font-bold bg-emerald-500/20 text-emerald-200 px-3 py-1 rounded-full border border-emerald-400/20"></span>
                </div>
                <h2 id="resTitle" class="text-2xl md:text-4xl font-extrabold tracking-tight"></h2>
                <p id="resSummary" class="text-slate-200 text-sm md:text-base mt-3 leading-relaxed max-w-3xl"></p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 pt-6 border-t border-white/10">
                    <div>
                        <span class="text-xs text-emerald-300 font-semibold block uppercase tracking-wider">Estimasi Total Biaya Kegiatan</span>
                        <span id="resTotalKegiatan" class="text-2xl font-extrabold text-white mt-0.5 block"></span>
                    </div>
                    <div>
                        <span class="text-xs text-emerald-300 font-semibold block uppercase tracking-wider">Saran Total Anggaran (Include Hotel & Transport)</span>
                        <span id="resTotalAnggaran" class="text-2xl font-extrabold text-amber-300 mt-0.5 block"></span>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a id="saveToTripBtn" href="{{ route('trips.create') }}" 
                       class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-lg">
                        <span>💾 Buat Jadi Rencana Liburan Nyata →</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Schedule by Day -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <span>🗓️</span> Jadwal Detail Hari per Hari
                </h3>
                <span id="resDaysCount" class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full"></span>
            </div>

            <div id="resDaysContainer" class="space-y-4"></div>
        </div>

        <!-- Smart Travel Tips -->
        <div class="bg-amber-50/80 border border-amber-200/80 p-6 md:p-8 rounded-3xl">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xl">💡</span>
                <h4 class="font-extrabold text-slate-900 text-base">Tips Pintar dari Asisten AI Triply</h4>
            </div>
            <ul id="resTipsList" class="space-y-2 text-sm text-slate-700"></ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setQuickDest(dest) {
        document.getElementById('aiDestinasi').value = dest;
    }

    document.getElementById('aiTripForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = document.getElementById('generateBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const loading = document.getElementById('aiLoading');
        const result = document.getElementById('aiResult');

        // UI state loading
        submitBtn.disabled = true;
        btnText.innerText = 'AI Sedang Berpikir...';
        btnIcon.innerText = '⏳';
        loading.classList.remove('hidden');
        result.classList.add('hidden');

        try {
            const formData = new FormData(form);
            const response = await fetch("{{ route('ai.generate') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                alert(data.message || 'Gagal menyusun itinerary. Periksa inputan kamu.');
                return;
            }

            renderAiResult(data.data);
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat memproses data AI.');
        } finally {
            submitBtn.disabled = false;
            btnText.innerText = 'Generate Itinerary Otomatis Sekarang';
            btnIcon.innerText = '✨';
            loading.classList.add('hidden');
        }
    });

    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function renderAiResult(data) {
        document.getElementById('resTitle').innerText = `${data.durasi_hari} Hari di ${data.destinasi}`;
        document.getElementById('resTipeBadge').innerText = data.tipe_liburan;
        document.getElementById('resSummary').innerText = data.ringkasan_ai;
        document.getElementById('resTotalKegiatan').innerText = formatRupiah(data.estimasi_total_kegiatan);
        document.getElementById('resTotalAnggaran').innerText = formatRupiah(data.rekomendasi_total_anggaran);
        document.getElementById('resDaysCount').innerText = `${data.durasi_hari} Hari Rencana`;

        // Direct create trip link with prefill parameters
        const saveBtn = document.getElementById('saveToTripBtn');
        saveBtn.href = `{{ route('trips.create') }}?destinasi=${encodeURIComponent(data.destinasi)}&anggaran=${encodeURIComponent(data.rekomendasi_total_anggaran)}`;

        // Days container
        const daysContainer = document.getElementById('resDaysContainer');
        daysContainer.innerHTML = '';

        data.days.forEach(day => {
            const dayCard = document.createElement('div');
            dayCard.className = 'bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs';

            let activitiesHtml = '';
            day.activities.forEach(act => {
                activitiesHtml += `
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[11px]">⏰ ${act.waktu}</span>
                                <span class="font-bold text-slate-800 text-sm">${act.kegiatan}</span>
                            </div>
                            <p class="text-slate-500 flex items-center gap-1 mt-0.5">
                                <span>📍</span> <span>${act.lokasi}</span>
                            </p>
                            ${act.catatan ? `<p class="text-slate-400 italic mt-1">💬 ${act.catatan}</p>` : ''}
                        </div>
                        <div class="shrink-0 text-right sm:text-right">
                            <span class="inline-block px-3 py-1 rounded-xl bg-white border border-slate-200 font-extrabold text-emerald-700">
                                ${formatRupiah(act.estimasi_biaya)}
                            </span>
                        </div>
                    </div>
                `;
            });

            dayCard.innerHTML = `
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center">
                            H${day.hari_ke}
                        </span>
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-sm sm:text-base">${day.judul}</h4>
                            <span class="text-xs text-slate-400 font-semibold">Total Biaya Hari Ini: ${formatRupiah(day.total_estimasi)}</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2.5">
                    ${activitiesHtml}
                </div>
            `;

            daysContainer.appendChild(dayCard);
        });

        // Tips List
        const tipsList = document.getElementById('resTipsList');
        tipsList.innerHTML = '';
        data.tips.forEach(tip => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-2';
            li.innerHTML = `<span class="text-amber-500 font-bold shrink-0">✔</span> <span>${tip}</span>`;
            tipsList.appendChild(li);
        });

        // Show result & scroll into view smoothly
        const resultSection = document.getElementById('aiResult');
        resultSection.classList.remove('hidden');
        resultSection.scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endpush

