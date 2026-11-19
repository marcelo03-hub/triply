@extends('layouts.app')

@section('title', 'Admin - Manajemen Destinasi | Triply')

@section('content')
<div class="max-w-7xl mx-auto px-6 md:px-12 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">
                <span>🛡️ Panel Administrator</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Manajemen Destinasi Wisata
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Kelola semua data destinasi wisata domestik dan luar negeri.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition">
                👥 Kelola Pengguna
            </a>
            <a href="{{ route('admin.destinations.create') }}" 
               class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm transition">
                <span>+</span> Tambah Destinasi
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.destinations.index') }}" method="GET" class="w-full md:w-96 flex items-center gap-2">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    🔍
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau lokasi..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            @if(request('wilayah'))
                <input type="hidden" name="wilayah" value="{{ request('wilayah') }}">
            @endif
            <button type="submit" class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 transition">
                Cari
            </button>
        </form>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="text-xs font-bold text-slate-400">Wilayah:</span>
            <a href="{{ route('admin.destinations.index', ['q' => request('q')]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('wilayah') ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.destinations.index', ['wilayah' => 'domestik', 'q' => request('q')]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('wilayah') === 'domestik' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🇮🇩 Domestik
            </a>
            <a href="{{ route('admin.destinations.index', ['wilayah' => 'internasional', 'q' => request('q')]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('wilayah') === 'internasional' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                ✈️ Luar Negeri
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Destinasi</th>
                        <th class="py-4 px-6">Wilayah / Kategori</th>
                        <th class="py-4 px-6">Estimasi Biaya</th>
                        <th class="py-4 px-6">Rating</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($destinations as $dest)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $dest->image_url }}" alt="{{ $dest->nama }}"
                                         class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                                    <div>
                                        <a href="{{ route('destinasi.show', $dest) }}" target="_blank"
                                           class="font-bold text-slate-900 hover:text-emerald-700 transition">
                                            {{ $dest->nama }}
                                        </a>
                                        <p class="text-xs text-slate-400 mt-0.5">📍 {{ $dest->lokasi }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold {{ $dest->wilayah === 'domestik' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ $dest->wilayah === 'domestik' ? '🇮🇩 Domestik' : '✈️ Internasional' }}
                                    </span>
                                    <span class="text-xs text-slate-500 capitalize">🏷️ {{ $dest->kategori }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-700">
                                Rp {{ number_format($dest->estimasi_biaya, 0, ',', '.') }}
                                <span class="block text-[11px] text-slate-400 font-normal">⏱️ {{ $dest->rekomendasi_durasi }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1 font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg text-xs">
                                    ⭐ {{ number_format($dest->rating, 1) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($dest->is_popular)
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                        🔥 Populer
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">Standar</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.destinations.edit', $dest) }}"
                                       class="p-2 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="{{ route('admin.destinations.destroy', $dest) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi {{ $dest->nama }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                Belum ada data destinasi. Klik "+ Tambah Destinasi" untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($destinations->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $destinations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

