@extends('layouts.app')

@section('title', 'Perjalanan Saya | Triply')

@section('content')
<div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full mb-3">
                <span>🧳</span> DAFTAR RENCANA
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                Perjalanan Saya
            </h1>
            <p class="text-slate-600 text-base md:text-lg mt-2 max-w-2xl leading-relaxed">
                Kelola semua rencana liburanmu, cek status keberangkatan, dan pantau pengeluaran perjalanan.
            </p>
        </div>

        <div>
            <a href="{{ route('trips.create') }}" 
               class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3.5 rounded-xl font-bold shadow-md transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Rencanakan Liburan Baru</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <!-- Status Filter Tabs -->
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            <a href="{{ route('trips.index') }}" 
               class="px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition {{ !request('status') ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua ({{ $stats['total'] }})
            </a>
            <a href="{{ route('trips.index', ['status' => 'planned']) }}" 
               class="px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition {{ request('status') === 'planned' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                ⏳ Direncanakan ({{ $stats['planned'] }})
            </a>
            <a href="{{ route('trips.index', ['status' => 'ongoing']) }}" 
               class="px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition {{ request('status') === 'ongoing' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                ✈️ Berlangsung ({{ $stats['ongoing'] }})
            </a>
            <a href="{{ route('trips.index', ['status' => 'completed']) }}" 
               class="px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition {{ request('status') === 'completed' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                ✅ Selesai ({{ $stats['completed'] }})
            </a>
        </div>

        <!-- Search Input Form -->
        <form action="{{ route('trips.index') }}" method="GET" class="relative w-full md:w-80">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau destinasi..."
                   class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
        </form>
    </div>

    <!-- Trips Grid -->
    @if($trips->isEmpty())
        <div class="bg-white rounded-3xl border border-dashed border-slate-300 p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-4xl mb-4">
                🗺️
            </div>
            <h3 class="text-xl font-bold text-slate-900">Belum Ada Rencana Liburan</h3>
            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">
                Mulai petualangan barumu hari ini! Buat rencana perjalanan, pilih destinasi favorit, dan tentukan anggarannya.
            </p>
            <a href="{{ route('trips.create') }}" 
               class="inline-block mt-6 bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-7 py-3 rounded-xl shadow-md transition">
                + Buat Rencana Perjalanan Baru
            </a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($trips as $trip)
                @php
                    $start = \Carbon\Carbon::parse($trip->tanggal_mulai);
                    $end = \Carbon\Carbon::parse($trip->tanggal_selesai);
                    $days = max(1, $start->diffInDays($end) + 1);

                    $badgeStyles = [
                        'planned' => 'bg-orange-100 text-orange-800 border-orange-200',
                        'ongoing' => 'bg-emerald-100 text-emerald-800 border-emerald-200 animate-pulse',
                        'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                    ];
                    $badgeText = [
                        'planned' => '⏳ Direncanakan',
                        'ongoing' => '✈️ Sedang Berjalan',
                        'completed' => '✅ Telah Selesai',
                    ];
                @endphp

                <article class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div class="p-7">
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyles[$trip->status] ?? 'bg-slate-100' }}">
                                {{ $badgeText[$trip->status] ?? ucfirst($trip->status) }}
                            </span>
                            <span class="text-xs font-semibold text-slate-400">
                                {{ $days }} Hari Liburan
                            </span>
                        </div>

                        <h2 class="text-xl font-extrabold text-slate-900 group-hover:text-emerald-700 transition leading-snug mb-2">
                            <a href="{{ route('trips.show', $trip) }}">
                                {{ $trip->nama_perjalanan }}
                            </a>
                        </h2>

                        <div class="flex items-center gap-2 text-sm font-semibold text-emerald-700 mb-4">
                            <span>📍 {{ $trip->destinasi }}</span>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4 space-y-2 text-xs text-slate-600 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Jadwal:</span>
                                <span class="font-bold text-slate-800">{{ $start->format('d M') }} — {{ $end->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Anggaran:</span>
                                <span class="font-extrabold text-emerald-700 text-sm">Rp {{ number_format($trip->anggaran, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($trip->catatan)
                            <p class="text-xs text-slate-500 line-clamp-2 italic">
                                "{{ $trip->catatan }}"
                            </p>
                        @endif
                    </div>

                    <div class="p-7 pt-0 flex items-center gap-2">
                        <a href="{{ route('trips.show', $trip) }}" 
                           class="flex-1 text-center bg-emerald-700 hover:bg-emerald-800 text-white py-2.5 rounded-xl font-bold text-sm shadow-xs transition">
                            Lihat Detail →
                        </a>
                        <a href="{{ route('trips.edit', $trip) }}" 
                           class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $trips->links() }}
        </div>
    @endif
</div>
@endsection

