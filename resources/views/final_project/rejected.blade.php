@extends('layouts.batu')

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <div
            class="glass-card p-12 text-center max-w-lg w-full border border-red-500/30 shadow-[0_0_40px_rgba(239,68,68,0.2)]">

            <div class="w-24 h-24 mx-auto bg-red-500/20 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-ban text-4xl text-red-500"></i>
            </div>

            <h2 class="text-3xl font-bold text-white mb-4">Access Denied</h2>

            <p class="text-gray-300 mb-8 leading-relaxed">
                Your request to join <span class="text-red-400 font-bold">{{ $team->name }}</span> was declined by the Team
                Leader.
                <br><br>
                If you believe this is a mistake, please contact the leader directly.
            </p>

            <a href="{{ route('dashboard') }}"
                class="px-6 py-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg text-white transition-colors">
                Return Home
            </a>
        </div>
    </div>
@endsection