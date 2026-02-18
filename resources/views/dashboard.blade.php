@extends('layouts.batu')

@section('content')
    <div class="p-6">
        <div class="glass-panel p-8 rounded-2xl max-w-4xl mx-auto text-center">
            <h1 class="text-3xl font-bold mb-4 font-amiri text-ramadan-night dark:text-white">Welcome to Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-300 mb-6 font-amiri text-lg">Ramadan Kareem! Manage your projects and
                team here.</p>
            <a href="{{ route('projects.index') }}" class="btn-gold px-6 py-2 rounded-full inline-block font-bold">Go to
                Projects</a>
        </div>
    </div>
@endsection