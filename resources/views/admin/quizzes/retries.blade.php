@extends('layouts.batu')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100"><i class="fas fa-undo-alt text-yellow-500 mr-2"></i> Quiz Retry Requests</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Review requests from members who were disqualified and are asking for another attempt.</p>
        </div>
        <a href="{{ route('admin.quizzes.index') }}" class="btn-royal-gold py-2 px-6 rounded-xl font-bold shadow-md">
            Go to Quizzes <i class="fas fa-arrow-right ml-2 opacity-70"></i>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700 uppercase tracking-wider text-xs text-gray-500 dark:text-gray-400 font-black">
                    <th class="p-4">Member</th>
                    <th class="p-4">Quiz details</th>
                    <th class="p-4">Status & Details</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $request)
                    <tr class="hover:bg-gray-50 transition border-b border-gray-100 dark:border-gray-700">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold shrink-0">
                                    {{ strtoupper(substr($request->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-gray-200">{{ $request->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $request->user->department ?? 'Member' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $request->quiz->title }}</p>
                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded-md">Attempt #{{ $request->attempt->attempt_number ?? 1 }}</span>
                        </td>
                        <td class="p-4">
                            <div class="mb-2">
                                @if($request->status == 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded inline-block"><i class="fas fa-clock mr-1"></i> Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded inline-block"><i class="fas fa-check mr-1"></i> Approved</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded inline-block"><i class="fas fa-times mr-1"></i> Rejected</span>
                                @endif
                                
                                <span class="text-xs text-gray-400 ml-2">{{ $request->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border border-gray-100 dark:border-gray-700 text-sm">
                                <span class="font-bold text-gray-500 dark:text-gray-400 block mb-1 text-xs uppercase">Member's Reason:</span>
                                <p class="text-gray-800 dark:text-gray-200">{{ $request->reason }}</p>
                            </div>

                            @if($request->admin_notes)
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 bg-blue-50 p-2 rounded">
                                    <strong>Admin Note:</strong> {{ $request->admin_notes }}
                                </div>
                            @endif
                        </td>
                        <td class="p-4 text-right align-middle">
                            @if($request->status == 'pending')
                                <button type="button" onclick="openReviewModal({{ $request->id }})" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 shadow-sm transition">
                                    Review Request
                                </button>

                                <form id="review-form-{{ $request->id }}" action="{{ route('admin.quizzes.retries.review', $request->id) }}" method="POST" class="hidden">
                                    @csrf
                                    <input type="hidden" name="status" id="status-{{ $request->id }}" value="">
                                    <textarea name="admin_notes" id="notes-{{ $request->id }}" class="hidden"></textarea>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs font-bold uppercase"><i class="fas fa-lock"></i> Closed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500 dark:text-gray-400 font-bold">No retry requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <div class="absolute top-4 right-4 cursor-pointer text-gray-400 hover:text-black" onclick="closeReviewModal()">
            <i class="fas fa-times text-xl"></i>
        </div>
        
        <h3 class="text-2xl font-black text-gray-900 dark:text-gray-100 mb-2">Review Request</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 font-bold">Provide optional notes, then approve or reject the member's request to retake the exam.</p>
        
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Admin Notes (Optional)</label>
        <textarea id="modal-notes" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 mb-6" placeholder="Example: Approved due to valid server timeout..."></textarea>
        
        <div class="flex gap-3">
            <button onclick="submitReview('rejected')" class="flex-1 bg-red-100 text-red-700 hover:bg-red-200 font-black py-3 rounded-xl transition">
                <i class="fas fa-times mr-1"></i> Reject
            </button>
            <button onclick="submitReview('approved')" class="flex-1 bg-green-500 text-white hover:bg-green-600 font-black py-3 rounded-xl shadow-md transition">
                <i class="fas fa-check mr-1"></i> Approve
            </button>
        </div>
    </div>
</div>

<script>
    let activeRequestId = null;

    function openReviewModal(id) {
        activeRequestId = id;
        document.getElementById('modal-notes').value = '';
        const modal = document.getElementById('review-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeReviewModal() {
        activeRequestId = null;
        const modal = document.getElementById('review-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitReview(status) {
        if (!activeRequestId) return;
        
        const notes = document.getElementById('modal-notes').value;
        document.getElementById('status-' + activeRequestId).value = status;
        document.getElementById('notes-' + activeRequestId).value = notes;
        
        document.getElementById('review-form-' + activeRequestId).submit();
    }
</script>
@endsection
