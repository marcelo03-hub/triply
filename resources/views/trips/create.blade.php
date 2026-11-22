@extends('layouts.app')

@section('title', 'Rencanakan Perjalanan Baru | Triply')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12">
    <div class="mb-8">
        <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:underline mb-3">
            <span>←</span> Kembali ke Perjalanan Saya
        </a>
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
            Rencanakan Perjalanan Baru
        </h1>
        <p class="text-slate-600 text-sm md:text-base mt-1">
            Susun rencana liburan <strong>Domestik</strong> maupun <strong>Luar Negeri</strong> dengan mudah.
        </p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-900 px-6 py-4 rounded-2xl mb-8">
            <p class="font-bold text-sm mb-2">Terjadi kesalahan pada input:</p>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('trips.store') }}" method="POST" onsubmit="handleFormSubmit(this)"
          class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-8 md:p-10 space-y-6">
        @csrf

        <!-- Nama Perjalanan -->
        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Nama Perjalanan <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_perjalanan" 
                   value="{{ old('nama_perjalanan', $destinasi ? 'Liburan ke ' . $destinasi : '') }}"
                   placeholder="Contoh: Liburan Musim Dingin ke Jepang, Eksplorasi Bali..."
                   class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('nama_perjalanan') border-red-500 @enderror"
                   required>
            @error('nama_perjalanan')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-slate-400 mt-1.5">Berikan nama unik yang berkesan untuk liburanmu.</p>
        </div>

        <!-- Destinasi -->
        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Destinasi Tujuan <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="text" name="destinasi" id="destinasiInput"
                       value="{{ old('destinasi', $destinasi) }}"
                       placeholder="Ketik destinasi kota/negara atau klik rekomendasi di bawah..."
                       class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('destinasi') border-red-500 @enderror"
                       required>
            </div>
            @error('destinasi')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            
            <!-- Quick Destination Tags from DB -->
            <div class="mt-4 space-y-2">
                @if(isset($destinations) && $destinations->isNotEmpty())
                    @php
                        $domestik = $destinations->where('wilayah', 'domestik');
                        $internasional = $destinations->where('wilayah', 'internasional');
                    @endphp
                    @if($domestik->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 items-center">
                            <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md">🇮🇩 Domestik:</span>
                            @foreach($domestik->take(7) as $d)
                                <button type="button" onclick="selectQuickDest('{{ $d->nama }}')"
                                        class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-700 transition">
                                    {{ $d->nama }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @if($internasional->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 items-center">
                            <span class="text-xs font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded-md">✈️ Luar Negeri:</span>
                            @foreach($internasional->take(7) as $d)
                                <button type="button" onclick="selectQuickDest('{{ $d->nama }}')"
                                        class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 hover:bg-blue-100 hover:text-blue-800 text-slate-700 transition">
                                    {{ $d->nama }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="flex flex-wrap gap-1.5 items-center">
                        <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md">🇮🇩 Domestik:</span>
                        @foreach(['Bali', 'Yogyakarta', 'Bandung', 'Lombok', 'Labuan Bajo', 'Borobudur'] as $d)
                            <button type="button" onclick="selectQuickDest('{{ $d }}')"
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-700 transition">
                                {{ $d }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Tanggal Keberangkatan & Kepulangan -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Tanggal Mulai / Berangkat <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                       value="{{ old('tanggal_mulai', date('Y-m-d', strtotime('+7 days'))) }}"
                       class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('tanggal_mulai') border-red-500 @enderror"
                       required>
                @error('tanggal_mulai')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Tanggal Selesai / Pulang <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                       value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+12 days'))) }}"
                       class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('tanggal_selesai') border-red-500 @enderror"
                       required>
                @error('tanggal_selesai')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Anggaran -->
        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Estimasi Anggaran (Rupiah) <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 font-bold text-slate-400 text-sm pointer-events-none">
                    Rp
                </span>
                <input type="number" name="anggaran" min="0" step="100000"
                       value="{{ old('anggaran', request('anggaran', 5000000)) }}"
                       placeholder="Contoh: 7500000"
                       class="w-full border border-slate-300 rounded-xl pl-12 pr-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('anggaran') border-red-500 @enderror"
                       required>
            </div>
            @error('anggaran')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-slate-400 mt-1.5">
                Hitung estimasi otomatis untuk domestik / luar negeri di <a href="{{ route('anggaran') }}" target="_blank" class="text-emerald-700 font-bold hover:underline">Kalkulator Anggaran ↗</a>
            </p>
        </div>

        <!-- Catatan Perjalanan -->
        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Catatan & Rencana Kegiatan (Opsional)
            </label>
            <textarea name="catatan" rows="4"
                      placeholder="Contoh: Tiket pesawat sudah dipesan, paspor aktif, beli e-SIM, daftar tempat belanja & kuliner favorit..."
                      class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">{{ old('catatan') }}</textarea>
            @error('catatan')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-4 space-y-3">
            <button type="submit"
                    class="submit-btn w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 text-base flex items-center justify-center gap-2">
                <span class="btn-spinner hidden animate-spin">⏳</span>
                <span class="btn-text">Simpan & Buat Rencana Perjalanan →</span>
            </button>
            <a href="{{ route('trips.index') }}" 
               class="block text-center text-sm font-semibold text-slate-500 hover:text-slate-800 py-2">
                Batal & Kembali
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function selectQuickDest(destName) {
        document.getElementById('destinasiInput').value = destName;
        const namaInput = document.querySelector('input[name="nama_perjalanan"]');
        if (!namaInput.value || namaInput.value.startsWith('Liburan')) {
            namaInput.value = 'Liburan ke ' + destName;
        }
    }
</script>
@endpush
