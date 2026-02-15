@extends('layouts.batu')

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <div class="glass-card p-12 max-w-xl w-full">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Initialize System</h1>
                <p class="text-gray-400">Set up the single graduation team for everyone.</p>
            </div>

            <form action="{{ route('final_project.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-300 mb-2">Team Name</label>
                    <input type="text" name="name"
                        class="w-full bg-black/50 border border-gray-700 rounded-lg p-3 text-white focus:border-cyan-500 focus:outline-none"
                        placeholder="e.g. Generation Team" required>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg font-bold text-white shadow-lg hover:shadow-emerald-500/30 transition-all">
                    Create Team & Initialize
                </button>
            </form>
        </div>
    </div>
@endsection