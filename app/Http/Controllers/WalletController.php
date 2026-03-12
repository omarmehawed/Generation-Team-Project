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
        $hasManagement = $user->hasPermission('wallet_management') || $user->email === '2420823@batechu.com';

        $pendingCount = 0;

        if ($hasManagement) {
            $query = WalletTransaction::with(['user', 'admin'])->latest();

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

            $transactions = $query->paginate(20);
            $totalBalance = User::sum('wallet_balance');
            $pendingCount = WalletDepositRequest::where('status', 'pending')->count();

            return view('wallet.index', compact('transactions', 'totalBalance', 'hasManagement', 'pendingCount'));
        } else {
            // Normal member view: Unified History
            $transactions = WalletTransaction::with(['admin', 'depositRequest'])
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
        $this->authorizeAccess();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type'    => 'required|in:deposit,withdrawal',
            'amount'  => 'required|numeric|min:0.1',
            'notes'   => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = $request->amount;
        $type = $request->type;

        if ($type === 'withdrawal' && $user->wallet_balance < $amount) {
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
            'notes' => $request->notes ?? "Manual " . ucfirst($type),
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

        return back()->with('success', ucfirst($type) . " of {$amount} completed for {$user->name}.");
    }

    /**
     * Apply a bulk operation (deposit/withdrawal) to all members with balance > 0.
     */
    public function bulkTransact(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'type'    => 'required|in:deposit,withdrawal',
            'amount'  => 'required|numeric|min:0.1',
        ]);

        $amount = $request->amount;
        $type = $request->type;
        $adminId = Auth::id();

        // Only members with balance > 0
        $users = User::where('wallet_balance', '>', 0)->get();
        
        if ($users->isEmpty()) {
            return back()->with('error', 'No members with balance > 0 found.');
        }

        $count = 0;
        foreach ($users as $user) {
            $appliedAmount = $amount;
            
            if ($type === 'withdrawal') {
                $appliedAmount = min($user->wallet_balance, $amount);
                $user->decrement('wallet_balance', $appliedAmount);
            } else {
                $appliedAmount = $amount;
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

        return back()->with('success', "Bulk " . ucfirst($type) . " applied to {$count} members.");
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

        return back()->with('success', 'Deposit request submitted successfully! Waiting for review.');
    }

    /**
     * Get all pending deposit requests (Leader/Authorized Side).
     */
    public function getDepositRequests()
    {
        if (!Auth::user()->hasPermission('deposit_requests')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $requests = WalletDepositRequest::with('user')->where('status', 'pending')->latest()->get();
        return response()->json($requests);
    }

    /**
     * Process a deposit request (Approve/Reject).
     */
    public function processDepositRequest(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('deposit_requests')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'action' => 'required|in:accept,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string',
        ]);

        $depositRequest = WalletDepositRequest::findOrFail($id);
        
        if ($depositRequest->status !== 'pending') {
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

            return back()->with('success', 'Deposit request accepted and balance updated.');
        } else {
            $depositRequest->update([
                'status' => 'rejected',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
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

            return back()->with('success', 'Deposit request rejected.');
        }
    }

    /**
     * Restrict access to specific emails.
     */
    private function authorizeAccess()
    {
        $user = Auth::user();
        if ($user->email === '2420823@batechu.com' || $user->hasPermission('wallet_management')) {
            return;
        }
        abort(403, 'Unauthorized access.');
    }
}
