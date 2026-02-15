@extends('layouts.staff')

@section('content')
    <!-- إضافة ستايل خاص للأنيميشن والفخامة -->
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.5);
        }

        .hover-scale {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-scale:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #175c53;
            border-radius: 20px;
        }
    </style>

    <div class="max-w-[1600px] mx-auto px-4 py-8 animate-fade-in">

        <!-- Header Section: تقسيم فخم بين العنوان والإحصائيات -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-[#175c53] rounded-2xl shadow-lg shadow-[#175c53]/20">
                    <i class="fas fa-project-diagram text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Project Proposals Review</h1>
                    <p class="text-gray-500 font-medium flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
                        Review student ideas and assign Teaching Assistants
                    </p>
                </div>
            </div>

            <div class="glass-card px-8 py-4 rounded-2xl border-l-4 border-[#175c53] shadow-sm flex items-center gap-6">
                <div class="text-right">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Pending Review</span>
                    <p class="text-3xl font-black text-[#175c53]">{{ $proposals->count() }}</p>
                </div>
                <div class="h-10 w-[1px] bg-gray-200"></div>
                <div class="bg-[#175c53]/10 p-3 rounded-full">
                    <i class="fas fa-hourglass-half text-[#175c53] animate-spin-slow"></i>
                </div>
            </div>
        </div>

        {{-- Search & Filter Bar: بصمة احترافية في التصميم --}}
        <div class="glass-card p-4 rounded-2xl mb-8 shadow-sm">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">

                {{-- Form البحث - حافظت عليه زي ما هو بس شيكته --}}
                <form action="{{ route('staff.proposals') }}" method="GET" class="flex-grow w-full lg:max-w-2xl">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-focus-within:text-[#175c53] transition-colors"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full p-4 pl-12 text-sm text-gray-700 bg-gray-50 border-none rounded-xl ring-1 ring-gray-200 focus:ring-2 focus:ring-[#175c53] focus:bg-white transition-all duration-300"
                            placeholder="Search by project title, team name or leader..." autocomplete="off">
                    </div>
                </form>

                <div class="flex items-center gap-4 w-full lg:w-auto">
                    {{-- فلتر السنة --}}
                    <form method="GET" class="w-full lg:w-auto">
                        <select name="year" onchange="this.form.submit()"
                            class="w-full bg-gray-50 border-none ring-1 ring-gray-200 rounded-xl text-sm font-bold text-gray-600 p-4 focus:ring-2 focus:ring-[#175c53] transition-all cursor-pointer">
                            <option value="all">📅 All Academic Years</option>
                            <option value="1" {{ request('year') == '1' ? 'selected' : '' }}>1st Year</option>
                            <option value="2" {{ request('year') == '2' ? 'selected' : '' }}>2nd Year</option>
                            <option value="3" {{ request('year') == '3' ? 'selected' : '' }}>3rd Year</option>
                            <option value="4" {{ request('year') == '4' ? 'selected' : '' }}>4th Year</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="glass-card rounded-3xl shadow-xl overflow-hidden border border-gray-100 flex flex-col">
            <div class="overflow-x-auto custom-scrollbar flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-wider">Project Identity
                            </th>
                            <th class="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-wider">Team Leader</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-wider">Documentation
                            </th>
                            <th class="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-wider text-right">
                                Decision Tool</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($proposals as $team)
                            <tr class="hover:bg-[#175c53]/[0.02] transition-all duration-200 group">

                                {{-- 1. اسم المشروع واسم التيم --}}
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-gray-900 text-lg group-hover:text-[#175c53] transition-colors">
                                            {{ $team->proposal_title ?? 'Untitled Proposal' }}
                                        </span>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span
                                                class="text-[11px] font-bold bg-amber-50 text-amber-700 px-3 py-1 rounded-full border border-amber-100 uppercase tracking-tight">
                                                Team: {{ $team->name }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-400 mt-2 italic line-clamp-1">
                                            "{{ Str::limit($team->proposal_description, 80) }}"
                                        </p>
                                    </div>
                                </td>

                                {{-- 2. اسم الليدر --}}
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#175c53] to-[#2d8b7d] flex items-center justify-center text-white font-bold shadow-md">
                                                {{ substr($team->leader->name ?? '?', 0, 1) }}
                                            </div>
                                            <div
                                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full">
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-gray-700">{{ $team->leader->name ?? 'Unknown Leader' }}</span>
                                            <span class="text-xs text-gray-400 tracking-tighter">Project Manager</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- 3. ملف الـ PDF --}}
                                <td class="px-8 py-6">
                                    @if ($team->proposal_file)
                                        {{-- 🔥 التعديل هنا: استخدام الراوت المباشر بدلاً من asset --}}
                                        <a href="{{ route('proposal.view_file', $team->id) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-100 text-red-600 rounded-xl text-xs font-black hover:bg-red-50 hover:shadow-md hover:shadow-red-500/10 transition-all active:scale-95">
                                            <i class="fas fa-file-pdf text-lg"></i>
                                            <span>VIEW PROPOSAL</span>
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-400 rounded-xl text-xs font-bold border border-dashed border-gray-200">
                                            <i class="fas fa-exclamation-circle"></i> Missing File
                                        </span>
                                    @endif
                                </td>

                                {{-- 4. الأزرار --}}
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button onclick="openRejectModal('{{ $team->id }}')"
                                            class="group/btn flex items-center gap-2 text-gray-400 hover:text-red-600 font-bold text-xs p-2 transition-all">
                                            <span class="hidden group-hover/btn:block">Reject</span>
                                            <div
                                                class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center group-hover/btn:border-red-200 group-hover/btn:bg-red-50">
                                                <i class="fas fa-times"></i>
                                            </div>
                                        </button>

                                        <button onclick="openApproveModal('{{ $team->id }}')"
                                            class="flex items-center gap-2 bg-[#175c53] hover:bg-[#0d3d37] text-white px-6 py-3 rounded-xl text-xs font-black shadow-lg shadow-[#175c53]/20 hover:shadow-[#175c53]/40 transition-all hover:-translate-y-0.5 active:scale-95">
                                            <i class="fas fa-check-circle"></i>
                                            APPROVE PROJECT
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                                            <i class="fas fa-folder-open text-4xl text-gray-200"></i>
                                        </div>
                                        <p class="text-gray-400 font-medium text-lg">No pending proposals found at the
                                            moment.</p>
                                        <p class="text-gray-300 text-sm italic">Sit back and relax!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 🔥 Pagination Links Section 🔥 --}}
            @if ($proposals->hasPages())
                <div class="bg-gray-50 border-t border-gray-100 px-6 py-4">
                    {{ $proposals->links() }}
                </div>
            @endif

        </div>

        {{-- 1. مودال القبول (Approve Modal) --}}
        <div id="approveModal" class="fixed inset-0 z-[100] hidden">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div
                    class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                    <div class="bg-gradient-to-r from-[#175c53] to-[#247a6e] px-8 py-6">
                        <div class="flex justify-between items-center text-white">
                            <h3 class="font-black text-xl flex items-center gap-3">
                                <i class="fas fa-user-shield"></i> Final Approval
                            </h3>
                            <button onclick="closeModal('approveModal')" class="hover:rotate-90 transition-transform">
                                <i class="fas fa-times text-white/50 hover:text-white text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <form id="approveForm" action="" method="POST" class="p-8">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <div class="mb-8">
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Assign
                                Supervisor (TA)</label>
                            <div class="relative">
                                <select name="ta_id" required
                                    class="w-full bg-gray-50 border-none ring-1 ring-gray-200 rounded-2xl p-4 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#175c53] transition-all appearance-none cursor-pointer">
                                    <option value="">-- Choose Teaching Assistant --</option>
                                    @foreach ($tas as $ta)
                                        <option value="{{ $ta->id }}">👨‍🏫 {{ $ta->name }}</option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3">
                            <button type="submit"
                                class="w-full bg-[#175c53] hover:bg-[#0d3d37] text-white py-4 rounded-2xl text-sm font-black shadow-xl shadow-[#175c53]/20 transition-all active:scale-95">
                                CONFIRM & NOTIFY TEAM
                            </button>
                            <button type="button" onclick="closeModal('approveModal')"
                                class="w-full py-4 text-gray-400 text-sm font-bold hover:text-gray-600 transition-colors">
                                Go back
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2. مودال الرفض (Reject Modal) --}}
        <div id="rejectModal" class="fixed inset-0 z-[100] hidden">
            <div class="absolute inset-0 bg-red-900/20 backdrop-blur-md transition-opacity"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div
                    class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-red-50">
                    <div class="bg-gradient-to-r from-red-600 to-rose-500 px-8 py-6">
                        <div class="flex justify-between items-center text-white">
                            <h3 class="font-black text-xl flex items-center gap-3">
                                <i class="fas fa-exclamation-triangle"></i> Reject Proposal
                            </h3>
                            <button onclick="closeModal('rejectModal')" class="hover:rotate-90 transition-transform">
                                <i class="fas fa-times text-white/50 hover:text-white text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <form id="rejectForm" action="" method="POST" class="p-8">
                        @csrf
                        <input type="hidden" name="action" value="reject">

                        <div class="mb-8">
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Reason
                                for Rejection</label>
                            <textarea name="rejection_reason" required rows="4"
                                class="w-full bg-red-50/30 border-none ring-1 ring-red-100 rounded-2xl p-4 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-red-500 transition-all placeholder:text-gray-300 custom-scrollbar"
                                placeholder="Please provide constructive feedback to the students..."></textarea>
                        </div>

                        <div class="flex flex-col gap-3">
                            <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-4 rounded-2xl text-sm font-black shadow-xl shadow-red-600/20 transition-all active:scale-95">
                                REJECT & SEND FEEDBACK
                            </button>
                            <button type="button" onclick="closeModal('rejectModal')"
                                class="w-full py-4 text-gray-400 text-sm font-bold hover:text-gray-600 transition-colors">
                                Cancel rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- سكربت فتح وقفل المودال - حافظت عليه مع إضافة تأثير انيميشن --}}
        <script>
            function openApproveModal(id) {
                document.getElementById('approveForm').action = "/staff/proposals/" + id + "/decide";
                const modal = document.getElementById('approveModal');
                modal.classList.remove('hidden');
                modal.querySelector('.relative').classList.add('animate-fade-in');
            }

            function openRejectModal(id) {
                document.getElementById('rejectForm').action = "/staff/proposals/" + id + "/decide";
                const modal = document.getElementById('rejectModal');
                modal.classList.remove('hidden');
                modal.querySelector('.relative').classList.add('animate-fade-in');
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
            }

            // إغلاق المودال عند الضغط خارج الصندوق
            window.onclick = function(event) {
                if (event.target.classList.contains('bg-gray-900/60') || event.target.classList.contains('bg-red-900/20')) {
                    event.target.parentElement.classList.add('hidden');
                }
            }
        </script>
    @endsection
