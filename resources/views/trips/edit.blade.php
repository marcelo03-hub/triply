@extends('layouts.app')

@section('title', 'Edit ' . $trip->nama_perjalanan . ' | Triply')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12">
    <div class="mb-8">
        <a href="{{ route('trips.show', $trip) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:underline mb-3">
            <span>←</span> Kembali ke Detail Rencana
        </a>
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
            Edit Rencana Liburan
        </h1>
        <p class="text-slate-600 text-sm md:text-base mt-1">
            Perbarui informasi perjalanan, anggaran, tanggal, atau status kegiatan liburanmu.
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

    <form action="{{ route('trips.update', $trip) }}" method="POST" onsubmit="handleFormSubmit(this)"
          class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-8 md:p-10 space-y-6">
        @csrf
        @method('PUT')

        <!-- Nama Perjalanan -->
        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Nama Perjalanan <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_perjalanan" 
                   value="{{ old('nama_perjalanan', $trip->nama_perjalanan) }}"
                   placeholder="Contoh: Liburan Akhir Tahun ke Bali..."
                   class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('nama_perjalanan') border-red-500 @enderror"
                   required>
            @error('nama_perjalanan')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Destinasi -->
        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Destinasi Tujuan <span class="text-red-500">*</span>
            </label>
            <input type="text" name="destinasi" id="destinasiInput"
                   value="{{ old('destinasi', $trip->destinasi) }}"
                   placeholder="Ketik destinasi tujuanmu..."
                   class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('destinasi') border-red-500 @enderror"
                   required>
            @error('destinasi')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            
            @if(isset($destinations) && $destinations->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-1.5 items-center">
                    <span class="text-xs font-bold text-slate-400">Rekomendasi:</span>
                    @foreach($destinations->take(8) as $d)
                        <button type="button" onclick="document.getElementById('destinasiInput').value = '{{ $d->nama }}'"
                                class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 text-slate-700 transition">
                            {{ $d->nama }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Status Perjalanan <span class="text-red-500">*</span>
            </label>
            <select name="status" class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('status') border-red-500 @enderror">
                <option value="planned" {{ old('status', $trip->status) === 'planned' ? 'selected' : '' }}>⏳ Direncanakan (Belum Berangkat)</option>
                <option value="ongoing" {{ old('status', $trip->status) === 'ongoing' ? 'selected' : '' }}>✈️ Sedang Berlangsung</option>
                <option value="completed" {{ old('status', $trip->status) === 'completed' ? 'selected' : '' }}>✅ Telah Selesai</option>
            </select>
            @error('status')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal Keberangkatan & Kepulangan -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Tanggal Mulai / Berangkat <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_mulai" 
                       value="{{ old('tanggal_mulai', \Carbon\Carbon::parse($trip->tanggal_mulai)->format('Y-m-d')) }}"
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
                <input type="date" name="tanggal_selesai" 
                       value="{{ old('tanggal_selesai', \Carbon\Carbon::parse($trip->tanggal_selesai)->format('Y-m-d')) }}"
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
                       value="{{ old('anggaran', (int) $trip->anggaran) }}"
                       class="w-full border border-slate-300 rounded-xl pl-12 pr-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm @error('anggaran') border-red-500 @enderror"
                       required>
            </div>
            @error('anggaran')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Catatan Perjalanan -->
        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Catatan & Rencana Kegiatan (Opsional)
            </label>
            <textarea name="catatan" rows="4"
                      class="w-full border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">{{ old('catatan', $trip->catatan) }}</textarea>
            @error('catatan')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-4 space-y-3">
            <button type="submit"
                    class="submit-btn w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 text-base flex items-center justify-center gap-2">
                <span class="btn-spinner hidden animate-spin">⏳</span>
                <span class="btn-text">Perbarui Rencana Perjalanan →</span>
            </button>
            <a href="{{ route('trips.show', $trip) }}" 
               class="block text-center text-sm font-semibold text-slate-500 hover:text-slate-800 py-2">
                Batal & Kembali
            </a>
        </div>
    </form>
</div>
@endsection
