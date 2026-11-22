@extends('layouts.app')

@section('title', 'Atur Ulang Kata Sandi | Triply')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <span class="inline-block p-3 rounded-2xl bg-emerald-50 text-emerald-700 text-3xl mb-3 shadow-xs">
                🔒
            </span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                Reset Kata Sandi
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Silakan buat kata sandi baru untuk akun Triply Anda.
            </p>
        </div>

        <!-- Form Card -->
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

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5" onsubmit="handleFormSubmit(this)">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Alamat Email
                    </label>
                    <input type="email" name="email" id="email" 
                           value="{{ old('email', $email) }}"
                           placeholder="nama@email.com"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror"
                           required autofocus>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Kata Sandi Baru
                    </label>
                    <input type="password" name="password" id="password"
                           placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('password') border-red-500 @enderror"
                           required>
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Konfirmasi Kata Sandi Baru
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           placeholder="Ketik ulang kata sandi baru"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           required>
                </div>

                <button type="submit" class="submit-btn w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition transform active:scale-98 text-sm flex items-center justify-center gap-2">
                    <span class="btn-spinner hidden animate-spin">⏳</span>
                    <span class="btn-text">Perbarui Kata Sandi</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
