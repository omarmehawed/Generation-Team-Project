<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class WalletController extends Controller
{
    /**
     * Handle Wallet Transaction (Add / Withdraw)
     */
    public function transaction(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:add,withdraw',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $amount = $request->amount;
            $type = $request->type;

            // Authorization Check: Only Team Leader or Admin can maximize wallet
            $requester = Auth::user();

            if ($requester->role === 'admin') {
                // Admin allowed
            } else {
                // Check if Requester is the Leader of the Team that the Target User belongs to
                $isLeader = \App\Models\Team::where('leader_id', $requester->id)
                    ->whereHas('members', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->exists();

                if (!$isLeader) {
                    return back()->with('error', 'Unauthorized! Only the Team Leader can manage wallet.');
                }
            }

            if ($type === 'withdraw') {
                if ($user->wallet_balance < $amount) {
                    return back()->with('error', 'Insufficient balance!');
                }
                $user->decrement('wallet_balance', $amount);
                $message = 'Amount withdrawn successfully.';
            } else {
                $user->increment('wallet_balance', $amount);
                $message = 'Amount added successfully.';
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
             return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }
}
