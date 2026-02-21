@props(['task', 'color'])

{{--
==========================================================================
📋 ROYAL TASK CARD COMPONENT
-----------------------------------------------------------------------
Status: OPTIMIZED FOR DOCTOR REVIEW
Features: Interactive States, Role-Based Actions, Glassmorphism
==========================================================================
--}}

<div class="relative group mb-4 transition-all duration-300 hover:-translate-y-1">

    {{-- Card Container --}}
    <div
        class="p-5 rounded-2xl border border-gray-100 bg-white shadow-sm hover:shadow-lg transition-all duration-300 relative overflow-hidden">

        {{-- Decorative Left Border based on status --}}
        <div class="absolute left-0 top-0 bottom-0 w-1.5
            {{ $task->status == 'completed'
    ? 'bg-green-500'
    : ($task->status == 'reviewing'
        ? 'bg-yellow-500'
        : ($task->status == 'rejected'
            ? 'bg-red-500'
            : 'bg-gray-200')) }}">
        </div>

        <div class="flex justify-between items-start pl-3">

            {{--
            #############################################
            # 📝 SECTION 1: TASK INFO (Title & Meta) #
            #############################################
            --}}
            <div class="flex-1 pr-4">
                {{-- Title --}}
                <h4
                    class="text-sm font-bold text-gray-800 leading-snug {{ $task->status == 'completed' ? 'line-through text-gray-400 decoration-gray-300' : '' }}">
                    {{ $task->title }}
                </h4>

                {{-- Meta Tags --}}
                <div class="flex items-center flex-wrap gap-2 mt-2">
                    {{-- Date Badge --}}
                    <span
                        class="text-[10px] text-gray-500 font-mono bg-gray-50 border border-gray-100 px-2 py-0.5 rounded-md flex items-center gap-1.5">
                        <i class="far fa-clock text-[9px]"></i>
                        {{ \Carbon\Carbon::parse($task->deadline)->format('M d') }}
                    </span>

                    {{-- Status Badges --}}
                    @if ($task->status == 'reviewing')
                        <span
                            class="text-[9px] bg-yellow-50 text-yellow-700 border border-yellow-100 px-2 py-0.5 rounded-full font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span>
                            In Review
                        </span>
                    @elseif($task->status == 'rejected')
                        <span
                            class="text-[9px] bg-red-50 text-red-700 border border-red-100 px-2 py-0.5 rounded-full font-bold flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            Changes Requested
                        </span>
                    @endif


                    {{-- بيظهر لو الطالب لسه مسلمش والوقت فات --}}
                    @if ($task->is_overdue)
                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600 border border-red-200 animate-pulse">
                            <i class="fas fa-exclamation-circle"></i> Late / Overdue
                        </span>
                    @endif


                    {{-- بيظهر لو الطالب سلم خلاص بس كان متأخر --}}
                    @if ($task->is_submitted_late)
                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200"
                            title="Submitted after deadline">
                            <i class="fas fa-clock"></i> Late Submission
                        </span>
                    @endif
                </div>
            </div>

            {{--
            #############################################
            # ⚡ SECTION 2: ACTIONS (Buttons & Logic) #
            #############################################
            --}}
            <div class="flex items-center gap-2 self-start pt-0.5">

                {{-- [CASE A]: Member -> Submit Task --}}
                @if ($task->status == 'pending' && Auth::id() == $task->user_id)
                    <button onclick="openSubmitTaskModal('{{ $task->id }}', '{{ $task->title }}')"
                        class="group/btn relative overflow-hidden bg-{{ $color ?? 'blue' }}-600 text-white text-[10px] font-bold px-4 py-2 rounded-xl transition-all hover:shadow-md hover:shadow-{{ $color ?? 'blue' }}-500/20 active:scale-95">
                        <span class="relative z-10 flex items-center gap-1.5">
                            <i class="fas fa-cloud-upload-alt"></i> Upload
                        </span>
                        <div
                            class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300">
                        </div>
                    </button>

                    {{-- [CASE B]: Leader/Vice -> Review Process --}}
                @elseif($task->status == 'reviewing' && ($myRole == 'leader' || $myRole == 'vice_leader'))
                    <div class="flex items-center gap-1.5 bg-gray-50 p-1 rounded-full border border-gray-100">

                        {{-- 1. View File --}}
                        @if ($task->submission_file)
                            {{-- ✅✅✅ التعديل هنا: استخدام الراوت الجوكر --}}
                            <a href="{{ $task->submission_file }}" target="_blank"
                                class="w-8 h-8 flex items-center justify-center rounded-full text-blue-600 hover:bg-blue-600 hover:text-white hover:shadow-md transition-all duration-200"
                                title="View Submitted File">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        @else
                            <span class="text-[9px] text-gray-400 px-2 cursor-help" title="No file attached">
                                <i class="fas fa-file-excel"></i>
                            </span>
                        @endif

                        {{-- Divider --}}
                        <div class="w-px h-4 bg-gray-200"></div>

                        {{-- 2. Approve --}}
                        <form action="{{ route('tasks.approve', $task->id) }}" method="POST" class="m-0">
                            @csrf
                            <button
                                class="w-8 h-8 flex items-center justify-center rounded-full text-green-600 hover:bg-green-500 hover:text-white hover:shadow-md hover:shadow-green-500/20 transition-all duration-200"
                                title="Approve Task">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                        </form>

                        {{-- 3. Reject --}}
                        <form action="{{ route('tasks.reject', $task->id) }}" method="POST" class="m-0">
                            @csrf
                            <button
                                class="w-8 h-8 flex items-center justify-center rounded-full text-red-500 hover:bg-red-500 hover:text-white hover:shadow-md hover:shadow-red-500/20 transition-all duration-200"
                                title="Request Changes">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </form>
                    </div>

                    {{-- [CASE C]: Completed State --}}
                @elseif($task->status == 'completed')
                    <div class="w-8 h-8 flex items-center justify-center bg-green-50 rounded-full border border-green-100">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                @endif
            </div>
        </div>

        {{--
        #############################################
        # 💬 SECTION 3: SUBMISSION COMMENTS #
        #############################################
        --}}
        @if ($task->submission_comment)
            <div class="mt-4 pl-3">
                <div class="relative bg-gray-50/80 p-3 rounded-xl border border-dashed border-gray-200">
                    <i class="fas fa-quote-left text-gray-300 absolute -top-2 left-3 bg-white px-1 text-xs"></i>
                    <p class="text-xs text-gray-600 italic leading-relaxed">
                        {{ $task->submission_comment }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>