 {{--
            ██████████████████████████████████████████████████████████████████████████
            🧩 SECTION 4: MODALS & OVERLAYS
            ██████████████████████████████████████████████████████████████████████████
            --}}

            {{-- 4.1 Members Modal --}}
            <div id="membersModal" class="fixed inset-0 z-[100] hidden" aria-modal="true">
                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity"
                    onclick="document.getElementById('membersModal').classList.add('hidden')"></div>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-6">
                        <div
                            class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-slate-100 animate-premium">
                            <div class="bg-slate-900 px-10 py-8 flex justify-between items-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-slate-900 to-slate-800"></div>
                                <div class="relative z-10">
                                    <h3 class="text-2xl font-black text-white uppercase tracking-tight">Squad Manifest</h3>
                                    <p class="text-[#D4AF37] text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                                        Official Project Contributors</p>
                                </div>
                                <button onclick="document.getElementById('membersModal').classList.add('hidden')"
                                    class="relative z-10 w-10 h-10 rounded-full bg-white/10 text-white hover:bg-[#D4AF37] hover:text-slate-900 transition-all flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="p-10 space-y-6 max-h-[60vh] overflow-y-auto custom-scrollbar bg-slate-50">
                                {{-- Leader --}}
                                <div
                                    class="flex items-center gap-6 p-6 bg-[#D4AF37]/10 rounded-[2rem] border-2 border-[#D4AF37]/20 shadow-xl shadow-yellow-50/50">
                                    <div
                                        class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#D4AF37] to-white flex items-center justify-center text-slate-900 text-2xl shadow-lg border border-white">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-900 text-lg tracking-tight">
                                            {{ $team->leader->name }}
                                        </p>
                                        <p class="text-[10px] text-[#D4AF37] font-black uppercase tracking-[0.2em] mt-1">
                                            Prime Leader</p>
                                        <p class="text-xs text-slate-400 mt-2 font-mono"><i
                                                class="fas fa-envelope mr-1"></i> {{ $team->leader->email }}</p>
                                    </div>
                                </div>

                                {{-- Members --}}
                                @foreach($team->members as $member)
                                    <div
                                        class="flex items-center gap-6 p-6 bg-white rounded-[2rem] border border-slate-100 hover:bg-slate-900 hover:border-slate-800 hover:shadow-xl transition-all group duration-300">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 font-black text-xl shadow-sm group-hover:bg-slate-800 group-hover:text-white transition-all">
                                            {{ substr($member->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p
                                                class="font-black text-slate-800 text-lg tracking-tight group-hover:text-white transition-colors">
                                                {{ $member->user->name }}
                                            </p>
                                            <p class="text-xs text-slate-400 font-mono mt-1 group-hover:text-slate-500">
                                                {{ $member->user->email }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4.2 History Log Modal --}}
            <div id="supervisionHistoryModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
                <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-xl transition-opacity"
                    onclick="document.getElementById('supervisionHistoryModal').classList.add('hidden')"></div>
                <div class="flex items-center justify-center min-h-screen p-6">
                    <div
                        class="relative bg-white rounded-[3rem] shadow-2xl w-full max-w-5xl max-h-[85vh] flex flex-col overflow-hidden border border-slate-200 animate-premium">
                        <div class="bg-slate-900 p-10 border-b border-slate-800 flex justify-between items-center shrink-0">
                            <div>
                                <h3 class="text-3xl font-black text-white uppercase tracking-tight">History Meeting Log</h3>
                                <p class="text-[#D4AF37] text-[10px] font-black uppercase tracking-[0.3em] mt-2">Historic
                                    interaction records</p>
                            </div>
                            <button onclick="document.getElementById('supervisionHistoryModal').classList.add('hidden')"
                                class="w-12 h-12 rounded-full bg-white/10 text-white hover:bg-white hover:text-slate-900 transition-all flex items-center justify-center">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <div class="p-10 overflow-y-auto custom-scrollbar bg-slate-50">
                            @forelse($teamMeetings as $meet)
                                {{-- التعديل هنا: لو نوع الميتنج مش سوبرفيجن، كمل ومتعرضوش --}}

                                <div x-data="{ openDetails: false }"
                                    class="mb-6 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-500">

                                    <div class="p-8 flex justify-between items-center cursor-pointer group"
                                        @click="openDetails = !openDetails">
                                        <div class="flex items-center gap-6">
                                            <div
                                                class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl shadow-inner border border-slate-50 {{ $meet->status == 'confirmed' || $meet->status == 'completed' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                                                @if($meet->status == 'confirmed' || $meet->status == 'completed') <i
                                                class="fas fa-check-double"></i> @else <i class="fas fa-info-circle"></i>
                                                    @endif
                                            </div>
                                            <div>
                                                <h4
                                                    class="font-black text-slate-800 text-xl tracking-tight group-hover:text-emerald-600 transition-colors">
                                                    {{ $meet->topic }}
                                                </h4>
                                                <p
                                                    class="text-[11px] text-slate-400 font-black uppercase tracking-widest mt-2 font-mono">
                                                    <i class="far fa-calendar-alt mr-1 text-[#D4AF37]"></i>
                                                    {{ \Carbon\Carbon::parse($meet->meeting_date)->format('d F Y, h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center transition-transform duration-500 border border-slate-100"
                                            :class="{'rotate-180 bg-slate-900 text-white': openDetails}">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>

                                    <div x-show="openDetails" x-collapse
                                        class="p-10 pt-0 border-t border-slate-50 bg-slate-50/50">
                                        <form action="{{ route('meetings.update_attendance', $meet->id) }}" method="POST"
                                            @submit="loading = true">
                                            @csrf @method('PUT')

                                            <div class="mt-8 space-y-4">
                                                <h5
                                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                                    <span class="w-1 h-3 bg-[#D4AF37] rounded-full"></span> Modify Attendance
                                                    Log
                                                </h5>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    @foreach($team->members as $member)
                                                        @php
                                                            $record = $meet->attendances->where('user_id', $member->user_id)->first();
                                                            $hasAttended = $record ? $record->is_present : false; 
                                                        @endphp

                                                        <div x-data="{ isEditing: false, currentStatus: {{ $hasAttended ? 1 : 0 }} }"
                                                            class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:border-[#D4AF37]/30 transition-all group/edit">

                                                            <div x-show="!isEditing" class="flex items-center justify-between">
                                                                <div class="flex items-center gap-4">
                                                                    <div>
                                                                        <p class="text-sm font-black text-slate-800">
                                                                            {{ $member->user->name }}
                                                                        </p>
                                                                        <span class="text-[9px] font-black uppercase mt-1 block"
                                                                            :class="currentStatus == 1 ? 'text-emerald-600' : 'text-red-500'">
                                                                            <i
                                                                                :class="currentStatus == 1 ? 'fas fa-check' : 'fas fa-times'"></i>
                                                                            <span
                                                                                x-text="currentStatus == 1 ? 'Present' : 'Absent'"></span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <button type="button" @click="isEditing = true"
                                                                    class="text-slate-300 hover:text-slate-900 p-2"><i
                                                                        class="fas fa-pen text-xs"></i></button>
                                                            </div>

                                                            <div x-show="isEditing" x-cloak
                                                                class="flex items-center justify-between">
                                                                <span
                                                                    class="text-[10px] font-black text-slate-900">{{ $member->user->name }}</span>
                                                                <div class="flex items-center gap-2">
                                                                    <div
                                                                        class="flex bg-slate-50 rounded-xl p-1 border border-slate-100">
                                                                        <label class="cursor-pointer">
                                                                            <input type="radio"
                                                                                name="attendance[{{ $member->user_id }}]" value="1"
                                                                                x-model="currentStatus" class="peer sr-only">
                                                                            <span
                                                                                class="block px-3 py-1.5 rounded-lg text-[9px] font-black peer-checked:bg-emerald-600 peer-checked:text-white transition-all">P</span>
                                                                        </label>
                                                                        <label class="cursor-pointer">
                                                                            <input type="radio"
                                                                                name="attendance[{{ $member->user_id }}]" value="0"
                                                                                x-model="currentStatus" class="peer sr-only">
                                                                            <span
                                                                                class="block px-3 py-1.5 rounded-lg text-[9px] font-black peer-checked:bg-red-600 peer-checked:text-white transition-all">A</span>
                                                                        </label>
                                                                    </div>
                                                                    <button type="button" @click="isEditing = false"
                                                                        class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center shadow-lg hover:bg-black"><i
                                                                            class="fas fa-check text-[10px]"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="pt-8 text-right">
                                                    <button type="submit"
                                                        class="bg-slate-900 text-white px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] hover:bg-black transition-all shadow-xl shadow-slate-200 hover:scale-[1.02] transform">
                                                        Synchronize Changes
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="p-20 text-center text-slate-300">
                                    <i class="fas fa-history text-7xl mb-6 opacity-20"></i>
                                    <p class="text-xl font-black uppercase tracking-widest">No Archival Records</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4.3 End Meeting Modal (Legacy Support) --}}
            <div id="endMeetingModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-6"
                aria-modal="true">
                <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-md" onclick="closeModal('endMeetingModal')">
                </div>
                <div
                    class="bg-white rounded-[3rem] p-10 w-full max-w-lg relative z-10 shadow-2xl border border-slate-100 animate-premium">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Mission Debrief</h3>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mt-2">Finalize meeting
                            and secure records</p>
                    </div>

                    <form id="endMeetingForm" method="POST" action="" @submit="loading = true">
                        @csrf
                        <div class="space-y-4 mb-10 max-h-[40vh] overflow-y-auto custom-scrollbar p-2">
                            @foreach($team->members as $member)
                                <div
                                    class="flex items-center justify-between bg-slate-50 p-5 rounded-[2rem] border border-slate-100 hover:bg-white hover:shadow-md transition-all">
                                    <span
                                        class="text-xs font-black text-slate-800 tracking-tight">{{ $member->user->name }}</span>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="attendance[{{ $member->user_id }}]" value="present"
                                                checked class="peer sr-only">
                                            <span
                                                class="w-6 h-6 rounded-full border-2 border-slate-300 peer-checked:border-emerald-600 peer-checked:bg-emerald-600 transition-all shadow-inner relative flex items-center justify-center">
                                                <i
                                                    class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100"></i>
                                            </span>
                                            <span
                                                class="text-[10px] font-black text-slate-400 peer-checked:text-emerald-600 uppercase">Present</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="attendance[{{ $member->user_id }}]" value="absent"
                                                class="peer sr-only">
                                            <span
                                                class="w-6 h-6 rounded-full border-2 border-slate-300 peer-checked:border-red-600 peer-checked:bg-red-600 transition-all shadow-inner relative flex items-center justify-center">
                                                <i
                                                    class="fas fa-times text-white text-[10px] opacity-0 peer-checked:opacity-100"></i>
                                            </span>
                                            <span
                                                class="text-[10px] font-black text-slate-400 peer-checked:text-red-600 uppercase">Absent</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit"
                            class="w-full bg-slate-900 hover:bg-black text-white py-5 rounded-[2rem] font-black text-[10px] uppercase tracking-[0.3em] transition-all shadow-2xl hover:shadow-emerald-200 transform hover:-translate-y-1">
                            Authorize Final Record
                        </button>
                    </form>
                </div>
            </div>

            {{-- 4.4 Back To Top Button --}}
            <button x-show="showScrollTop" x-transition.scale @click="window.scrollTo({top: 0, behavior: 'smooth'})"
                class="fixed bottom-10 right-10 w-14 h-14 bg-slate-900 text-[#D4AF37] rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform z-50 border border-[#D4AF37]/30 hover:border-[#D4AF37] group">
                <i class="fas fa-arrow-up group-hover:animate-bounce"></i>
            </button>

        </div>
    </div>
