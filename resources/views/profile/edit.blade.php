@extends('layouts.app')

@section('title', 'Profil & Pengaturan Akun | Triply')

@section('content')
<div class="max-w-4xl mx-auto px-6 md:px-12 py-10">
    <!-- Breadcrumb & Title -->
    <div class="mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:underline mb-2">
            <span>←</span> Kembali ke Beranda
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
            Profil & Pengaturan Akun
        </h1>
        <p class="text-slate-500 text-sm mt-1">
            Kelola data diri, kontak, foto profil, dan kata sandi akun Triply kamu.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <span class="text-lg">✅</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-8">
        <!-- Left: Profile Summary Card -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 text-center">
                <div class="relative w-28 h-28 mx-auto mb-4">
                    <img id="avatarPreview"
                         src="{{ $user->avatar_url }}" 
                         alt="{{ $user->name }}" 
                         class="w-full h-full object-cover rounded-full border-4 border-emerald-100 shadow-xs">
                    <span class="absolute bottom-1 right-1 bg-emerald-600 text-white p-1.5 rounded-full shadow-md text-xs">
                        ✏️
                    </span>
                </div>

                <h2 class="text-lg font-bold text-slate-900 leading-tight">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5 break-all">{{ $user->email }}</p>

                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap justify-center gap-2">
                    @if($user->isAdmin())
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                            🛡️ Administrator
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            🎒 Traveler
                        </span>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 text-left space-y-2 text-xs text-slate-600">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Bergabung:</span>
                        <span class="font-semibold">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Total Perjalanan:</span>
                        <span class="font-semibold text-emerald-700">{{ $user->trips()->count() }} Trip</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Destinasi Favorit:</span>
                        <span class="font-semibold text-emerald-700">{{ $user->favorites()->count() }} Tempat</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Edit Forms (Profile Info & Password) -->
        <div class="md:col-span-2 space-y-8">
            <!-- Form 1: Info Pribadi -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 md:p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span>👤</span> Informasi Pribadi
                </h3>
                <p class="text-xs text-slate-500 mb-6">Perbarui nama lengkap, email, nomor kontak, dan bio perjalananmu.</p>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5" id="profileForm">
                    @csrf
                    @method('PATCH')

                    <!-- Avatar Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Ganti Foto Profil
                        </label>
                        <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1">Maksimal 2 MB (JPG, PNG, atau WEBP).</p>
                        @error('avatar')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Alamat Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telepon -->
                    <div>
                        <label for="telepon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nomor WhatsApp / Telepon
                        </label>
                        <input type="text" id="telepon" name="telepon" value="{{ old('telepon', $user->telepon) }}" placeholder="Contoh: 081234567890"
                               class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold @error('telepon') border-red-400 @enderror">
                        @error('telepon')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Bio Singkat Traveler
                        </label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Ceritakan gaya liburanmu atau destinasi impianmu..."
                                  class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('bio') border-red-400 @enderror">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="saveProfileBtn"
                                class="w-full sm:w-auto bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-6 py-3 rounded-2xl text-sm transition shadow-xs flex items-center justify-center gap-2">
                            <span id="saveProfileText">Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form 2: Ubah Kata Sandi -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 md:p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span>🔒</span> Keamanan & Kata Sandi
                </h3>
                <p class="text-xs text-slate-500 mb-6">Pastikan akun menggunakan kata sandi yang aman dan tidak mudah ditebak.</p>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-5" id="passwordForm">
                    @csrf
                    @method('PUT')

                    <!-- Password Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Kata Sandi Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold @error('current_password') border-red-400 @enderror">
                        @error('current_password')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Kata Sandi Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold @error('password') border-red-400 @enderror">
                        @error('password')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="savePasswordBtn"
                                class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white font-bold px-6 py-3 rounded-2xl text-sm transition shadow-xs flex items-center justify-center gap-2">
                            <span id="savePasswordText">Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live preview avatar before upload
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    avatarPreview.src = evt.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Submit loading state for profile form
    const profileForm = document.getElementById('profileForm');
    const saveProfileBtn = document.getElementById('saveProfileBtn');
    const saveProfileText = document.getElementById('saveProfileText');

    if (profileForm && saveProfileBtn) {
        profileForm.addEventListener('submit', function() {
            saveProfileBtn.disabled = true;
            saveProfileText.innerText = 'Menyimpan Profil...';
        });
    }

    // Submit loading state for password form
    const passwordForm = document.getElementById('passwordForm');
    const savePasswordBtn = document.getElementById('savePasswordBtn');
    const savePasswordText = document.getElementById('savePasswordText');

    if (passwordForm && savePasswordBtn) {
        passwordForm.addEventListener('submit', function() {
            savePasswordBtn.disabled = true;
            savePasswordText.innerText = 'Memperbarui Sandi...';
        });
    }
</script>
@endpush
