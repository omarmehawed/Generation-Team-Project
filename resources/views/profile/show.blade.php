@extends('layouts.batu')

@section('content')
    <div class="max-w-7xl mx-auto py-8">

        {{-- Join Request Answers (Specific Emails Only) --}}
        @if(auth()->check() && in_array(auth()->user()->email, ['2420823@batechu.com', '2420324@batechu.com']) && $user->joinRequest)
            <div x-data="{ open: false }" class="mt-8 bg-gray-900 rounded-3xl p-6 border border-gray-800 shadow-2xl">
                <button @click="open = !open" class="w-full flex justify-between items-center text-left focus:outline-none">
                    <h3 class="text-xl font-bold text-white font-tech flex items-center gap-3">
                        <i class="fas fa-file-contract text-cyan-400"></i> Join Request Answers
                    </h3>
                    <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" style="color: var(--text-muted)"></i>
                </button>
                
                <div x-show="open" x-collapse class="mt-6 text-gray-300 space-y-4 border-t border-gray-800 pt-6">
                     @php $answers = $user->joinRequest->answers; @endphp
                     @if($answers)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($answers as $key => $value)
                                <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
                                    <h4 class="text-cyan-400 font-bold mb-2 text-sm uppercase tracking-wider">{{ ucwords(str_replace('_', ' ', $key)) }}</h4>
                                    @if(is_array($value))
                                        <ul class="list-disc list-inside space-y-1 text-sm">
                                            @foreach($value as $subKey => $subValue)
                                                <li>
                                                    @if(!is_numeric($subKey))
                                                        <strong class="text-gray-400">{{ ucwords(str_replace('_', ' ', $subKey)) }}:</strong> 
                                                    @endif
                                                    {{ is_array($subValue) ? json_encode($subValue) : $subValue }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm font-medium">{{ $value }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No answers available.</p>
                    @endif
                </div>
            </div>
        @endif

    </div> {{-- End Max Width Container --}}

    {{-- Modals (Keep existing modals if any) --}}
    @include('profile.partials.wallet-modal') 


        @php
            $managedTeam = null;
            if(auth()->check() && auth()->id() != $user->id) {
                $managedTeam = \App\Models\Team::where('leader_id', auth()->id())
                    ->whereHas('members', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->first();
            }
            $isLeaderOfStudent = $managedTeam ? true : false;
        @endphp

        {{-- 1. User Info Section --}}
        <div class="bg-gray-900 rounded-3xl p-8 mb-12 relative overflow-hidden shadow-2xl border border-gray-800">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(rgba(0, 243, 255, 0.2) 1px, transparent 1px); background-size: 30px 30px;">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                {{-- Profile Photo --}}
                <div class="relative group">
                    <form action="{{ route('profile.update_details') }}" method="POST" enctype="multipart/form-data" id="profile-photo-form">
                        @csrf
                        <input type="file" name="profile_photo" id="profile-photo-input" class="hidden" accept="image/*" onchange="document.getElementById('profile-photo-form').submit()">
                        
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-cyan-500/30 shadow-[0_0_20px_rgba(6,182,212,0.3)] relative cursor-pointer"
                             onclick="document.getElementById('profile-photo-input').click()">
                            
                            {{-- Image --}}
                            @if($user->profile_photo_path)
                                <img src="{{ Str::startsWith($user->profile_photo_path, ['http://', 'https://']) ? $user->profile_photo_path : asset('storage/' . $user->profile_photo_path) }}" 
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=000&color=00f3ff&bold=true&size=128';"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=000&color=00f3ff&bold=true&size=128"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @endif

                            {{-- Overlay --}}
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white">
                                <i class="fas fa-camera text-2xl mb-1 text-cyan-400"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider">Edit</span>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- User Details & Form --}}
                <div class="flex-1 w-full">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-4xl font-bold text-white font-tech mb-1 tracking-wider">{{ $user->name }}</h1>
                            <p class="text-cyan-400 font-mono text-sm mb-2">{{ $user->email }}</p>
                            
                            {{-- Wallet Balance Badge --}}
                            <div class="inline-flex items-center gap-2 bg-gray-800/50 border border-gray-700 rounded-full px-3 py-1">
                                <i class="fas fa-wallet text-green-400 text-xs"></i>
                                <span class="text-gray-300 text-xs font-bold">Balance:</span>
                                <span class="text-green-400 text-sm font-black">{{ number_format($user->wallet_balance, 2) }}</span>
                                <span class="text-gray-500 text-[10px] font-bold">L.E</span>
                            </div>
                        </div>
                        
                        {{-- Edit/Cancel Buttons REMOVED --}}

                        {{-- Leader Actions --}}
                        @if($isLeaderOfStudent)
                            <div class="flex gap-2">
                                <button onclick="openManageModal()" 
                                    class="bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-500 border border-yellow-500/30 px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 hover:shadow-lg hover:shadow-yellow-500/10">
                                    <i class="fas fa-user-cog"></i> Manage Role
                                </button>
                                <button onclick="openWalletModal()" 
                                    class="bg-green-500/10 hover:bg-green-500/20 text-green-500 border border-green-500/30 px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 hover:shadow-lg hover:shadow-green-500/10">
                                    <i class="fas fa-wallet"></i> Add to Wallet
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Static Read-Only Data Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 bg-gray-800/30 p-6 rounded-2xl border border-gray-700/50">
                        
                        {{-- Phone --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Phone Number</label>
                            <div class="flex items-center gap-2 text-gray-300 font-medium">
                                <i class="fas fa-phone text-blue-500 text-sm"></i>
                                {{ $user->phone_number ?? 'Not set' }}
                            </div>
                        </div>

                        {{-- WhatsApp --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">WhatsApp</label>
                            <div class="flex items-center gap-2 text-gray-300 font-medium">
                                <i class="fab fa-whatsapp text-green-500 text-sm"></i>
                                {{ $user->whatsapp_number ?? 'Not set' }}
                            </div>
                        </div>

                        {{-- National ID --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">National ID</label>
                            <div class="flex items-center gap-2 text-gray-300 font-medium">
                                <i class="fas fa-id-card text-purple-500 text-sm"></i>
                                {{ $user->national_id ?? 'Not set' }}
                            </div>
                        </div>

                        {{-- Date of Birth --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Date of Birth</label>
                            <div class="flex items-center gap-2 text-gray-300 font-medium">
                                <i class="fas fa-birthday-cake text-pink-500 text-sm"></i>
                                {{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d M, Y') : 'Not set' }}
                            </div>
                        </div>

                        {{-- Academic Year --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Academic Year</label>
                            <div class="flex items-center gap-2 text-gray-300 font-medium">
                                <i class="fas fa-university text-yellow-500 text-sm"></i>
                                {{ 'Level ' . ($user->academic_year ?? '1') }}
                            </div>
                        </div>

                        {{-- Department --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Department</label>
                            <div class="flex items-center gap-2 text-gray-300 font-medium">
                                <i class="fas fa-laptop-code text-cyan-500 text-sm"></i>
                                {{ ucfirst($user->department ?? 'General') }}
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Home Address</label>
                            <div class="flex items-start gap-2 text-gray-300 font-medium">
                                <i class="fas fa-map-marker-alt text-red-500 text-sm mt-1"></i>
                                {{ $user->address ?? 'Not set' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🆕 Weekly Evaluation Section (Leaders Only) --}}
        @if($isLeaderOfStudent)
            @include('profile.partials.weekly-evaluation-card')
            
            {{-- Permission Management Modal --}}
            @include('profile.partials.manage-role-modal', ['team' => $managedTeam])
            
            {{-- Wallet Management Modal --}}
            @include('profile.partials.wallet-modal', ['user' => $user])
        @endif

        {{-- 🚀 Big Separator --}}
        <div class="flex items-center justify-center my-12">
            <div class="h-px bg-gradient-to-r from-transparent via-cyan-500 to-transparent w-full max-w-2xl opacity-50">
            </div>
            <span class="px-4 text-cyan-500 font-tech text-xl font-bold uppercase tracking-[0.2em] whitespace-nowrap">
                Start Your Journey
            </span>
            <div class="h-px bg-gradient-to-r from-transparent via-cyan-500 to-transparent w-full max-w-2xl opacity-50">
            </div>
        </div>


        {{-- 2. Graduation Project Section --}}
        @if($gradProjectData)
            @include('profile.partials.project-card', [
                'data' => $gradProjectData,
                'type' => 'graduation',
                'color' => 'purple'
            ])
        @else
            <div class="mb-12 p-6 bg-gray-900/50 rounded-2xl border border-gray-800 text-center">
                <i class="fas fa-graduation-cap text-4xl text-gray-700 mb-2"></i>
                <h3 class="text-gray-400 font-bold">No Graduation Project</h3>
                <p class="text-gray-600 text-sm">Join a team to start your journey.</p>
            </div>
        @endif


        {{-- 3. Subject Projects Section --}}
        <div class="space-y-6">
            @forelse($subjectProjectsData as $data)
                @include('profile.partials.project-card', [
                    'data' => $data,
                    'type' => 'subject',
                    'color' => 'blue'
                ])
            @empty
                <div class="text-center py-12 bg-gray-900 rounded-3xl border border-gray-800">
                    <i class="fas fa-layer-group text-5xl text-gray-700 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-400">No Subject Projects Found</h3>
                    <p class="text-gray-600">Join a team to start tracking your progress.</p>
                </div>
            @endforelse
        </div>

    </div>
@endsection