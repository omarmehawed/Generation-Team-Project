@extends('layouts.staff')

@section('content')
    <div class="min-h-screen bg-[#F3F4F6] p-6 font-sans relative overflow-hidden">

        {{-- Background Decoration --}}
        <div
            class="absolute top-0 left-0 w-full h-64 bg-gradient-to-r from-gray-700 to-gray-900 shadow-lg -z-10 rounded-b-[3rem]">
        </div>

        <div class="max-w-7xl mx-auto space-y-6 mt-4">

            {{-- Header --}}
            <div class="flex justify-between items-end text-black mb-6">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight flex items-center gap-3">
                        <span class="bg-white/20 p-2 rounded-xl backdrop-blur-md border border-white/10">🛡️</span>
                        Activity Logs
                    </h1>
                    <p class="mt-2 text-black-300 text-sm font-medium opacity-80">Track who did what and when.</p>
                </div>
            </div>

            {{-- Table Container --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Actor (Causer)</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Action</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Target User (Subject)</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Date & Time</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50/80 transition-colors duration-200 group">

                                    {{-- 1. الفاعل (مين عمل كدة؟) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                                {{ $log->causer ? strtoupper(substr($log->causer->name, 0, 1)) : '?' }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">
                                                    {{ $log->causer->name ?? 'System / Unknown' }}</p>
                                                <p class="text-[10px] text-gray-500">{{ $log->causer->email ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 2. نوع الفعل --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($log->action == 'Created')
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100">✨
                                                Created</span>
                                        @elseif($log->action == 'Updated')
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">✏️
                                                Updated</span>
                                        @elseif($log->action == 'Deleted')
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100">🗑️
                                                Deleted</span>
                                        @endif
                                    </td>

                                    {{-- 3. المفعول به (اتعمل في مين؟) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($log->subject)
                                            <div class="text-sm font-bold text-gray-800">{{ $log->subject->name }}</div>
                                            <div class="text-[10px] text-gray-500 uppercase">{{ $log->subject->role }}</div>
                                        @else
                                            <span class="text-xs text-red-400 italic">User Deleted completely</span>
                                        @endif
                                    </td>

                                    {{-- 4. الوقت --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs font-bold text-gray-700">
                                            {{ $log->created_at->format('d M Y, h:i A') }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>

                                    {{-- 5. التفاصيل (زرار) --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($log->changes)
                                            {{-- زرار شيك يفتح المودال --}}
                                            <button onclick="showChanges({{ json_encode($log->changes) }})"
                                                class="group flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-blue-600 text-gray-500 hover:text-white transition-all duration-200 shadow-sm"
                                                title="View Details">
                                                <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                                            </button>
                                        @else
                                            <span class="text-gray-300 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 bg-gray-50">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-history text-4xl mb-3 text-gray-300"></i>
                                            <p class="text-sm">No activity logs found yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>


    {{-- Modals --}}
    {{-- 🔍 Changes Viewer Modal --}}
    <div id="changesModal" class="fixed inset-0 z-50 hidden">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeChangesModal()"></div>

        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl border border-gray-100 transform transition-all scale-100 overflow-hidden flex flex-col max-h-[85vh]">

                {{-- Header --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Change Details</h3>
                            <p class="text-xs text-gray-500">Compare old vs new values.</p>
                        </div>
                    </div>
                    <button onclick="closeChangesModal()"
                        class="text-gray-400 hover:text-red-500 transition text-xl">✕</button>
                </div>

                {{-- Content (Scrollable Table) --}}
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 w-1/3">Field / Attribute</th>
                                <th class="px-4 py-3 w-1/3 text-red-600">Old Value (Before)</th>
                                <th class="px-4 py-3 w-1/3 text-green-600">New Value (After)</th>
                            </tr>
                        </thead>
                        <tbody id="changesTableBody" class="divide-y divide-gray-100">
                            {{-- JS will inject rows here --}}
                        </tbody>
                    </table>

                    {{-- رسالة لو مفيش تغييرات --}}
                    <div id="noChangesMsg" class="hidden text-center py-8 text-gray-400">
                        <p>No specific changes recorded for this action.</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-right">
                    <button onclick="closeChangesModal()"
                        class="px-6 py-2 bg-gray-800 text-white rounded-xl font-bold hover:bg-black transition shadow-lg">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script>
        function showChanges(data) {
            const modal = document.getElementById('changesModal');
            const tbody = document.getElementById('changesTableBody');
            const noMsg = document.getElementById('noChangesMsg');

            tbody.innerHTML = '';

            let hasData = false;

            // 1️⃣ حالة التعديل (Update) - بيكون فيه before و after
            if (data.before || data.after) {
                let allKeys = new Set([...Object.keys(data.before || {}), ...Object.keys(data.after || {})]);

                allKeys.forEach(key => {
                    // تجاهل الحقول الغير مهمة زي updated_at لو مش عايز تشوفها
                    // if (key === 'updated_at') return;

                    let oldVal = data.before ? data.before[key] : '-';
                    let newVal = data.after ? data.after[key] : '-';

                    // تنسيق التواريخ لو طويلة
                    oldVal = formatValue(oldVal);
                    newVal = formatValue(newVal);

                    let row = `
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-bold text-gray-700 capitalize bg-gray-50/50">${key.replace('_', ' ')}</td>
                        <td class="px-4 py-3 text-red-600 bg-red-50/30 font-mono text-xs break-all">${oldVal}</td>
                        <td class="px-4 py-3 text-green-600 bg-green-50/30 font-mono text-xs break-all">${newVal}</td>
                    </tr>
                `;
                    tbody.innerHTML += row;
                    hasData = true;
                });
            }
            // 2️⃣ حالة الإنشاء (Create) - بيكون فيه attributes بس
            else if (data.attributes) {
                Object.keys(data.attributes).forEach(key => {
                    let val = formatValue(data.attributes[key]);
                    let row = `
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-bold text-gray-700 capitalize bg-gray-50/50">${key.replace('_', ' ')}</td>
                        <td class="px-4 py-3 text-gray-400 italic">-</td>
                        <td class="px-4 py-3 text-green-600 bg-green-50/30 font-mono text-xs break-all">${val}</td>
                    </tr>
                `;
                    tbody.innerHTML += row;
                    hasData = true;
                });
            }

            // إظهار/إخفاء رسالة "لا يوجد تغييرات"
            if (hasData) {
                tbody.parentElement.classList.remove('hidden');
                noMsg.classList.add('hidden');
            } else {
                tbody.parentElement.classList.add('hidden');
                noMsg.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closeChangesModal() {
            document.getElementById('changesModal').classList.add('hidden');
        }

        // دالة مساعدة لتنسيق القيم (null, true, false, dates)
        function formatValue(val) {
            if (val === null || val === '') return '<span class="text-gray-300">null</span>';
            if (val === true) return 'true';
            if (val === false) return 'false';
            if (typeof val === 'string' && val.length > 30) {
                // لو نص طويل (زي تاريخ) ممكن نقصره أو نعرضه زي ما هو
                return val;
            }
            return val;
        }
    </script>
@endsection
