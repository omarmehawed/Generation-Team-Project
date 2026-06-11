<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill wallet_transactions for all previously rejected deposit requests
     * that don't already have a corresponding transaction record.
     */
    public function up(): void
    {
        $rejectedRequests = DB::table('wallet_deposit_requests')
            ->where('status', 'rejected')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('wallet_transactions')
                    ->whereColumn('wallet_transactions.deposit_request_id', 'wallet_deposit_requests.id');
            })
            ->get();

        foreach ($rejectedRequests as $req) {
            $methodName = str_replace('_', ' ', strtoupper($req->payment_method));
            $notes = "Rejected deposit via {$methodName}";
            if ($req->rejection_reason) {
                $notes .= " — Reason: " . $req->rejection_reason;
            }

            // Get the user's current wallet balance (since rejected didn't change it)
            $user = DB::table('users')->where('id', $req->user_id)->first();

            DB::table('wallet_transactions')->insert([
                'user_id'            => $req->user_id,
                'admin_id'           => $req->processed_by ?? $req->user_id,
                'type'               => 'rejected',
                'amount'             => $req->amount,
                'balance_after'      => $user->wallet_balance ?? 0,
                'notes'              => $notes,
                'deposit_request_id' => $req->id,
                'created_at'         => $req->processed_at ?? $req->updated_at ?? $req->created_at,
                'updated_at'         => $req->processed_at ?? $req->updated_at ?? $req->created_at,
            ]);
        }
    }

    /**
     * Remove the backfilled rejected transaction records.
     */
    public function down(): void
    {
        // Only delete wallet_transactions that were backfilled (type=rejected with a deposit_request_id)
        DB::table('wallet_transactions')
            ->where('type', 'rejected')
            ->whereNotNull('deposit_request_id')
            ->delete();
    }
};
