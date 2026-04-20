@extends('layouts.batu')

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <div
            class="glass-card p-12 text-center max-w-lg w-full border border-red-500/30 shadow-[0_0_40px_rgba(239,68,68,0.2)]">

            <div
                class="w-24 h-24 mx-auto bg-red-500/20 rounded-full flex items-center justify-center mb-6 animate-bounce">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
            </div>

            <h2 class="text-3xl font-bold text-white mb-4">Access Restricted</h2>

            <p class="text-gray-300 mb-8 leading-relaxed">
                {{ $message }}
            </p>

            <div class="flex flex-col gap-4">
                <a href="{{ route('dashboard') }}"
                    class="btn-primary py-3 px-6 rounded-xl font-bold text-white transition-all transform hover:scale-105">
                    Return to Home
                </a>
                
                <a href="javascript:history.back()"
                    class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Go Back
                </a>
            </div>
        </div>
    </div>
@endsection
