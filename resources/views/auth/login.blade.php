@extends('layouts.app')

@section('title', 'Masuk ke Akun Anda | Triply')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <span class="inline-block p-3 rounded-2xl bg-emerald-50 text-emerald-700 text-3xl mb-3 shadow-xs">
                🧭
            </span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                Selamat Datang di Triply
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Masuk untuk mengakses dan menyimpan rencana liburanmu.
            </p>
        </div>

        <!-- Demo Account Banner for Quick Testing -->
        <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 mb-6 text-xs text-emerald-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div>
                <p class="font-bold flex items-center gap-1.5 text-emerald-800">
                    <span>💡</span> Akun Demo untuk Pengujian:
                </p>
                <p class="text-emerald-700 mt-0.5">
                    Email: <code class="font-mono bg-emerald-100 px-1 py-0.5 rounded">traveler@triply.id</code> | Pass: <code class="font-mono bg-emerald-100 px-1 py-0.5 rounded">password123</code>
                </p>
            </div>
            <button type="button" onclick="fillDemoAccount()" 
                    class="shrink-0 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg transition text-xs shadow-xs">
                Isi Otomatis
            </button>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-8 sm:p-10">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs font-medium mb-6">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-1.5">
                                <span>⚠️</span> {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5" onsubmit="handleFormSubmit(this)">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </span>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror"
                               required autofocus>
                    </div>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Kata Sandi (Password)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" name="password" id="password"
                               placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('password') border-red-500 @enderror"
                               required>
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <span id="eyeIcon">👁️</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span class="text-slate-600">Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="font-semibold text-emerald-700 hover:underline">
                        Lupa password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="submit-btn w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 rounded-xl shadow-md transition transform active:scale-95 text-sm flex items-center justify-center gap-2">
                    <span class="btn-spinner hidden animate-spin">⏳</span>
                    <span class="btn-text">Masuk ke Akun →</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
                Belum memiliki akun Triply?
                <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:underline ml-1">
                    Daftar Sekarang
                </a>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-slate-600 font-medium">
                ← Kembali ke Halaman Beranda
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillDemoAccount() {
        document.getElementById('email').value = 'traveler@triply.id';
        document.getElementById('password').value = 'password123';
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            eyeIcon.textContent = '👁️';
        }
    }
</script>
@endpush

