@extends('layouts.batu')

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <div
            class="glass-card p-12 text-center max-w-lg w-full border border-yellow-500/30 shadow-[0_0_40px_rgba(234,179,8,0.2)]">

            <div
                class="w-24 h-24 mx-auto bg-yellow-500/20 rounded-full flex items-center justify-center mb-6 animate-pulse">
                <i class="fas fa-history text-4xl text-yellow-400"></i>
            </div>

            <h2 class="text-3xl font-bold text-white mb-4">Request Pending</h2>

            <p class="text-gray-300 mb-8 leading-relaxed">
                Your request to join <span class="text-yellow-400 font-bold">{{ $team->name }}</span> has been sent
                successfully.
                <br><br>
                Please wait for the Team Leader (<span class="text-white">{{ $team->leader->name ?? 'Leader' }}</span>) to
                approve your request.
            </p>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i> Return Home
            </a>
        </div>
    </div>
@endsection