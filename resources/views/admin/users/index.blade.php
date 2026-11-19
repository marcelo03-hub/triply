@extends('layouts.app')

@section('title', 'Admin - Manajemen Pengguna | Triply')

@section('content')
<div class="max-w-7xl mx-auto px-6 md:px-12 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">
                <span>🛡️ Panel Administrator</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Manajemen Pengguna
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Lihat dan kelola akun pengguna serta hak akses administrator.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.destinations.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition">
                🏕️ Kelola Destinasi
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengguna</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
            <span class="text-xs text-slate-500">Semua akun terdaftar</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Administrator</span>
            <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $stats['admins'] }}</p>
            <span class="text-xs text-slate-500">Memiliki akses penuh</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Traveler Biasa</span>
            <p class="text-2xl font-extrabold text-blue-700 mt-1">{{ $stats['travelers'] }}</p>
            <span class="text-xs text-slate-500">Pengguna reguler</span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-96 flex items-center gap-2">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    🔍
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <button type="submit" class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 transition">
                Cari
            </button>
        </form>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="text-xs font-bold text-slate-400">Role:</span>
            <a href="{{ route('admin.users.index', ['q' => request('q')]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('role') ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'admin', 'q' => request('q')]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('role') === 'admin' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Admin
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'user', 'q' => request('q')]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('role') === 'user' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                User Biasa
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Pengguna</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6">Role</th>
                        <th class="py-4 px-6">Jumlah Trip</th>
                        <th class="py-4 px-6">Terdaftar Sejak</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-bold text-slate-900">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span>{{ $user->name }}</span>
                                        @if(auth()->id() === $user->id)
                                            <span class="ml-1 text-[11px] text-emerald-600 font-semibold">(Anda)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-mono text-xs">
                                {{ $user->email }}
                            </td>
                            <td class="py-4 px-6">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                        👑 Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        👤 Traveler
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-700">
                                {{ $user->trips_count }} Rencana
                            </td>
                            <td class="py-4 px-6 text-slate-500 text-xs">
                                {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if(auth()->id() !== $user->id)
                                    <div class="inline-flex items-center gap-2">
                                        <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg border {{ $user->is_admin ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-purple-200 text-purple-700 hover:bg-purple-50' }} transition">
                                                {{ $user->is_admin ? 'Jadikan User' : 'Jadikan Admin' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus Akun">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Akun aktif saat ini</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                Tidak ada akun pengguna yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

