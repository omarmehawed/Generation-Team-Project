<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * Display the wallet interface (Search + History).
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = WalletTransaction::with(['user', 'admin'])->latest();

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

        return view('wallet.index', compact('transactions'));
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
        ]);

        return back()->with('success', ucfirst($type) . " of {$amount} completed for {$user->name}.");
    }

    /**
     * Restrict access to specific emails.
     */
    private function authorizeAccess()
    {
        $allowed = ['2420823@batechu.com', '2420324@batechu.com'];
        if (!in_array(Auth::user()->email, $allowed)) {
            abort(403, 'Unauthorized access.');
        }
    }
}
