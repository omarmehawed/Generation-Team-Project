@extends('layouts.batu')

@section('title', 'Complete Profile')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white/10 dark:bg-gray-900/40 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl relative overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>

        <div class="relative z-10">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900/50 mb-6 border-2 border-indigo-200 shadow-inner">
                    <i class="fas fa-user-check text-2xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Complete Your Profile</h2>
                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 font-medium">
                    Please provide your gender to continue. This is a one-time required step.
                </p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('profile.complete.store') }}" method="POST">
                @csrf
                
                {{-- [FUTURE FIELDS placeholder]
                     To add more required fields (e.g. Academic Number, Department, etc.),
                     simply add the input groups below this block. The UI is designed
                     to scale vertically and look great with additional form controls.
                --}}

                <div class="space-y-4">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                        Select Gender <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Male Option -->
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="gender" value="male" class="peer sr-only" required>
                            <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 text-center">
                                <i class="fas fa-male text-3xl mb-2 text-gray-400 group-hover:text-indigo-500 peer-checked:text-indigo-600 transition-colors"></i>
                                <div class="font-bold text-gray-700 dark:text-gray-300 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300">Male</div>
                            </div>
                        </label>

                        <!-- Female Option -->
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="gender" value="female" class="peer sr-only" required>
                            <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all peer-checked:border-pink-500 peer-checked:bg-pink-50 dark:peer-checked:bg-pink-900/30 text-center">
                                <i class="fas fa-female text-3xl mb-2 text-gray-400 group-hover:text-pink-500 peer-checked:text-pink-600 transition-colors"></i>
                                <div class="font-bold text-gray-700 dark:text-gray-300 peer-checked:text-pink-700 dark:peer-checked:text-pink-300">Female</div>
                            </div>
                        </label>
                    </div>
                    @error('gender')
                        <p class="mt-2 text-xs text-red-500 font-bold"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-lg hover:shadow-indigo-500/30">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-arrow-right text-indigo-400 group-hover:text-indigo-300 transition-colors"></i>
                        </span>
                        Save and Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
