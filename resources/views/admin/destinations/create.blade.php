@extends('layouts.app')

@section('title', 'Tambah Destinasi Baru | Admin Triply')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">
    <div class="mb-8">
        <a href="{{ route('admin.destinations.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:underline mb-2">
            ← Kembali ke Daftar Destinasi
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
            Tambah Destinasi Baru
        </h1>
        <p class="text-slate-500 text-sm mt-1">
            Tambahkan destinasi wisata baru agar dapat dipilih dan dijelajahi oleh pengguna.
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

    <form action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmit(this)"
          class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-8 space-y-6">
        @csrf

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Nama Destinasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                       placeholder="Contoh: Pulau Nusa Penida"
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
                <input type="text" name="lokasi" value="{{ old('lokasi') }}" required
                       placeholder="Contoh: Klungkung, Bali, Indonesia"
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
                    <option value="domestik" {{ old('wilayah') === 'domestik' ? 'selected' : '' }}>🇮🇩 Domestik (Indonesia)</option>
                    <option value="internasional" {{ old('wilayah') === 'internasional' ? 'selected' : '' }}>✈️ Luar Negeri (Internasional)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="kategori" required
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="pantai" {{ old('kategori') === 'pantai' ? 'selected' : '' }}>🏖️ Pantai & Bahari</option>
                    <option value="alam" {{ old('kategori') === 'alam' ? 'selected' : '' }}>⛰️ Alam & Gunung</option>
                    <option value="budaya" {{ old('kategori') === 'budaya' ? 'selected' : '' }}>🏛️ Budaya & Sejarah</option>
                    <option value="kota" {{ old('kategori') === 'kota' ? 'selected' : '' }}>🏙️ Kota & Belanja</option>
                    <option value="religi" {{ old('kategori') === 'religi' ? 'selected' : '' }}>🕌 Wisata Religi</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Estimasi Biaya <span class="text-red-500">*</span>
                </label>
                <input type="text" name="estimasi_biaya" value="{{ old('estimasi_biaya', 'Rp 2.500.000 - Rp 5.000.000') }}" required
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
                <input type="text" name="rekomendasi_durasi" value="{{ old('rekomendasi_durasi', '3 - 5 Hari') }}" required
                       placeholder="Contoh: 3 - 4 Hari"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Rating (1.0 - 5.0)
                </label>
                <input type="number" name="rating" value="{{ old('rating', '4.8') }}" min="1" max="5" step="0.1" required
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Upload Berkas Foto (Lokal / Gambar)
                </label>
                <input type="file" name="foto" accept="image/*"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                @error('foto')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 4MB).</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Atau Gunakan URL Gambar Eksternal
                </label>
                <input type="text" name="gambar" value="{{ old('gambar') }}"
                       placeholder="https://images.unsplash.com/... atau /images/nama-file.jpg"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-slate-400 mt-1">Kosongkan jika ingin memakai foto default acak pemandangan alam.</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
                Deskripsi Lengkap <span class="text-red-500">*</span>
            </label>
            <textarea name="deskripsi" rows="4" required
                      placeholder="Jelaskan daya tarik, keindahan, serta informasi penting mengenai destinasi ini..."
                      class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Highlight / Tempat Populer (Pisahkan dengan koma)
                </label>
                <input type="text" name="highlight" value="{{ old('highlight') }}"
                       placeholder="Contoh: Pantai Kelingking, Broken Beach, Crystal Bay"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">
                    Tips Perjalanan
                </label>
                <input type="text" name="tips" value="{{ old('tips') }}"
                       placeholder="Contoh: Sewa motor, bawa tabir surya dan uang tunai secukupnya"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}
                       class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                <span class="text-sm font-bold text-slate-800">Tampilkan sebagai Destinasi Populer / Unggulan</span>
            </label>
        </div>

        <div class="pt-4 flex items-center gap-3">
            <button type="submit"
                    class="submit-btn bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-6 py-3 rounded-xl transition text-sm shadow-sm flex items-center gap-2">
                <span class="btn-spinner hidden animate-spin">⏳</span>
                <span class="btn-text">Simpan Destinasi Baru</span>
            </button>
            <a href="{{ route('admin.destinations.index') }}"
               class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

