@extends('layouts.batu')

@section('content')
    <div class="max-w-7xl mx-auto py-8">

        {{-- Join Request Answers (Specific Emails Only) --}}
        @if(auth()->check() && in_array(auth()->user()->email, ['2420823@batechu.com', '2420324@batechu.com']) && $user->joinRequest)
            <div x-data="{ open: false }" class="mt-8 bg-white dark:bg-gray-900 rounded-3xl p-6 border border-gray-200 dark:border-gray-800 shadow-xl dark:shadow-2xl">
                <button @click="open = !open" class="w-full flex justify-between items-center text-left focus:outline-none">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white font-tech flex items-center gap-3">
                        <i class="fas fa-file-contract text-cyan-400"></i> Join Request Answers
                    </h3>
                    <i class="fas transition-transform duration-300 text-gray-500 dark:text-gray-400" :class="open ? 'fa-chevron-up rotate-180' : 'fa-chevron-down'"></i>
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
        <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 mb-12 relative overflow-hidden shadow-xl dark:shadow-2xl border border-gray-200 dark:border-gray-800">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(rgba(0, 243, 255, 0.2) 1px, transparent 1px); background-size: 30px 30px;">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                {{-- Profile Photo --}}
                <div class="relative group w-32 h-32 rounded-full overflow-hidden border-4 border-cyan-500/30 shadow-[0_0_20px_rgba(6,182,212,0.3)]">
                    
                    {{-- Form is NO LONGER wrapping the entire div, so we can split click zones --}}
                    <form action="{{ route('profile.update_details') }}" method="POST" enctype="multipart/form-data" id="profile-photo-form">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="file" name="profile_photo" id="profile-photo-input" class="hidden" accept="image/*" onchange="document.getElementById('profile-photo-form').submit()">
                    </form>

                    {{-- Image Display --}}
                    @php
                        $avatarUrl = $user->profile_photo_path ?: "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&background=000&color=00f3ff&bold=true&size=512";
                    @endphp
                    <img id="profile-img-preview" src="{{ $avatarUrl }}" 
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=000&color=00f3ff&bold=true&size=512';"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                    {{-- Split Overlay Content --}}
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex text-white divide-x divide-white/20">
                        {{-- 1. View Button (Left Half) --}}
                        <div onclick="openPhotoLightbox()" class="flex-1 flex flex-col items-center justify-center hover:bg-white/10 cursor-pointer transition-colors backdrop-blur-sm" title="View Full Photo">
                            <i class="fas fa-search-plus text-xl mb-1 text-white group-hover:animate-pulse"></i>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-white/90">View</span>
                        </div>
                        
                        {{-- 2. Edit Button (Right Half) --}}
                        <div onclick="document.getElementById('profile-photo-input').click()" class="flex-1 flex flex-col items-center justify-center hover:bg-cyan-500/20 cursor-pointer transition-colors backdrop-blur-sm" title="Upload New Photo">
                            <i class="fas fa-camera text-xl mb-1 text-cyan-400 group-hover:animate-bounce"></i>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-cyan-300">Edit</span>
                        </div>
                    </div>
                </div>

                {{-- User Details & Form --}}
                <div class="flex-1 w-full">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900 dark:text-white font-tech mb-1 tracking-wider">{{ $user->name }}</h1>
                            <p class="text-cyan-600 dark:text-cyan-400 font-mono text-sm mb-2">{{ $user->email }}</p>
                            
                            {{-- Wallet Balance Badge --}}
                            <div class="inline-flex items-center gap-2 bg-gray-800/50 border border-gray-700 rounded-full px-3 py-1">
                                <i class="fas fa-wallet text-green-400 text-xs"></i>
                                <span class="text-gray-300 text-xs font-bold">Balance:</span>
                                <span class="text-green-400 text-sm font-black">{{ number_format($user->wallet_balance, 2) }}</span>
                                <span class="text-gray-500 text-[10px] font-bold">L.E</span>
                            </div>
                        </div>
                        
                        {{-- Edit/Cancel Buttons REMOVED --}}

                        {{-- User Actions --}}
                        @if(auth()->id() === $user->id)
                            <div class="flex flex-col gap-2 mb-2 w-full max-w-sm mt-4">
                                @if($user->google_id)
                                    <div class="flex flex-col bg-white dark:bg-gray-800 border-l-4 border-green-500 border-y border-r border-y-gray-200 border-r-gray-200 dark:border-y-gray-700 dark:border-r-gray-700 rounded-lg p-4 shadow-sm w-full">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-6 h-6">
                                                <div>
                                                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Google Account</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $user->google_email ?? 'Linked' }}</p>
                                                </div>
                                            </div>
                                            <span class="bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 text-[10px] font-bold px-2 py-1 rounded-full border border-green-200 dark:border-green-500/30">Connected</span>
                                        </div>
                                        
                                        <form action="{{ route('auth.google.unlink') }}" method="POST" class="w-full flex justify-end m-0" onsubmit="return confirm('Are you sure you want to unlink your Google account? You will no longer be able to log in with it.');">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors flex items-center gap-1">
                                                <i class="fas fa-unlink"></i> Unlink Google Account
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="flex items-center justify-between bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm w-full">
                                        <div class="flex items-center gap-3">
                                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-6 h-6 opacity-50 grayscale">
                                            <div>
                                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Google Account</p>
                                                <span class="bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 text-[10px] font-bold px-2 py-1 rounded-full border border-gray-200 dark:border-gray-600">Not connected</span>
                                            </div>
                                        </div>
                                        
                                        <a href="{{ route('auth.google') }}" class="inline-flex items-center gap-2 bg-amber-50 hover:bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 rounded-lg px-4 py-2 shadow-sm transition-colors cursor-pointer text-sm font-bold">
                                            <i class="fas fa-link"></i> Link with Google
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 bg-gray-50 dark:bg-gray-800/30 p-6 rounded-2xl border border-gray-200 dark:border-gray-700/50">
                        
                        {{-- Phone --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Phone Number</label>
                            <div class="flex items-center gap-2 text-gray-800 dark:text-gray-300 font-medium">
                                <i class="fas fa-phone text-blue-500 text-sm"></i>
                                {{ $user->phone_number ?? 'Not set' }}
                            </div>
                        </div>

                        {{-- WhatsApp --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">WhatsApp</label>
                            <div class="flex items-center gap-2 text-gray-800 dark:text-gray-300 font-medium">
                                <i class="fab fa-whatsapp text-green-500 text-sm"></i>
                                {{ $user->whatsapp_number ?? 'Not set' }}
                            </div>
                        </div>

                        {{-- National ID --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">National ID</label>
                            <div class="flex items-center gap-2 text-gray-800 dark:text-gray-300 font-medium">
                                <i class="fas fa-id-card text-purple-500 text-sm"></i>
                                {{ $user->national_id ?? 'Not set' }}
                            </div>
                        </div>

                        {{-- Date of Birth --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Date of Birth</label>
                            <div class="flex items-center gap-2 text-gray-800 dark:text-gray-300 font-medium">
                                <i class="fas fa-birthday-cake text-pink-500 text-sm"></i>
                                {{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d M, Y') : 'Not set' }}
                            </div>
                        </div>

                        {{-- Academic Year --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Academic Year</label>
                            <div class="flex items-center gap-2 text-gray-800 dark:text-gray-300 font-medium">
                                <i class="fas fa-university text-yellow-500 text-sm"></i>
                                {{ 'Level ' . ($user->academic_year ?? '1') }}
                            </div>
                        </div>

                        {{-- Department --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Department</label>
                            <div class="flex items-center gap-2 text-gray-800 dark:text-gray-300 font-medium">
                                <i class="fas fa-laptop-code text-cyan-500 text-sm"></i>
                                {{ ucfirst($user->department ?? 'General') }}
                            </div>
                        </div>

                        {{-- Residence Status --}}
                        <div>
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Residence Status</label>
                            <div class="flex items-center gap-2 text-gray-800 dark:text-gray-300 font-medium">
                                @if($user->is_dorm)
                                    مغترب
                                @else
                                    غير مغترب
                                @endif
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-gray-500 text-[10px] uppercase font-bold mb-1 tracking-wider">Home Address</label>
                            <div class="flex items-start gap-2 text-gray-800 dark:text-gray-300 font-medium">
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
            <div class="mb-12 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
                <i class="fas fa-graduation-cap text-4xl text-gray-400 dark:text-gray-700 mb-2"></i>
                <h3 class="text-gray-700 dark:text-gray-400 font-bold">No Graduation Project</h3>
                <p class="text-gray-500 dark:text-gray-600 text-sm">Join a team to start your journey.</p>
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

    {{-- 📸 Profile Photo Lightbox Modal --}}
    <div id="photoLightboxModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 backdrop-blur-md transition-opacity opacity-0">
        {{-- Close Button --}}
        <button onclick="closePhotoLightbox()" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-3 rounded-full flex items-center justify-center backdrop-blur-sm shadow-xl z-50">
            <i class="fas fa-times text-2xl"></i>
        </button>

        {{-- Image Container --}}
        <div class="relative max-w-4xl max-h-[90vh] mx-4 overflow-hidden rounded-2xl shadow-2xl shadow-cyan-500/20 transform scale-95 transition-transform duration-300" id="lightboxContent">
            <img src="{{ $avatarUrl }}" alt="Profile Photo" id="lightboxImage" class="max-w-full max-h-[90vh] object-contain cursor-zoom-out" onclick="closePhotoLightbox()">
            
            {{-- Name Banner --}}
            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent p-6 text-center pointer-events-none">
                <h3 class="text-white font-tech tracking-wider text-xl">{{ $user->name }}</h3>
                <p class="text-cyan-400 font-mono text-xs">{{ $user->email }}</p>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openPhotoLightbox() {
        const modal = document.getElementById('photoLightboxModal');
        const content = document.getElementById('lightboxContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Trigger reflow for animation
        void modal.offsetWidth;
        
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    function closePhotoLightbox() {
        const modal = document.getElementById('photoLightboxModal');
        const content = document.getElementById('lightboxContent');
        
        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        
        // Restore body scroll
        document.body.style.overflow = 'auto';
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300); // Wait for transition
    }

    // Close on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape" && !document.getElementById('photoLightboxModal').classList.contains('hidden')) {
            closePhotoLightbox();
        }
    });
</script>
@endsection