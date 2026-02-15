@extends('layouts.batu')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold">Welcome to Dashboard</h1>
        <a href="{{ route('projects.index') }}" class="text-blue-600 underline">Go to Projects</a>

    </div>
@endsection
