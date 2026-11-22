@extends('layouts.app')

@section('title', 'Lupa Kata Sandi | Triply')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <span class="inline-block p-3 rounded-2xl bg-emerald-50 text-emerald-700 text-3xl mb-3 shadow-xs">
                🔑
            </span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                Lupa Kata Sandi?
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Masukkan alamat email akunmu. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-8 sm:p-10">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold mb-6">
                    {{ session('status') }}
                </div>
            @endif

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

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5" onsubmit="handleFormSubmit(this)">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Alamat Email
                    </label>
                    <input type="email" name="email" id="email" 
                           value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror"
                           required autofocus>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="submit-btn w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition transform active:scale-98 text-sm flex items-center justify-center gap-2">
                    <span class="btn-spinner hidden animate-spin">⏳</span>
                    <span class="btn-text">Kirim Tautan Reset Password</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
                Ingat kata sandi Anda? 
                <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:underline">
                    Masuk di sini
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
