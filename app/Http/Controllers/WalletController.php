<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WalletDepositRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Notifications\BatuNotification;

class WalletController extends Controller
{
    /**
     * Display the wallet interface (Search + History).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $isGlobalAdmin = $user->hasPermission('wallet_management');
        $isLeader = \App\Models\Team::where('leader_id', $user->id)->exists();
        
        $hasManagement = $isGlobalAdmin || $isLeader;

        $pendingCount = 0;

        if ($hasManagement) {
            $query = WalletTransaction::with(['user', 'admin', 'depositRequest.processor'])->latest();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "{$search}%");
                });
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if (!$isGlobalAdmin && $isLeader) {
                // Scope everything strictly to the Leader's Team
                $teamMemberIds = \App\Models\TeamMember::whereIn('team_id', function($q) use ($user) {
                    $q->select('id')->from('teams')->where('leader_id', $user->id);
                })->pluck('user_id');

                $query->whereIn('user_id', $teamMemberIds);
                $totalBalance = User::whereIn('id', $teamMemberIds)->sum('wallet_balance');
                $pendingCount = WalletDepositRequest::whereIn('user_id', $teamMemberIds)->where('status', 'pending')->count();
            } else {
                $totalBalance = User::sum('wallet_balance');
                $pendingCount = WalletDepositRequest::where('status', 'pending')->count();
            }

            $transactions = $query->paginate(20);

            return view('wallet.index', compact('transactions', 'totalBalance', 'hasManagement', 'pendingCount'));
        } else {
            // Normal member view: Unified History
            $transactions = WalletTransaction::with(['admin', 'depositRequest.processor'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
            
            // Still get pending requests to show them as "In Progress"
            $myRequests = WalletDepositRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->get();

            return view('wallet.index', compact('transactions', 'myRequests', 'hasManagement', 'pendingCount'));
        }
    }

    /**
     * Search for a user by Academic Number (Email prefix).
     */
    public function search(Request $request)
    {
        $this->authorizeAccess();

        $request->validate(['academic_id' => 'required|string']);

        $academicId = trim($request->academic_id);

        // Logic:
        // 1. Check if 'email' starts with the ID (e.g. 2420823@...)
        // 2. Check if 'email' IS the ID (in case they typed full email)
        
        $user = User::where('email', 'LIKE', "{$academicId}@%")
                    ->orWhere('email', $academicId)
                    ->orWhere('university_email', 'LIKE', "{$academicId}@%")
                    ->orWhere('university_email', $academicId)
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Calculate academic_id for display if column is null
        $displayId = $user->academic_id ?? Str::before($user->email, '@');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'balance' => $user->wallet_balance ?? 0.00,
            'academic_id' => $displayId,
            'avatar' => $user->profile_photo_url,
        ]);
    }

    /**
     * Perform a deposit or withdrawal.
     */
    public function transact(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type'    => 'required|in:deposit,withdrawal',
            'amount'  => 'required|numeric|min:0.1',
            'notes'   => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);

        // Custom Authorization supporting scoped Leaders or Admins
        $isLeader = \App\Models\Team::where('leader_id', Auth::id())
            ->whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->exists();

        if (!Auth::user()->hasPermission('wallet_management') && !$isLeader) {
            abort(403, 'Unauthorized access to make transactions for this student.');
        }

        $amount = $request->amount;
        $type = $request->type;

        if ($type === 'withdrawal' && $user->wallet_balance < $amount) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Insufficient balance for withdrawal!'], 422);
            }
            return back()->with('error', 'Insufficient balance for withdrawal!');
        }

        // 1. Update User Balance
        if ($type === 'deposit') {
            $user->increment('wallet_balance', $amount);
        } else {
            $user->decrement('wallet_balance', $amount);
        }

        // 2. Log Transaction
        WalletTransaction::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $user->wallet_balance,
            'notes' => filled($request->notes) ? $request->notes : "Manual " . ucfirst($type),
        ]);

        // 3. Notify User
        $user->notify(new BatuNotification([
            'title' => ($type === 'deposit' ? '💰 Wallet Credited' : '💸 Wallet Debited'),
            'message' => "Your wallet has been " . ($type === 'deposit' ? "credited with" : "debited by") . " {$amount} EGP by Admin.",
            'icon' => ($type === 'deposit' ? 'fas fa-coins' : 'fas fa-wallet'),
            'color' => ($type === 'deposit' ? 'text-green-500' : 'text-red-500'),
            'action_url' => route('wallet.index'),
            'type' => 'info'
        ]));

        $message = ucfirst($type) . " of {$amount} completed for {$user->name}.";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('wallet.index')
            ]);
        }

        return back()->with('success', $message);
    }

    public function activeBalances(Request $request)
    {
        $this->authorizeAccess();

        $user = Auth::user();
        $isGlobalAdmin = $user->hasPermission('wallet_management');
        
        $query = User::where('wallet_balance', '>', 0);
        
        if (!$isGlobalAdmin) {
            $teamMemberIds = \App\Models\TeamMember::whereIn('team_id', function($q) use ($user) {
                $q->select('id')->from('teams')->where('leader_id', $user->id);
            })->pluck('user_id');
            $query->whereIn('id', $teamMemberIds);
        }

        $users = $query->get();

        return response()->json([
            'users' => $users->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->profile_photo_url,
                'academic_id' => $u->university_email ?? $u->email,
                'balance' => $u->wallet_balance,
            ]),
            'total_balance' => $users->sum('wallet_balance'),
        ]);
    }

    public function searchMember(Request $request)
    {
        $this->authorizeAccess();

        $query = $request->get('query');
        if (empty($query)) return response()->json(null);

        $user = User::where(function($q) use ($query) {
            $q->where('university_email', 'like', $query . '%')
              ->orWhere('email', 'like', $query . '%');
        });

        if (!Auth::user()->hasPermission('wallet_management')) {
            $teamMemberIds = \App\Models\TeamMember::whereIn('team_id', function($q) {
                $q->select('id')->from('teams')->where('leader_id', Auth::id());
            })->pluck('user_id');
            $user->whereIn('id', $teamMemberIds);
        }

        $user = $user->first();

        if (!$user) return response()->json(null);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->profile_photo_url,
            'academic_id' => $user->university_email ?? $user->email,
        ]);
    }

    /**
     * Apply a bulk operation (deposit/withdrawal) to selected members.
     */
    public function bulkTransact(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'type'       => 'required|in:deposit,withdrawal',
            'amount'     => 'required|numeric|min:0.1',
            'user_ids'   => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;
        $amount = $request->amount;
        $type = $request->type;
        $adminId = Auth::id();

        $adminUser = Auth::user();
        
        $query = User::whereIn('id', $userIds);
        
        if (!$adminUser->hasPermission('wallet_management')) {
            $teamMemberIds = \App\Models\TeamMember::whereIn('team_id', function($q) use ($adminUser) {
                $q->select('id')->from('teams')->where('leader_id', $adminUser->id);
            })->pluck('user_id');
            $query->whereIn('id', $teamMemberIds);
        }

        $users = $query->get();
        
        if ($users->isEmpty()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No valid members selected.'], 422);
            }
            return back()->with('error', 'No valid members selected.');
        }

        $count = 0;
        foreach ($users as $user) {
            $appliedAmount = $amount;
            
            if ($type === 'withdrawal') {
                $appliedAmount = min($user->wallet_balance, $amount);
                if ($appliedAmount <= 0) continue;
                $user->decrement('wallet_balance', $appliedAmount);
            } else {
                $user->increment('wallet_balance', $appliedAmount);
            }

            WalletTransaction::create([
                'user_id' => $user->id,
                'admin_id' => $adminId,
                'type' => $type,
                'amount' => $appliedAmount,
                'balance_after' => $user->wallet_balance,
                'notes' => "Bulk " . ucfirst($type),
            ]);

            // Notify
            $user->notify(new BatuNotification([
                'title' => ($type === 'deposit' ? '💰 Wallet Credited' : '💸 Wallet Debited'),
                'message' => "Your wallet has been " . ($type === 'deposit' ? "credited with" : "debited by") . " {$appliedAmount} EGP (Bulk Operation).",
                'icon' => ($type === 'deposit' ? 'fas fa-coins' : 'fas fa-wallet'),
                'color' => ($type === 'deposit' ? 'text-green-500' : 'text-red-500'),
                'action_url' => route('wallet.index'),
                'type' => 'info'
            ]));
            
            $count++;
        }

        $message = "Bulk " . ucfirst($type) . " applied to {$count} members.";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('wallet.index')
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Submit a deposit request (Member Side).
     */
    public function submitDepositRequest(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,vodafone_cash,instapay',
            'amount' => 'required|numeric|min:1',
            'notes' => 'required_if:payment_method,cash|nullable|string',
            'phone_number' => 'required_if:payment_method,vodafone_cash,instapay|nullable|string',
            'transfer_date' => 'required_if:payment_method,vodafone_cash,instapay|nullable|date',
            'transfer_time' => 'required_if:payment_method,vodafone_cash,instapay|nullable',
            'screenshot' => 'required_if:payment_method,vodafone_cash,instapay|nullable|image|max:10240',
        ]);

        $data = $request->only(['payment_method', 'amount', 'notes', 'phone_number', 'transfer_date', 'transfer_time']);
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('wallet/screenshots', 'r2');
            $data['screenshot_path'] = Storage::disk('r2')->url($path);
        }

        WalletDepositRequest::create($data);

        $message = 'Deposit request submitted successfully! Waiting for review.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('wallet.index')
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Update a pending deposit request (Member Side).
     */
    public function updateDepositRequest(Request $request, $id)
    {
        $depositRequest = WalletDepositRequest::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($depositRequest->status !== 'pending') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only pending requests can be edited.'], 422);
            }
            return back()->with('error', 'Only pending requests can be edited.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,vodafone_cash,instapay',
            'amount' => 'required|numeric|min:1',
            'notes' => 'required_if:payment_method,cash|nullable|string',
            'phone_number' => 'required_if:payment_method,vodafone_cash,instapay|nullable|string',
            'transfer_date' => 'required_if:payment_method,vodafone_cash,instapay|nullable|date',
            'transfer_time' => 'required_if:payment_method,vodafone_cash,instapay|nullable',
            'screenshot' => 'nullable|image|max:10240',
        ]);

        $data = $request->only(['payment_method', 'amount', 'notes', 'phone_number', 'transfer_date', 'transfer_time']);

        // Capture old values on first edit
        if (is_null($depositRequest->old_values)) {
            $depositRequest->old_values = [
                'amount' => $depositRequest->amount,
                'payment_method' => $depositRequest->payment_method,
                'notes' => $depositRequest->notes,
                'phone_number' => $depositRequest->phone_number,
                'transfer_date' => $depositRequest->transfer_date ? $depositRequest->transfer_date->format('Y-m-d') : null,
                'transfer_time' => $depositRequest->transfer_time,
                'screenshot_path' => $depositRequest->screenshot_path,
            ];
        }
        $data['is_edited'] = true;
        $data['old_values'] = $depositRequest->old_values;

        // Check if screenshot is required due to missing old one and changed payment method
        if (in_array($request->payment_method, ['vodafone_cash', 'instapay']) && !$depositRequest->screenshot_path && !$request->hasFile('screenshot')) {
            return back()->withErrors(['screenshot' => 'The screenshot is required.']);
        }

        if ($request->hasFile('screenshot')) {
            // REMOVED deletion logic to keep old screenshot for audit trail
            $path = $request->file('screenshot')->store('wallet/screenshots', 'r2');
            $data['screenshot_path'] = Storage::disk('r2')->url($path);
        } else if ($request->payment_method === 'cash') {
            $data['screenshot_path'] = null; // Clear if changed to cash
        }

        $depositRequest->update($data);

        $message = 'Deposit request updated successfully!';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('wallet.index')
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Get all pending deposit requests (Leader/Authorized Side).
     */
    public function getDepositRequests()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasPermission('deposit_requests') || $user->hasPermission('wallet_management');
        $isLeader = \App\Models\Team::where('leader_id', $user->id)->exists();

        if (!$isGlobalAdmin && !$isLeader) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = WalletDepositRequest::with('user')->where('status', 'pending')->latest();
        
        if (!$isGlobalAdmin && $isLeader) {
            $teamMemberIds = \App\Models\TeamMember::whereIn('team_id', function($q) use ($user) {
                $q->select('id')->from('teams')->where('leader_id', $user->id);
            })->pluck('user_id');
            $query->whereIn('user_id', $teamMemberIds);
        }

        $requests = $query->get();
        return response()->json($requests);
    }

    /**
     * Process a deposit request (Approve/Reject).
     */
    public function processDepositRequest(Request $request, $id)
    {
        $user = Auth::user();
        $depositRequest = WalletDepositRequest::findOrFail($id);
        
        $isGlobalAdmin = $user->hasPermission('deposit_requests') || $user->hasPermission('wallet_management');
        
        if (!$isGlobalAdmin) {
            $isLeader = \App\Models\Team::where('leader_id', $user->id)
                ->whereHas('members', function($q) use ($depositRequest) {
                    $q->where('user_id', $depositRequest->user_id);
                })->exists();
                
            if (!$isLeader) {
                abort(403, 'Unauthorized');
            }
        }

        $request->validate([
            'action' => 'required|in:accept,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string',
        ]);

        $depositRequest = WalletDepositRequest::findOrFail($id);
        
        if ($depositRequest->status !== 'pending') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Request already processed.'], 422);
            }
            return back()->with('error', 'Request already processed.');
        }

        if ($request->action === 'accept') {
            $user = $depositRequest->user;
            $user->increment('wallet_balance', $depositRequest->amount);

            $methodName = str_replace('_', ' ', strtoupper($depositRequest->payment_method));
            $txnNotes = "Deposit via {$methodName}";
            if ($depositRequest->notes) {
                $txnNotes .= ": " . $depositRequest->notes;
            }

            WalletTransaction::create([
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'type' => 'deposit',
                'amount' => $depositRequest->amount,
                'balance_after' => $user->wallet_balance,
                'notes' => $txnNotes,
                'deposit_request_id' => $depositRequest->id,
            ]);

            $depositRequest->update([
                'status' => 'accepted',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            // 3. Notify User
            $user->notify(new BatuNotification([
                'title' => '✅ Deposit Approved',
                'message' => "Your deposit request for {$depositRequest->amount} EGP has been approved.",
                'icon' => 'fas fa-check-circle',
                'color' => 'text-green-500',
                'action_url' => route('wallet.index'),
                'type' => 'success'
            ]));

            $message = 'Deposit request accepted and balance updated.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('wallet.index')
                ]);
            }
            return back()->with('success', $message);
        } else {
            $depositRequest->update([
                'status' => 'rejected',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Log the rejected transaction so it appears in history
            $targetUser = $depositRequest->user;
            $methodName = str_replace('_', ' ', strtoupper($depositRequest->payment_method));
            $rejNotes = "Rejected deposit via {$methodName}";
            if ($request->rejection_reason) {
                $rejNotes .= " — Reason: " . $request->rejection_reason;
            }

            WalletTransaction::create([
                'user_id' => $targetUser->id,
                'admin_id' => Auth::id(),
                'type' => 'rejected',
                'amount' => $depositRequest->amount,
                'balance_after' => $targetUser->wallet_balance,
                'notes' => $rejNotes,
                'deposit_request_id' => $depositRequest->id,
            ]);

            // Notify User
            $depositRequest->user->notify(new BatuNotification([
                'title' => '❌ Deposit Rejected',
                'message' => "Your deposit request for {$depositRequest->amount} EGP was rejected. Reason: " . $request->rejection_reason,
                'icon' => 'fas fa-times-circle',
                'color' => 'text-red-500',
                'action_url' => route('wallet.index'),
                'type' => 'alert'
            ]));

            $message = 'Deposit request rejected.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('wallet.index')
                ]);
            }
            return back()->with('success', $message);
        }
    }

    /**
     * Export Active Balances to Excel (.xlsx).
     */
    public function exportActiveBalances(Request $request)
    {
        $this->authorizeAccess();

        $user = Auth::user();
        $isGlobalAdmin = $user->hasPermission('wallet_management');

        $query = User::where('wallet_balance', '>', 0);

        if (!$isGlobalAdmin) {
            $teamMemberIds = \App\Models\TeamMember::whereIn('team_id', function($q) use ($user) {
                $q->select('id')->from('teams')->where('leader_id', $user->id);
            })->pluck('user_id');
            $query->whereIn('id', $teamMemberIds);
        }

        $users = $query->get();

        $filename = 'Active_Balance_Report_' . now()->format('Y-m-d_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ActiveBalanceExport($users),
            $filename
        );
    }

    /**
     * Restrict access to specific emails.
     */
    private function authorizeAccess()
    {
        $user = Auth::user();
        if ($user->hasPermission('wallet_management')) {
            return;
        }
        
        if (\App\Models\Team::where('leader_id', $user->id)->exists()) {
            return;
        }
        
        abort(403, 'Unauthorized access.');
    }
}
