{{--
==========================================================================
👑 ROYAL MODALS SYSTEM V5.1 - ULTIMATE PRO EDITION
-----------------------------------------------------------------------
Project: Borg El Arab Technological University (Graduation Project)
Developer: Omar Mehawed - Lead Developer
Architecture: High-End Glassmorphism & Atomic Design Patterns
Version: 5.1 (Refactored for Performance & Scalability)
Status: PRODUCTION READY & DOCTOR REVIEW APPROVED
==========================================================================
--}}

{{-- ========================================================= --}}
{{-- 🎨 CORE STYLES: The Engine of Beauty & Animations --}}
{{-- ========================================================= --}}
<style>
    /* ---------------------------------------------------------
       1. GLOBAL RESET & LAYOUT STRATEGY
    --------------------------------------------------------- */
    #royal-modals-container {
        position: relative;
        z-index: 999999 !important;
        font-family: 'Inter', 'Cairo', system-ui, -apple-system, sans-serif;
    }

    body.modal-open-lock {
        overflow: hidden !important;
        padding-right: 15px;
        /* Prevent layout shift on Windows */
    }

    /* ---------------------------------------------------------
       2. CINEMATIC OVERLAY (Glass Effect)
    --------------------------------------------------------- */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        /* Deep Slate */
        backdrop-filter: blur(12px);
        /* High-end blur */
        -webkit-backdrop-filter: blur(12px);
        transition: opacity 0.3s ease-out;
        z-index: -1;
    }

    .modal-centering-wrapper {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        overflow-y: auto;
        z-index: 999999;
        perspective: 1000px;
        /* Enables 3D transforms */
    }

    /* ---------------------------------------------------------
       3. THE MODAL CARD (The Royal Container)
    --------------------------------------------------------- */
    .modal-content {
        position: relative;
        background: #ffffff;
        width: 100%;
        max-width: 32rem;
        border-radius: 1.5rem !important;
        box-shadow:
            0 25px 50px -12px rgba(0, 0, 0, 0.35),
            0 0 0 1px rgba(255, 255, 255, 0.1);
        transform: scale(0.92) translateY(30px);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        /* Professional Pop Elasticity */
        margin: auto;
        overflow: hidden;
        backface-visibility: hidden;
    }

    /* Active State (Triggered by JS) */
    .modal-content.active {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    /* ---------------------------------------------------------
       4. BRANDING & COLORS (Royal Gold Theme)
    --------------------------------------------------------- */
    .border-royal-gold {
        border-color: #D4AF37;
    }

    .bg-royal-gold {
        background-color: #D4AF37;
    }

    .text-royal-gold {
        color: #D4AF37;
    }

    .modal-header-gradient {
        background: linear-gradient(135deg, #1f2937 0%, #0f172a 100%);
        position: relative;
    }

    /* ---------------------------------------------------------
       5. INPUTS: High Definition Fields
    --------------------------------------------------------- */
    .input-classic {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background-color: #f9fafb;
        font-weight: 600;
        color: #1f2937;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .input-classic:focus {
        border-color: #D4AF37;
        background-color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.15);
        transform: translateY(-1px);
    }

    .input-classic::placeholder {
        color: #9ca3af;
        font-weight: 500;
    }

    /* ---------------------------------------------------------
       6. BUTTONS: Interactive Elements
    --------------------------------------------------------- */
    .btn-modal-primary {
        background: linear-gradient(135deg, #1f2937 0%, #000000 100%);
        color: #D4AF37;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-modal-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
        background: #D4AF37;
        color: #111827;
        border-color: #D4AF37;
    }

    .btn-modal-primary:active {
        transform: translateY(0);
    }

    .btn-cancel {
        color: #6b7280;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 0 1rem;
        transition: color 0.2s;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .btn-cancel:hover {
        color: #1f2937;
    }

    /* ---------------------------------------------------------
       7. UTILITIES & ANIMATIONS
    --------------------------------------------------------- */
    /* Custom Scrollbar */
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Shimmer Effect (Gold Shine) */
    .shimmer-icon {
        animation: shimmer 3s infinite linear;
    }

    @keyframes shimmer {
        0% {
            filter: drop-shadow(0 0 0 transparent);
        }

        50% {
            filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.6));
            transform: scale(1.1);
        }

        100% {
            filter: drop-shadow(0 0 0 transparent);
        }
    }

    /* Ripple Click Effect */
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
</style>

<div id="royal-modals-container">

    {{--
    ####################################################################
    # #
    # 🏛️ SECTION 1: TEAM FORMATION & ACCESS (Onboarding) #
    # Logic: Creation, Joining, Invitations, Leaving #
    # #
    ####################################################################
    --}}

    {{-- 1. Create Team Modal --}}
    {{-- مكتبات القص --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    {{-- ========================================== --}}
    {{-- 1. مودال الإنشاء (Create Team) --}}
    {{-- ========================================== --}}
    <div id="createTeamModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('createTeamModal')"></div>
            <div class="modal-content border-t-4 border-royal-gold">
                <form action="{{ route('final_project.store') }}" method="POST">
                    @csrf
                    @if (isset($project) && $project)
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                    @endif

                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex items-center gap-6 mb-6">
                            {{-- منطقة اللوجو --}}
                            <div class="relative group cursor-pointer w-24 h-24">
                                <img id="createLogoPreview"
                                    src="https://ui-avatars.com/api/?name=New+Team&background=000&color=D4AF37&size=256"
                                    class="w-full h-full rounded-full object-cover border-4 border-gray-100 shadow-md">

                                <label for="createRawInput"
                                    class="absolute bottom-0 right-0 bg-gray-900 text-[#D4AF37] w-8 h-8 rounded-full flex items-center justify-center shadow-lg border-2 border-white hover:scale-110 transition cursor-pointer z-10">
                                    <i class="fas fa-camera text-xs"></i>
                                </label>

                                {{-- Input الملف والبيانات --}}
                                <input type="file" id="createRawInput" accept="image/*" class="hidden"
                                    onchange="initCropper(this, 'create')">
                                <input type="hidden" name="team_logo_base64" id="createBase64">
                            </div>

                            <div class="flex-1">
                                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Create Team</h3>
                                <p class="text-xs text-gray-500 font-medium">Upload badge & name your squad.</p>
                                <p class="text-xs text-red-500 font-medium">The Team Name Cannot Changed.</p>
                            </div>
                        </div>

                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide ml-1">Team
                            Name</label>
                        <input type="text" name="name" required placeholder="e.g. Alpha Operations"
                            class="input-classic"
                            oninput="if(!document.getElementById('createBase64').value) document.getElementById('createLogoPreview').src = `https://ui-avatars.com/api/?name=${this.value}&background=000&color=D4AF37&size=256`">
                    </div>

                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="btn-modal-primary ripple-btn">Initialize Team</button>
                        <button type="button" onclick="closeModal('createTeamModal')" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 2. مودال التعديل (Edit Logo) --}}
    {{-- ========================================== --}}
    @if (isset($team) && isset($myRole) && $myRole == 'leader')
        <div id="editLogoModal" class="hidden relative z-[9999]" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="modal-centering-wrapper">
                <div class="modal-overlay" onclick="closeModal('editLogoModal')"></div>
                <div class="modal-content border-t-4 border-royal-gold w-full max-w-lg">
                    <form action="{{ route('final_project.update_logo') }}" method="POST">
                        @csrf
                        <input type="hidden" name="team_id" value="{{ $team->id }}">

                        <div class="bg-white px-6 py-6">
                            <h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-edit text-[#D4AF37]"></i> Update Team Badge
                            </h3>

                            <div class="flex flex-col items-center space-y-4">
                                <label
                                    class="block w-full cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-[#D4AF37] hover:bg-yellow-50 transition">
                                    <span class="text-gray-500 text-sm font-bold block mb-1">Click to choose
                                        image</span>
                                    <input type="file" id="editRawInput" accept="image/*" class="hidden"
                                        onchange="initCropper(this, 'edit')" />
                                </label>
                                <input type="hidden" name="team_logo_base64" id="editBase64">
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" id="editSaveBtn"
                                class="btn-royal-gold py-2 px-6 rounded-xl font-bold shadow-md hidden">Save
                                Changes</button>
                            <button type="button" onclick="closeModal('editLogoModal')" class="btn-cancel">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- 3. مودال القص المشترك (Shared Cropper) --}}
    {{-- ========================================== --}}
    <div id="sharedCropperModal"
        class="fixed inset-0 z-[100000] hidden bg-black/90 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white rounded-2xl overflow-hidden shadow-2xl w-full max-w-lg">
            <div class="h-[400px] bg-black flex justify-center items-center">
                <img id="sharedImageToCrop" class="max-w-full max-h-full block">
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="closeSharedCropper()" class="btn-cancel">Cancel</button>
                <button type="button" id="sharedCropBtn"
                    class="bg-[#D4AF37] text-black px-6 py-2 rounded-xl font-bold">Crop & Save</button>
            </div>
        </div>
    </div>

    {{-- سكريبت الجافاسكريبت الموحد --}}
    <script>
        let cropperInstance;
        let currentMode = ''; // 'create' or 'edit'

        function initCropper(input, mode) {
            if (input.files && input.files[0]) {
                currentMode = mode;
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('sharedImageToCrop').src = e.target.result;
                    const modal = document.getElementById('sharedCropperModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    if (cropperInstance) cropperInstance.destroy();
                    cropperInstance = new Cropper(document.getElementById('sharedImageToCrop'), {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                    });
                };
                reader.readAsDataURL(input.files[0]);
                input.value = ''; // Reset input
            }
        }

        document.getElementById('sharedCropBtn').addEventListener('click', function () {
            if (!cropperInstance) return;
            const canvas = cropperInstance.getCroppedCanvas({
                width: 300,
                height: 300
            });
            const base64 = canvas.toDataURL('image/png');

            if (currentMode === 'create') {
                document.getElementById('createLogoPreview').src = base64;
                document.getElementById('createBase64').value = base64;
            } else if (currentMode === 'edit') {
                document.getElementById('editBase64').value = base64;
                document.getElementById('editSaveBtn').classList.remove('hidden'); // Show save button
                // Optionally update a preview in edit modal if you had one
            }

            closeSharedCropper();
        });

        function closeSharedCropper() {
            document.getElementById('sharedCropperModal').classList.add('hidden');
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }
        }
    </script>

    {{-- 2. Join Team Modal --}}
    <div id="joinTeamModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('joinTeamModal')"></div>
            <div class="modal-content">
                <form action="{{ route('final_project.join') }}" method="POST">
                    @csrf
                    @if (isset($project) && $project)
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                    @endif
                    <div class="bg-white px-8 pt-10 pb-8 text-center">
                        <div
                            class="w-20 h-20 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-[#C5A059] shadow-xl relative">
                            <div class="absolute inset-0 rounded-full border border-white/20"></div>
                            <i class="fas fa-key text-[#C5A059] text-3xl shimmer-icon"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 mb-2">Access Portal</h3>
                        <p class="text-gray-500 text-sm mb-8 font-medium">Enter the 6-character secure access code.</p>

                        <div class="relative max-w-[280px] mx-auto">
                            <input type="text" name="code" required placeholder="X7K9M2" maxlength="6"
                                class="w-full border-2 border-gray-200 rounded-2xl p-4 text-center text-4xl font-mono font-bold uppercase tracking-[0.3em] text-gray-800 focus:border-[#C5A059] focus:ring-4 focus:ring-yellow-500/10 outline-none transition-all placeholder-gray-200 shadow-inner">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="btn-modal-primary w-full ripple-btn">Authenticate &
                            Join</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 3. Invite Member Modal --}}
    <div id="inviteMemberModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('inviteMemberModal')"></div>
            <div class="modal-content">
                <form action="{{ route('final_project.invite') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <div class="p-2.5 bg-blue-50 rounded-xl text-blue-600"><i class="fas fa-paper-plane"></i>
                            </div>
                            Send Invitation
                        </h3>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide ml-1">Student
                            Email</label>
                        <input type="email" name="email" required placeholder="2xxxxxx@batechu.com"
                            class="input-classic">
                    </div>
                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="btn-modal-primary ripple-btn">Add Member</button>
                        <button type="button" onclick="closeModal('inviteMemberModal')"
                            class="btn-cancel">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 4. Leave Team Modal --}}
    {{-- 4. Leave Team Modal (Updated for Leader Logic) --}}
    <div id="leaveTeamModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('leaveTeamModal')"></div>
            <div class="modal-content border-t-4 border-red-600 !border-t-red-600">
                <form action="{{ route('final_project.leave') }}" method="POST">
                    @csrf
                    {{-- تأكد أن المتغير $team متاح هنا، أو استخدم $myTeam حسب الكود عندك --}}
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex items-center gap-4 text-red-600 mb-4">
                            <div class="p-3 bg-red-50 rounded-full animate-pulse">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Confirm Disengagement</h3>
                        </div>

                        {{-- 🔥 الجزء الجديد: فحص الليدر --}}
                        {{-- بنفترض إنك بتبعت للمودال متغير اسمه $memberRecord أو بتعمل check على العضو الحالي --}}
                        @php
                            $currentUserRole = \App\Models\TeamMember::where('team_id', $team->id ?? 0)
                                ->where('user_id', auth()->id())
                                ->value('role');
                            $membersCount = \App\Models\TeamMember::where('team_id', $team->id ?? 0)->count();
                        @endphp

                        @if ($currentUserRole == 'leader' && $membersCount > 1)
                            <div class="bg-red-50 p-4 rounded-xl border border-red-100 mb-4">
                                <p class="text-red-800 font-bold text-sm mb-2">⚠️ You are the Team Leader</p>
                                <p class="text-red-600 text-xs">You must appoint a new leader from the list below
                                    before leaving.</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Select New Leader:</label>
                                <select name="new_leader_id" required
                                    class="w-full border border-gray-300 p-3 rounded-xl text-sm bg-white focus:ring-red-500 focus:border-red-500 font-bold">
                                    <option value="" disabled selected>-- Select a member --</option>
                                    @foreach ($team->members as $member)
                                        @if ($member->user_id != auth()->id())
                                            <option value="{{ $member->user_id }}">{{ $member->user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @else
                            {{-- الرسالة العادية للأعضاء أو الليدر الوحيد --}}
                            <p class="text-gray-600 text-sm ml-1 leading-relaxed font-medium">
                                Are you sure you want to leave this team? <br>
                                <span class="text-red-500 text-xs">⚠️ This action cannot be undone and you will lose
                                    access.</span>
                            </p>
                        @endif

                    </div>

                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="bg-red-600 text-white font-bold py-3 px-6 rounded-xl text-xs uppercase tracking-wider hover:bg-red-700 transition shadow-lg ripple-btn hover:shadow-red-500/30">
                            Leave Team
                        </button>
                        <button type="button" onclick="closeModal('leaveTeamModal')" class="btn-cancel">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{--
    ####################################################################
    # #
    # 👔 SECTION 2: MANAGEMENT & ADMINISTRATIVE #
    # Logic: Roles, Permissions, Reporting, Proposals #
    # #
    ####################################################################
    --}}

    {{-- 5. Manage Member Modal --}}
    <div id="manageMemberModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('manageMemberModal')"></div>
            <div class="modal-content border-t-8 border-[#D4AF37] !border-t-[#D4AF37]">
                <form action="{{ route('final_project.updateMember') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">
                    <input type="hidden" name="user_id" id="manageUserId">

                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex items-center gap-3 mb-6 text-[#AA8A26]">
                            <div class="p-2 bg-[#FFF8E1] rounded-full"><i class="fas fa-user-cog text-xl"></i></div>
                            <h3 class="text-xl font-black text-gray-900">Manage Roles</h3>
                        </div>

                        <div
                            class="text-sm text-gray-500 mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200 flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i>
                            <span>Editing for: <span id="manageUserName" class="font-bold text-gray-900"></span></span>
                        </div>

                        {{-- Roles --}}
                        <div class="mb-5">
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Administrative
                                Role</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="member" class="peer hidden" checked>
                                    <div
                                        class="p-3 rounded-xl border-2 border-gray-100 text-center peer-checked:border-[#D4AF37] peer-checked:bg-[#FFF8E1] transition-all hover:bg-gray-50">
                                        <p class="text-sm font-bold text-gray-600 peer-checked:text-[#AA8A26]">Member
                                            👤</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="vice_leader" class="peer hidden">
                                    <div
                                        class="p-3 rounded-xl border-2 border-gray-100 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all hover:bg-gray-50">
                                        <p class="text-sm font-bold text-gray-600 peer-checked:text-purple-700">Vice
                                            Head 🎖️</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Technical Team --}}
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Technical
                                Assignment</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="technical_role" value="general" class="peer hidden"
                                        checked>
                                    <div
                                        class="p-2.5 rounded-xl border border-gray-200 text-center peer-checked:bg-gray-800 peer-checked:text-white transition text-xs font-bold shadow-sm peer-checked:border-gray-800">
                                        General</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="technical_role" value="software" class="peer hidden">
                                    <div
                                        class="p-2.5 rounded-xl border border-gray-200 text-center peer-checked:bg-blue-600 peer-checked:text-white transition text-xs font-bold shadow-sm peer-checked:border-blue-600">
                                        Software 💻</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="technical_role" value="hardware" class="peer hidden">
                                    <div
                                        class="p-2.5 rounded-xl border border-gray-200 text-center peer-checked:bg-orange-500 peer-checked:text-white transition text-xs font-bold shadow-sm peer-checked:border-orange-500">
                                        Hardware 🔌</div>
                                </label>
                            </div>
                        </div>

                        {{-- Extra --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Extra
                                Responsibility</label>
                            <select name="extra_role" id="manageExtraRole"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 text-sm focus:ring-[#D4AF37] focus:border-[#D4AF37] outline-none transition bg-white font-semibold text-gray-700">
                                <option value="none">None</option>
                                <option value="presentation">🎤 Presentation Master</option>
                                <option value="reports">📝 Weekly Reports</option>
                                <option value="marketing">📢 Marketing & Media</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit"
                            class="btn-modal-primary py-2 px-6 rounded-xl font-bold shadow-md transition transform hover:-translate-y-0.5 ripple-btn">Save
                            Changes</button>
                        <button type="button" onclick="closeModal('manageMemberModal')"
                            class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 6. Report Member Modal --}}
    <div id="reportMemberModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('reportMemberModal')"></div>
            <div class="modal-content overflow-hidden">
                <div
                    class="px-6 py-5 flex justify-between items-center bg-gradient-to-r from-red-700 to-red-900 rounded-t-[1.3rem]">
                    <h3 class="text-white font-bold text-lg flex items-center gap-2"><i class="fas fa-gavel"></i>
                        Report Member</h3>
                    <button onclick="closeModal('reportMemberModal')"
                        class="text-white/70 hover:text-white transition transform hover:rotate-90">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('final_project.reportMember') }}" method="POST" class="p-6 bg-white">
                    @csrf
                    <input type="hidden" name="reported_user_id" id="reported_user_id_input">

                    <p
                        class="text-sm text-gray-600 mb-5 bg-red-50 p-4 rounded-xl border border-red-100 flex gap-2 items-start">
                        <i class="fas fa-info-circle text-red-500 mt-0.5"></i>
                        <span>You are filing a formal complaint against <span id="reported_member_name"
                                class="font-bold text-red-800"></span>. This will be reviewed by supervisors.</span>
                    </p>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Complaint / Issue
                            Detail</label>
                        <textarea name="complaint" rows="5" required class="input-classic resize-none"
                            placeholder="Please describe the issue in detail..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeModal('reportMemberModal')"
                            class="text-gray-500 font-bold text-xs uppercase px-4 py-3 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider px-6 py-3 rounded-xl shadow-md ripple-btn hover:shadow-red-500/30">Submit
                            Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 7. Submit Proposal Modal --}}
    <div id="proposalModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('proposalModal')"></div>
            <div class="modal-content !max-w-xl">
                {{-- Header --}}
                <div class="modal-header-gradient px-8 py-6 relative overflow-hidden rounded-t-[1.3rem]">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#D4AF37]/20 rounded-full blur-2xl -mr-6 -mt-6">
                    </div>
                    <h3 class="text-2xl font-black text-white flex items-center gap-3 relative z-10">
                        <i class="fas fa-lightbulb text-[#FFD700] shimmer-icon"></i> Submit Proposal
                    </h3>
                    <p class="text-gray-400 text-xs mt-1 relative z-10 pl-9">Present your graduation project idea for
                        official approval.</p>
                </div>

                <form action="{{ route('final_project.submitProposal') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div class="bg-white px-8 pt-6 pb-6 space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Project
                                Title</label>
                            <input type="text" name="proposal_title" required placeholder="e.g. Smart Traffic System AI"
                                class="input-classic">
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description
                                & Objectives</label>
                            <textarea name="proposal_description" required rows="4"
                                placeholder="Describe your project idea, goals, and technologies used..."
                                class="input-classic resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Proposal
                                Document (PDF)</label>
                            <div class="relative group">
                                <input type="file" name="proposal_file" required accept=".pdf,.doc,.docx,.pptx"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#D4AF37]/10 file:text-[#AA8A26] hover:file:bg-[#D4AF37]/20 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-[#D4AF37]/50 transition bg-gray-50 h-14 pt-1.5">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 flex justify-between">
                                <span>Max size: 1GB.</span>
                                <span>Formats: PDF, DOCX, PPTX.</span>
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="btn-modal-primary ripple-btn">Submit Proposal <i
                                class="fas fa-paper-plane ml-2"></i></button>
                        <button type="button" onclick="closeModal('proposalModal')" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{--
    ####################################################################
    # #
    # 💰 SECTION 3: FINANCIALS & BUDGETING #
    # Logic: Expenses, Fund Requests, Payments, History #
    # #
    ####################################################################
    --}}

    {{-- 8. Add Expense Modal --}}
    <div id="addExpenseModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('addExpenseModal')"></div>
            <div class="modal-content">
                <form action="{{ route('final_project.storeExpense') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div class="bg-white px-8 pt-8 pb-6">
                        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <div class="p-2.5 bg-green-100 rounded-xl text-green-600 shadow-sm"><i
                                    class="fas fa-receipt"></i></div>
                            Add New Expense
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Item
                                    Name</label>
                                <input type="text" name="item" required placeholder="e.g. Raspberry Pi 4"
                                    class="input-classic">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Shop
                                    Name</label>
                                <input type="text" name="shop_name" required placeholder="Store Name"
                                    class="input-classic">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Amount
                                        (EGP)</label>
                                    <input type="number" name="amount" required step="0.01"
                                        class="w-full border-2 border-gray-200 bg-gray-50 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white outline-none transition font-mono text-lg font-bold">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Receipt
                                        Image</label>
                                    <input type="file" name="receipt" accept="image/*"
                                        class="w-full text-xs text-gray-500 border border-dashed border-gray-300 rounded-xl p-3 hover:bg-gray-50 cursor-pointer transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold text-xs uppercase tracking-wider py-3 px-6 rounded-xl shadow-md transition ripple-btn">Save
                            Record</button>
                        <button type="button" onclick="closeModal('addExpenseModal')" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 9. Create Fund Request Modal --}}
    <div id="createFundModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('createFundModal')"></div>
            <div class="modal-content border border-yellow-500/30">
                <form action="{{ route('final_project.storeFund') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div class="modal-header-gradient px-8 py-6 rounded-t-[1.3rem]">
                        <h3 class="text-xl font-black text-[#FFD700] flex items-center gap-3">
                            <i class="fas fa-hand-holding-usd shimmer-icon"></i> Request Funds
                        </h3>
                        <p class="text-gray-400 text-xs mt-1 pl-8">Set a budget target and track payments from members.
                        </p>
                    </div>

                    <div class="bg-white px-8 pt-6 pb-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Title /
                                Reason</label>
                            <input type="text" name="title" required placeholder="e.g. Buying Motors & Sensors"
                                class="input-classic">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Amount
                                    Per Member</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-400 font-bold">EGP</span>
                                    <input type="number" name="amount_per_member" required step="0.01" placeholder="200"
                                        class="w-full border-2 border-gray-100 bg-gray-50 p-3 pl-12 rounded-xl focus:ring-0 focus:border-[#D4AF37] focus:bg-white transition font-mono text-lg font-black text-gray-800 outline-none">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deadline</label>
                                <input type="date" name="deadline" required
                                    class="w-full border-2 border-gray-100 bg-gray-50 p-3 rounded-xl focus:ring-0 focus:border-[#D4AF37] focus:bg-white transition text-sm font-bold text-gray-700 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="btn-modal-primary ripple-btn">Start Collecting</button>
                        <button type="button" onclick="closeModal('createFundModal')" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    {{-- 11. Funds History Modal --}}
    {{-- 11. Funds History Modal --}}
    {{-- 11. Funds History Modal (Updated with Payment Proofs) --}}
    <div id="fundsHistoryModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('fundsHistoryModal')"></div>
            <div class="modal-content !max-w-2xl flex flex-col max-h-[85vh]">
                <div class="modal-header-gradient px-8 py-6 rounded-t-[1.3rem] flex-shrink-0">
                    <h3 class="text-xl font-black text-[#FFD700] flex items-center gap-3"><i class="fas fa-history"></i>
                        Funds History</h3>
                </div>

                <div class="bg-white p-6 overflow-y-auto custom-scroll flex-grow">
                    @if (isset($fundsHistory) && $fundsHistory->count() > 0)
                        <div class="space-y-6">
                            @foreach ($fundsHistory as $fund)
                                <div
                                    class="border border-gray-200 rounded-2xl p-5 hover:border-yellow-200 transition hover:shadow-md bg-gray-50/30">
                                    <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-2">
                                        <div>
                                            <h4 class="font-bold text-lg text-gray-800">{{ $fund->title }}</h4>
                                            <span class="text-xs text-gray-400 font-medium"><i
                                                    class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($fund->created_at)->format('d M, Y') }}</span>
                                        </div>
                                        <span
                                            class="bg-yellow-50 text-[#AA8A26] px-3 py-1 rounded-lg text-xs font-bold font-mono shadow-sm">{{ $fund->amount_per_member }}
                                            EGP</span>
                                    </div>

                                    @if ($fund->contributions && $fund->contributions->count() > 0)
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach ($fund->contributions as $contrib)
                                                <div
                                                    class="flex justify-between items-center text-xs p-2.5 rounded-lg {{ $contrib->status == 'paid' ? 'bg-green-50/50 border border-green-100' : 'bg-red-50/50 border border-red-100' }}">

                                                    {{-- اسم العضو والصورة --}}
                                                    <span class="font-bold text-gray-700 flex items-center gap-2">
                                                        <img src="https://ui-avatars.com/api/?name={{ $contrib->user->name }}&background=random&size=24"
                                                            class="rounded-full w-5 h-5">
                                                        {{ $contrib->user->name }}
                                                    </span>

                                                    {{-- حالة الدفع والتفاصيل --}}
                                                    <div class="flex items-center gap-2">
                                                        @if ($contrib->status == 'paid')
                                                            {{-- 1. علامة Paid --}}
                                                            <span
                                                                class="text-green-600 font-bold bg-white px-2 py-0.5 rounded shadow-sm border border-green-100 flex items-center gap-1">
                                                                <i class="fas fa-check"></i> Paid
                                                            </span>

                                                            {{-- 2. تفاصيل الطريقة (كاش ولا وصل) --}}
                                                            @if ($contrib->payment_method == 'transfer' && $contrib->payment_proof)
                                                                {{-- ✅ التعديل: استخدام راوت final_project --}}
                                                                <a href="{{ route('final_project.view_attachment', ['path' => $contrib->payment_proof]) }}"
                                                                    target="_blank"
                                                                    class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded hover:bg-blue-100 transition flex items-center gap-1 border border-blue-100 group"
                                                                    title="View Receipt">
                                                                    <i class="fas fa-file-invoice group-hover:text-blue-700"></i>
                                                                    Receipt
                                                                </a>
                                                            @elseif ($contrib->payment_method == 'cash')
                                                                <span
                                                                    class="text-[10px] text-gray-500 font-bold bg-gray-100 px-2 py-0.5 rounded border border-gray-200 cursor-default">
                                                                    Cash 💵
                                                                </span>
                                                            @endif
                                                        @else
                                                            {{-- 3. حالة Unpaid وزرار الدفع --}}
                                                            <span
                                                                class="text-red-500 font-bold mr-2 bg-white px-2 py-0.5 rounded shadow-sm border border-red-100">Unpaid</span>

                                                            @if ($myRole == 'leader')
                                                                <button
                                                                    onclick="openMarkPaidModal({{ $contrib->id }}, {{ $fund->amount_per_member }}, '{{ $contrib->user->name }}')"
                                                                    class="bg-gray-900 text-white px-3 py-1 rounded-lg shadow-sm hover:bg-black transition text-[10px] uppercase font-bold flex items-center gap-1">
                                                                    <i class="fas fa-hand-holding-usd"></i> Pay
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-wallet text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 font-medium">No financial history recorded yet.</p>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 px-8 py-4 text-right border-t border-gray-200 flex-shrink-0">
                    <button type="button" onclick="closeModal('fundsHistoryModal')"
                        class="bg-white border border-gray-300 text-gray-700 py-2 px-6 rounded-xl font-bold hover:bg-gray-100 transition shadow-sm text-xs uppercase tracking-wide">Close
                        Log</button>
                </div>
            </div>
        </div>
    </div>


    {{--
    ####################################################################
    # #
    # 📊 SECTION 4: OPERATIONS (Reports, Supervision, Meetings) #
    # Logic: Weekly Reporting, Bookings, Attendance Tracking #
    # #
    ####################################################################
    --}}

    {{-- 12. Add Report Week Modal --}}
    <div id="addReportModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('addReportModal')"></div>
            <div class="modal-content !max-w-xl">
                <form action="{{ route('final_project.storeReport') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 px-8 py-6 rounded-t-[1.3rem]">
                        <h3 class="text-xl font-black text-white flex items-center gap-3">
                            <i class="fas fa-calendar-check shimmer-icon"></i> Weekly Report
                        </h3>
                        <p class="text-blue-100 text-xs mt-1 pl-8">Document your team's progress for the supervisor.
                        </p>
                    </div>

                    <div class="bg-white px-8 pt-6 pb-6 space-y-4">
                        <div class="grid grid-cols-4 gap-4">
                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Week
                                    No.</label>
                                <input type="number" name="week_number" required min="1" max="20" placeholder="1"
                                    class="w-full border-2 border-gray-100 bg-gray-50 p-3 rounded-xl focus:ring-0 focus:border-blue-500 text-center font-bold text-lg transition outline-none">
                            </div>
                            <div class="col-span-3">
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Attachment
                                    (Optional)</label>
                                <input type="file" name="report_file"
                                    class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer border rounded-lg p-1.5 border-gray-200">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Report
                                Date & Time</label>
                            <input type="datetime-local" name="report_date" required
                                class="input-classic font-bold text-gray-800">
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider mb-2 text-green-600">Achievements
                                (Done)</label>
                            <textarea name="achievements" required rows="3"
                                placeholder="- Finished circuit design&#10;- Configured database"
                                class="input-classic resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-blue-600">Next
                                Week Plan</label>
                            <textarea name="plans" required rows="2" placeholder="- Buy components&#10;- Start frontend"
                                class="input-classic resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-red-500">Challenges
                                / Blockers</label>
                            <textarea name="challenges" rows="2" placeholder="e.g. Sensor not available in market..."
                                class="w-full border-2 border-gray-100 bg-gray-50 p-3 rounded-xl focus:ring-0 focus:border-red-400 transition text-sm resize-none outline-none"></textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-xl font-bold shadow-lg transition transform hover:-translate-y-0.5 ripple-btn text-xs uppercase tracking-wider">Submit
                            Report</button>
                        <button type="button" onclick="closeModal('addReportModal')" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 13. Book Supervision Modal --}}
    <div id="bookSupervisionModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('bookSupervisionModal')"></div>
            <div class="modal-content !max-w-lg">
                <form action="{{ route('final_project.requestSupervision') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div class="modal-header-gradient px-8 py-6 relative overflow-hidden rounded-t-[1.3rem]">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-[#D4AF37]/10 rounded-full blur-xl -mr-6 -mt-6">
                        </div>
                        <h3 class="text-xl font-black text-[#FFD700] flex items-center gap-3 relative z-10">
                            <i class="fas fa-chalkboard-teacher shimmer-icon"></i> Request Supervision
                        </h3>
                    </div>

                    <div class="bg-white px-8 pt-6 pb-6 space-y-5">
                        <div class="group">
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 group-focus-within:text-[#D4AF37] transition-colors">Meeting
                                Topic</label>
                            <input type="text" name="topic" required placeholder="e.g. Reviewing Project Proposal"
                                class="input-classic">
                        </div>

                        <div class="group">
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 group-focus-within:text-[#D4AF37] transition-colors">Details</label>
                            <textarea name="description" required rows="3"
                                placeholder="Explain the reason (e.g. Discussing hardware issues...)"
                                class="input-classic resize-none"></textarea>
                        </div>

                        <div class="group">
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 group-focus-within:text-[#D4AF37] transition-colors">Preferred
                                Date & Time</label>
                            <input type="datetime-local" name="meeting_date" required class="input-classic text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Meeting
                                Mode</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="mode" value="online" class="peer hidden" checked>
                                    <div class="p-3 rounded-xl border-2 border-gray-100 text-center transition-all duration-300
                                                peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-md
                                                group-hover:border-blue-200">
                                        <div
                                            class="text-xs font-bold text-gray-600 peer-checked:text-blue-700 flex items-center justify-center gap-2">
                                            <i class="fas fa-video"></i> Online
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="mode" value="offline" class="peer hidden">
                                    <div class="p-3 rounded-xl border-2 border-gray-100 text-center transition-all duration-300
                                                peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:shadow-md
                                                group-hover:border-purple-200">
                                        <div
                                            class="text-xs font-bold text-gray-600 peer-checked:text-purple-700 flex items-center justify-center gap-2">
                                            <i class="fas fa-university"></i> Offline
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="btn-modal-primary ripple-btn">Send Request <i
                                class="fas fa-paper-plane ml-1"></i></button>
                        <button type="button" onclick="closeModal('bookSupervisionModal')"
                            class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 14. Internal Meeting Modal --}}
    <div id="internalMeetingModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('internalMeetingModal')"></div>
            <div class="modal-content !max-w-md" x-data="{ mode: 'online', locationType: 'college' }">
                <form action="{{ route('final_project.storeInternalMeeting') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div class="bg-gray-800 px-8 py-5 rounded-t-[1.3rem]">
                        <h3 class="text-lg font-black text-white flex items-center gap-2"><i class="fas fa-users"></i>
                            Internal Meeting</h3>
                    </div>

                    <div class="bg-white px-8 pt-6 pb-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Topic</label>
                            <input type="text" name="topic" required placeholder="e.g. Brainstorming Phase 1"
                                class="input-classic font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Date & Time</label>
                            <input type="datetime-local" name="meeting_date" required class="input-classic text-sm">
                        </div>

                        {{-- Mode --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Mode</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="mode" value="online" class="peer hidden" x-model="mode">
                                    <div
                                        class="p-2 border rounded-lg text-center text-xs font-bold peer-checked:bg-gray-800 peer-checked:text-white transition hover:bg-gray-50 shadow-sm">
                                        Online 🌐</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="mode" value="offline" class="peer hidden" x-model="mode">
                                    <div
                                        class="p-2 border rounded-lg text-center text-xs font-bold peer-checked:bg-gray-800 peer-checked:text-white transition hover:bg-gray-50 shadow-sm">
                                        Offline 📍</div>
                                </label>
                            </div>
                        </div>

                        <div x-show="mode == 'online'" x-transition>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Meeting Link</label>
                            <input type="url" name="meeting_link" placeholder="Zoom / Google Meet URL"
                                class="w-full border rounded-xl p-2.5 text-sm bg-gray-50 focus:bg-white transition focus:ring-2 focus:ring-gray-300 outline-none">
                        </div>

                        <div x-show="mode == 'offline'" x-transition>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Location</label>
                            <select name="location_type"
                                class="w-full border rounded-xl p-2.5 text-sm mb-2 bg-white outline-none"
                                x-model="locationType">
                                <option value="college">🏛️ College Campus</option>
                                <option value="other">🗺️ Other Place</option>
                            </select>

                            <div x-show="locationType == 'other'" x-transition>
                                <input type="text" name="custom_location" placeholder="e.g. Co-working Space / Cafe"
                                    class="w-full border rounded-xl p-2.5 text-sm bg-gray-50">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-4 flex justify-end gap-2 border-t border-gray-100">
                        <button type="submit"
                            class="bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-md hover:bg-black transition ripple-btn">Schedule</button>
                        <button type="button" onclick="closeModal('internalMeetingModal')"
                            class="text-gray-500 px-4 py-2 text-sm hover:text-gray-700 font-bold">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 15. Mark Attendance Modal --}}
    <div id="markAttendanceModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('markAttendanceModal')"></div>
            <div class="modal-content !max-w-md">
                <form action="{{ route('final_project.markAttendance') }}" method="POST">
                    @csrf
                    <input type="hidden" name="meeting_id" id="attendanceMeetingId">

                    <div class="bg-gray-900 px-8 py-5 border-b border-gray-800 rounded-t-[1.3rem]">
                        <h3 class="text-lg font-black text-white">Mark Attendance 📝</h3>
                        <p class="text-xs text-gray-400 mt-1" id="attendanceMeetingTopic">Select members who are
                            PRESENT.</p>
                    </div>

                    <div class="bg-white px-8 pt-6 pb-6 max-h-60 overflow-y-auto custom-scroll">
                        <div class="space-y-2">
                            @if (isset($team) && $team)
                                @foreach ($team->members as $member)
                                    <label
                                        class="flex items-center p-3 border rounded-xl hover:bg-gray-50 cursor-pointer transition select-none group">
                                        <input type="checkbox" name="attendees[]" value="{{ $member->user_id }}"
                                            class="w-5 h-5 text-green-600 rounded focus:ring-green-500 cursor-pointer">
                                        <div class="ml-3 flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ $member->user->name }}"
                                                class="w-8 h-8 rounded-full border group-hover:scale-110 transition shadow-sm">
                                            <span class="text-sm font-bold text-gray-700">{{ $member->user->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-center text-gray-400 text-xs">No members found.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-4 flex justify-end gap-2 border-t border-gray-100">
                        <button type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-md hover:bg-green-700 transition ripple-btn">Save
                            Attendance</button>
                        <button type="button" onclick="closeModal('markAttendanceModal')"
                            class="text-gray-500 px-4 py-2 text-sm hover:text-gray-700 font-bold">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 16. Supervision History Modal --}}
    @if (isset($team) && $team)
        <div id="supervisionHistoryModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="modal-centering-wrapper">
                <div class="modal-overlay" onclick="closeModal('supervisionHistoryModal')"></div>
                <div class="modal-content !max-w-2xl flex flex-col max-h-[85vh]">
                    <div
                        class="bg-gray-900 px-8 py-5 border-b border-[#D4AF37]/30 flex justify-between items-center rounded-t-[1.3rem] flex-shrink-0">
                        <h3 class="text-lg font-black text-[#FFD700]"><i class="fas fa-history"></i> Supervision Log
                        </h3>
                        <button onclick="closeModal('supervisionHistoryModal')"
                            class="text-gray-400 hover:text-white transition transform hover:rotate-90"><i
                                class="fas fa-times"></i></button>
                    </div>

                    <div class="bg-white px-8 py-6 overflow-y-auto custom-scroll flex-grow">
                        @php
                            $supervisionLogs = \App\Models\Meeting::where('team_id', $team->id)
                                ->where('type', 'supervision')
                                ->orderBy('meeting_date', 'desc')
                                ->get();
                        @endphp

                        @forelse($supervisionLogs as $meet)
                            <div
                                class="mb-4 border border-gray-100 rounded-xl p-4 hover:border-yellow-200 transition bg-gray-50/50 hover:shadow-md group">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm group-hover:text-black transition">
                                            {{ $meet->topic }}
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $meet->description ?? 'No description' }}
                                        </p>
                                        <span class="text-[10px] text-gray-400 block mt-2 font-mono">
                                            <i class="far fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($meet->meeting_date)->format('d M, Y - h:i A') }}
                                        </span>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold px-2 py-1 rounded-lg uppercase tracking-wider shadow-sm {{ $meet->status == 'confirmed' ? 'bg-green-100 text-green-700' : ($meet->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-600') }}">
                                        {{ ucfirst($meet->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <i class="fas fa-clipboard-list text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-400 text-sm">No supervision history.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 17. Internal History Modal --}}
    @if (isset($team) && $team)
        <div id="internalHistoryModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="modal-centering-wrapper">
                <div class="modal-overlay" onclick="closeModal('internalHistoryModal')"></div>
                <div class="modal-content !max-w-2xl flex flex-col max-h-[85vh]">
                    <div
                        class="bg-white px-8 py-5 border-b border-gray-200 flex justify-between items-center rounded-t-[1.3rem] flex-shrink-0">
                        <h3 class="text-lg font-black text-gray-800"><i class="fas fa-users text-blue-600"></i>
                            Internal Log</h3>
                        <button onclick="closeModal('internalHistoryModal')"
                            class="text-gray-400 hover:text-gray-600 transition transform hover:rotate-90"><i
                                class="fas fa-times"></i></button>
                    </div>

                    <div class="bg-white px-8 py-6 overflow-y-auto custom-scroll flex-grow">
                        @php
                            $internalLogs = \App\Models\Meeting::where('team_id', $team->id)
                                ->where('type', 'internal')
                                ->orderBy('meeting_date', 'desc')
                                ->get();
                        @endphp

                        @forelse($internalLogs as $meet)
                            <div
                                class="mb-4 border border-gray-100 rounded-xl p-4 hover:border-blue-200 transition hover:shadow-md bg-gray-50/20">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $meet->topic }}</h4>
                                        <span class="text-xs text-gray-400 block mt-1 font-mono">
                                            {{ \Carbon\Carbon::parse($meet->meeting_date)->format('d M, Y - h:i A') }}
                                            • {{ ucfirst($meet->mode) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-1">
                                    @foreach ($meet->attendances as $attendance)
                                        <span
                                            class="text-[9px] px-2 py-0.5 rounded border font-bold {{ $attendance->is_present ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-400 opacity-60' }}">
                                            {{ explode(' ', $attendance->user->name)[0] }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <i class="fas fa-users-slash text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-400 text-sm">No internal meetings.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{--
    ####################################################################
    # #
    # 📂 SECTION 5: ARTIFACTS & TASKS #
    # Logic: Gallery Uploads, Task Assignments, Submissions #
    # #
    ####################################################################
    --}}

    {{-- 18. Upload Gallery Modal --}}
    <div id="uploadGalleryModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('uploadGalleryModal')"></div>
            <div class="modal-content !max-w-lg">
                <form action="{{ route('final_project.uploadGallery') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div class="bg-gradient-to-r from-purple-700 to-indigo-800 px-8 py-5 rounded-t-[1.3rem]">
                        <h3 class="text-lg font-black text-white flex items-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i> Upload Artifact
                        </h3>
                    </div>

                    <div class="bg-white px-8 pt-6 pb-6 space-y-5">
                        {{-- Caption --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Caption / Title</label>
                            <input type="text" name="caption" required placeholder="e.g. System Architecture v2"
                                class="input-classic font-bold">
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Category</label>
                            <select name="category"
                                class="w-full border rounded-xl p-3 text-sm bg-white focus:ring-purple-500 focus:border-purple-500 transition outline-none">
                                <option value="prototype">🛠️ Prototype / Hardware</option>
                                <option value="software">💻 Software / UI</option>
                                <option value="diagram">📊 Diagram / ERD</option>
                                <option value="video">🎥 Demo Video</option>
                                <option value="other">📂 Other</option>
                            </select>
                        </div>

                        {{-- Type Selector (Image vs Video) --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">Artifact Type</label>
                            <div class="flex p-1 bg-gray-100 rounded-xl">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="type" value="image" class="peer hidden" checked
                                        onchange="toggleMediaType('image')">
                                    <div
                                        class="text-center py-2 text-xs font-bold text-gray-500 rounded-lg peer-checked:bg-white peer-checked:text-purple-600 peer-checked:shadow-sm transition-all">
                                        <i class="fas fa-image mr-1"></i> Image
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="type" value="video" class="peer hidden"
                                        onchange="toggleMediaType('video')">
                                    <div
                                        class="text-center py-2 text-xs font-bold text-gray-500 rounded-lg peer-checked:bg-white peer-checked:text-purple-600 peer-checked:shadow-sm transition-all">
                                        <i class="fas fa-video mr-1"></i> Video Link
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- INPUT: Image File --}}
                        <div id="imageUploadSection" class="transition-all duration-300">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Image File</label>
                            <label
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-purple-50 hover:border-purple-300 transition group bg-gray-50">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i
                                        class="fas fa-cloud-upload-alt text-3xl text-gray-300 group-hover:text-purple-500 transition mb-2"></i>
                                    <p class="text-xs text-gray-500">Click to upload image</p>
                                </div>
                                <input type="file" name="image" accept="image/*" class="hidden">
                            </label>
                        </div>

                        {{-- INPUT: Video Link --}}
                        <div id="videoLinkSection" class="hidden transition-all duration-300">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Video Link
                                (YouTube/Drive)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-link text-gray-400"></i>
                                </div>
                                <input type="url" name="video_link" placeholder="https://youtube.com/..."
                                    class="input-classic pl-10">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Paste a valid URL for your demo video.</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-4 flex justify-end gap-2 border-t border-gray-100">
                        <button type="submit"
                            class="bg-purple-600 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-md hover:bg-purple-700 transition ripple-btn">Upload
                            Asset</button>
                        <button type="button" onclick="closeModal('uploadGalleryModal')"
                            class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 19. Add Task Modal --}}
    <div id="addTaskModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('addTaskModal')"></div>
            <div class="modal-content !max-w-md">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">

                    <div id="addTaskHeader" class="bg-gray-800 px-8 py-5 transition-colors rounded-t-[1.3rem]">
                        <h3 class="text-lg font-black text-white flex items-center gap-2">
                            <i class="fas fa-tasks"></i> Assign Task <span id="addTaskTypeBadge"
                                class="bg-white/20 text-xs px-2 py-0.5 rounded ml-2 font-mono"></span>
                        </h3>
                    </div>

                    <div class="bg-white px-8 pt-6 pb-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Task Title</label>
                            <input type="text" name="title" required placeholder="e.g. Design Database Schema"
                                class="input-classic font-bold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Assign To</label>
                            <select name="user_id" id="taskAssignUser"
                                class="w-full border rounded-xl p-3 text-sm bg-white outline-none focus:border-gray-500 transition">
                                {{-- JS Injected --}}
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Deadline</label>
                            <input type="date" name="deadline" required class="input-classic text-sm">
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-4 flex justify-end gap-2 border-t border-gray-100">
                        <button type="submit"
                            class="bg-gray-900 text-white px-6 py-2 rounded-xl text-sm font-bold hover:shadow-lg transition ripple-btn">Assign</button>
                        <button type="button" onclick="closeModal('addTaskModal')" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 20. Submit Task Modal --}}
    <div id="submitTaskModal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="modal-centering-wrapper">
            <div class="modal-overlay" onclick="closeModal('submitTaskModal')"></div>

            <div class="modal-content !max-w-lg">
                {{-- Form with ID and Enctype for Files --}}
                <form id="submissionForm" method="POST" enctype="multipart/form-data" class="space-y-4"
                    x-data="{ type: 'file' }">
                    @csrf

                    {{-- 👇 1. حقل مخفي بيحدد نوع التسليم بناء على التاب المختار --}}
                    <input type="hidden" name="submission_type" x-model="type">

                    <div class="bg-green-600 px-6 py-5 border-b border-green-500 rounded-t-[1.3rem]">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-upload"></i> Submit Task
                        </h3>
                        <p class="text-green-100 text-xs mt-1" id="submitTaskTitle"></p>
                    </div>

                    <div class="p-6 space-y-4 bg-white">

                        {{-- 👇 2. أزرار التبديل (Tabs) --}}
                        <div class="flex p-1 bg-gray-100 rounded-xl mb-4">
                            <button type="button" @click="type = 'file'"
                                :class="{ 'bg-white text-green-700 shadow-sm': type === 'file', 'text-gray-500 hover:text-gray-700': type !== 'file' }"
                                class="flex-1 py-2 text-xs font-bold rounded-lg transition-all duration-200">
                                <i class="fas fa-file-alt mr-1"></i> Upload File
                            </button>

                        </div>

                        {{-- 3. منطقة رفع الملف (تظهر فقط لما نختار File) --}}
                        <div x-show="type === 'file'" x-transition>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Upload File</label>
                            <input type="file" name="submission_file" :required="type === 'file'"
                                class="w-full text-xs text-gray-500 border rounded-lg p-2 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        </div>



                        {{-- تعليق إضافي (مشترك للاثنين) --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Comment</label>
                            <textarea name="submission_comment" rows="3"
                                class="w-full border-2 border-gray-100 bg-gray-50 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white outline-none transition text-sm"
                                placeholder="Optional notes..."></textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 border-t border-gray-100">
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold shadow-lg transition ripple-btn">Submit</button>
                        <button type="button" onclick="closeModal('submitTaskModal')" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- ⚡ MAGIC SCRIPTS: Core Logic & Behavior --}}
    {{-- ========================================================= --}}
    <script>
        /**
         * 1. DOM Teleportation & Initialization
         * Ensures modals are placed correctly in the body to avoid CSS z-index conflicts.
         */
        document.addEventListener("DOMContentLoaded", function () {
            const modalContainer = document.getElementById('royal-modals-container');
            if (modalContainer) {
                document.body.appendChild(modalContainer);
            }
        });

        /**
         * 2. Open Modal Logic
         * Handles the opening animation and body lock.
         */
        window.openModal = function (id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('hidden');
                document.body.classList.add('modal-open-lock'); // Freeze scrolling

                // Add animation delay for smooth "pop"
                setTimeout(() => {
                    const content = el.querySelector('.modal-content');
                    if (content) content.classList.add('active');
                }, 50); // Slight delay for rendering
            }
        };

        /**
         * 3. Close Modal Logic
         * Handles the closing animation and cleanup.
         */
        window.closeModal = function (id) {
            const el = document.getElementById(id);
            if (el) {
                const content = el.querySelector('.modal-content');
                if (content) content.classList.remove('active');

                // Wait for animation to finish before hiding
                setTimeout(() => {
                    el.classList.add('hidden');
                    // Check if any other modal is open
                    const openModals = document.querySelectorAll('.hidden.relative.z-50:not(.hidden)');
                    if (openModals.length === 0) {
                        document.body.classList.remove('modal-open-lock');
                    }
                }, 300); // Matches CSS transition duration
            }
        };

        /**
         * 4. Media Type Toggler (Gallery)
         */
        window.toggleMediaType = function (type) {
            const imageSec = document.getElementById('imageUploadSection');
            const videoSec = document.getElementById('videoLinkSection');

            if (type === 'video') {
                imageSec.classList.add('hidden');
                videoSec.classList.remove('hidden');
                if (imageSec.querySelector('input')) imageSec.querySelector('input').removeAttribute('required');
                if (videoSec.querySelector('input')) videoSec.querySelector('input').setAttribute('required', 'true');
            } else {
                videoSec.classList.add('hidden');
                imageSec.classList.remove('hidden');
                if (videoSec.querySelector('input')) videoSec.querySelector('input').removeAttribute('required');
                if (imageSec.querySelector('input')) imageSec.querySelector('input').setAttribute('required', 'true');
            }
        };

        /**
         * 5. UI Enhancements (Ripples & Hotkeys)
         */
        // Button Ripple Effect
        document.querySelectorAll('.ripple-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                const rect = e.target.getBoundingClientRect();
                let x = e.clientX - rect.left;
                let y = e.clientY - rect.top;
                let ripple = document.createElement('span');
                ripple.classList.add('ripple');
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                this.appendChild(ripple);
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Close on ESC Key
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                const modals = document.querySelectorAll('[id$="Modal"]');
                modals.forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modal.id);
                    }
                });
            }
        });

        /**
         * 6. Submit Task Logic (Consolidated & Optimized)
         * Handles dynamic form action routing for task submission
         */
        window.openSubmitTaskModal = function (taskId, taskTitle) {
            // 1. Locate Modal & Elements
            const modal = document.getElementById('submitTaskModal');
            const titleEl = document.getElementById('submitTaskTitle');
            const form = document.getElementById('submissionForm');

            if (modal && titleEl && form) {
                // 2. Set Content
                titleEl.innerText = taskTitle;

                // 3. Set Dynamic Route Action
                // Ensures Action is Correct: /tasks/{id}/submit
                form.action = `/tasks/${taskId}/submit`;

                // 4. Trigger Open Animation via helper
                modal.classList.remove('hidden');
                document.body.classList.add('modal-open-lock');
                setTimeout(() => {
                    const content = modal.querySelector('.modal-content');
                    if (content) content.classList.add('active');
                }, 50);
            } else {
                console.error('System Error: Submit Task Modal elements not found.');
            }
        }


        document.getElementById('submissionForm').addEventListener('submit', function (e) {
            // 1. بنشوف إيه الظاهر دلوقتي (لينك ولا فايل)
            // لو حقل الفايل مخفي (display: none) يبقى إحنا في مود اللينك
            const fileDiv = this.querySelector('[x-show="type === \'file\'"]');
            const isFileVisible = fileDiv && fileDiv.style.display !== 'none';

            // 2. بنحدد النوع يدوياً
            const type = isFileVisible ? 'file' : 'link';

            // 3. بنزرع القيمة في الانبوت غصب عنه قبل ما يتبعت
            let input = this.querySelector('input[name="submission_type"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'submission_type';
                this.appendChild(input);
            }
            input.value = type;
        });
    </script>
</div>