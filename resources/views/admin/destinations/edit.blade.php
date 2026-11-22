@extends('layouts.app')

@section('title', 'Edit Destinasi: ' . $destination->nama . ' | Admin Triply')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">
    <div class="mb-8">
        <a href="{{ route('admin.destinations.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:underline mb-2">
            ← Kembali ke Daftar Destinasi
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
            Edit Destinasi: {{ $destination->nama }}
        </h1>
        <p class="text-slate-500 text-sm mt-1">
            Perbarui data informasi destinasi wisata ini.
        </p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-900 px-6 py-4 rounded-2xl mb-8">
            <p class="font-bold text-sm mb-2">Periksa kembali data yang dimasukkan:</p>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.destinations.update', $destination) }}" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmit(this)"
          class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Nama Destinasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama', $destination->nama) }}" required
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nama') border-red-500 @enderror">
                @error('nama')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                @error('slug')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Lokasi Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $destination->lokasi) }}" required
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('lokasi') border-red-500 @enderror">
                @error('lokasi')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Wilayah <span class="text-red-500">*</span>
                </label>
                <select name="wilayah" required
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="domestik" {{ old('wilayah', $destination->wilayah) === 'domestik' ? 'selected' : '' }}>🇮🇩 Domestik (Indonesia)</option>
                    <option value="internasional" {{ old('wilayah', $destination->wilayah) === 'internasional' ? 'selected' : '' }}>✈️ Luar Negeri (Internasional)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="kategori" required
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="pantai" {{ old('kategori', $destination->kategori) === 'pantai' ? 'selected' : '' }}>🏖️ Pantai & Bahari</option>
                    <option value="alam" {{ old('kategori', $destination->kategori) === 'alam' ? 'selected' : '' }}>⛰️ Alam & Gunung</option>
                    <option value="budaya" {{ old('kategori', $destination->kategori) === 'budaya' ? 'selected' : '' }}>🏛️ Budaya & Sejarah</option>
                    <option value="kota" {{ old('kategori', $destination->kategori) === 'kota' ? 'selected' : '' }}>🏙️ Kota & Belanja</option>
                    <option value="religi" {{ old('kategori', $destination->kategori) === 'religi' ? 'selected' : '' }}>🕌 Wisata Religi</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Estimasi Biaya <span class="text-red-500">*</span>
                </label>
                <input type="text" name="estimasi_biaya" value="{{ old('estimasi_biaya', $destination->estimasi_biaya) }}" required
                       placeholder="Contoh: Rp 3.000.000 - Rp 6.000.000 atau 3500000"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('estimasi_biaya')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Rekomendasi Durasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="rekomendasi_durasi" value="{{ old('rekomendasi_durasi', $destination->rekomendasi_durasi) }}" required
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Rating (1.0 - 5.0)
                </label>
                <input type="number" name="rating" value="{{ old('rating', $destination->rating) }}" min="1" max="5" step="0.1" required
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Ganti Foto (Upload Berkas)
                </label>
                <input type="file" name="foto" accept="image/*"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                @error('foto')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengganti file gambar.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Atau Gunakan URL Gambar Eksternal
                </label>
                <input type="text" name="gambar" value="{{ old('gambar', $destination->gambar) }}"
                       placeholder="https://... atau /images/nama-file.jpg"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @if($destination->gambar)
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-slate-400">Preview Saat Ini:</span>
                        <img src="{{ $destination->image_url }}" alt="Preview" class="w-16 h-10 object-cover rounded-lg border border-slate-200">
                    </div>
                @endif
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Deskripsi Lengkap <span class="text-red-500">*</span>
            </label>
            <textarea name="deskripsi" rows="4" required
                      class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi', $destination->deskripsi) }}</textarea>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Highlight / Tempat Populer
                </label>
                <input type="text" name="highlight" value="{{ old('highlight', $destination->highlight) }}"
                       placeholder="Contoh: Spot A, Spot B, Spot C"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Tips Perjalanan
                </label>
                <input type="text" name="tips" value="{{ old('tips', $destination->tips) }}"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $destination->is_popular) ? 'checked' : '' }}
                       class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                <span class="text-sm font-bold text-slate-800">Tampilkan sebagai Destinasi Populer / Unggulan</span>
            </label>
        </div>

        <div class="pt-4 flex items-center gap-3">
            <button type="submit"
                    class="submit-btn bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-6 py-3 rounded-xl transition text-sm shadow-sm flex items-center gap-2">
                <span class="btn-spinner hidden animate-spin">⏳</span>
                <span class="btn-text">Perbarui Destinasi</span>
            </button>
            <a href="{{ route('admin.destinations.index') }}"
               class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

