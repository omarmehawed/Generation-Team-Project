@extends('layouts.batu')

@section('content')
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        {{-- Animated Background --}}
        <div class="absolute inset-0 pointer-events-none">
            <div
                class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[100px] animate-pulse">
            </div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-cyan-600/20 rounded-full blur-[100px] animate-pulse"
                style="animation-delay: 2s"></div>
        </div>

        <div class="relative z-10 max-w-4xl w-full mx-4">

            <div class="text-center mb-12">
                <h1
                    class="text-5xl md:text-7xl font-bold font-tech text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-500 mb-4 drop-shadow-lg">
                    {{ $project->title }}
                </h1>
                <p class="text-xl text-gray-400 font-light tracking-widest uppercase">Graduation Project Workspace</p>
            </div>

            {{-- Team Card --}}
            <div
                class="glass-card p-8 md:p-12 text-center transform transition-all hover:scale-[1.01] duration-500 border border-white/10 hover:border-cyan-500/30">

                <div class="mb-8">
                    @if($team->logo)
                        <img src="{{ route('final_project.logo', $team->id) }}" alt="Team Logo"
                            class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-cyan-500/50 shadow-[0_0_30px_rgba(0,243,255,0.3)]">
                    @else
                        <div
                            class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-gray-800 to-black flex items-center justify-center border-4 border-gray-700 shadow-xl">
                            <i class="fas fa-users text-4xl text-gray-500"></i>
                        </div>
                    @endif
                </div>

                <h2 class="text-3xl font-bold text-white mb-2">{{ $team->name }}</h2>
                <div class="flex items-center justify-center gap-2 mb-6">
                    <span
                        class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 text-sm border border-purple-500/30">
                        <i class="fas fa-user-circle mr-1"></i> Leader: {{ $team->leader->name ?? 'Unknown' }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-sm border border-blue-500/30">
                        <i class="fas fa-users mr-1"></i> {{ $team->members->count() }} Members
                    </span>
                </div>

                <p class="text-gray-300 mb-8 max-w-2xl mx-auto leading-relaxed">
                    {{ $team->proposal_description ?? 'This is the official workspace for the graduation project team. Join to access tasks, budget, timeline, and collaboration tools.' }}
                </p>

                <form action="{{ route('final_project.join') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id }}">
                    <input type="hidden" name="code" value="{{ $team->code }}"> {{-- Still passing code for validation but
                    hidden --}}
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <button type="submit"
                        class="group relative px-8 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-xl font-bold text-white text-lg shadow-[0_0_20px_rgba(0,243,255,0.4)] hover:shadow-[0_0_40px_rgba(0,243,255,0.6)] transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                        </div>
                        <span class="relative flex items-center gap-3">
                            Join Team Space <i
                                class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </button>
                </form>

                <p class="mt-6 text-sm text-gray-500">
                    * Your request will be sent to the team leader for approval.
                </p>

            </div>
        </div>
    </div>
@endsection