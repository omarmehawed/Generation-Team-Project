<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletDepositRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'notes',
        'phone_number',
        'transfer_date',
        'transfer_time',
        'screenshot_path',
        'status',
        'processed_by',
        'processed_at',
        'rejection_reason',
        'is_edited',
        'old_values',
    ];

    protected $appends = [
        'screenshot_url',
        'old_screenshot_url',
        'field_discrepancies',
    ];

    public function getScreenshotUrlAttribute()
    {
        if (!$this->screenshot_path) return null;
        if (str_starts_with($this->screenshot_path, 'http')) {
            return $this->screenshot_path;
        }
        return \Illuminate\Support\Facades\Storage::disk('r2')->url($this->screenshot_path);
    }

    public function getOldScreenshotUrlAttribute()
    {
        if (empty($this->old_values['screenshot_path'])) return null;
        if (str_starts_with($this->old_values['screenshot_path'], 'http')) {
            return $this->old_values['screenshot_path'];
        }
        return \Illuminate\Support\Facades\Storage::disk('r2')->url($this->old_values['screenshot_path']);
    }

    public function getFieldDiscrepanciesAttribute()
    {
        if (!$this->is_edited || empty($this->old_values)) {
            return null;
        }

        $discrepancies = [];
        $old = $this->old_values;

        if (isset($old['payment_method']) && $old['payment_method'] !== $this->payment_method) {
            $discrepancies['payment_method'] = [
                'before' => $old['payment_method'],
                'after' => $this->payment_method
            ];
        }

        if (isset($old['amount']) && (float)$old['amount'] !== (float)$this->amount) {
            $discrepancies['amount'] = [
                'before' => $old['amount'],
                'after' => $this->amount
            ];
        }

        if (isset($old['phone_number']) && $old['phone_number'] !== $this->phone_number) {
            $discrepancies['phone_number'] = [
                'before' => $old['phone_number'],
                'after' => $this->phone_number
            ];
        }

        $oldDate = isset($old['transfer_date']) ? \Carbon\Carbon::parse($old['transfer_date'])->format('Y-m-d') : null;
        $newDate = $this->transfer_date ? $this->transfer_date->format('Y-m-d') : null;
        if ($oldDate !== $newDate) {
            $discrepancies['transfer_date'] = [
                'before' => $oldDate,
                'after' => $newDate
            ];
        }

        if (isset($old['transfer_time']) && $old['transfer_time'] !== $this->transfer_time) {
            $discrepancies['transfer_time'] = [
                'before' => $old['transfer_time'],
                'after' => $this->transfer_time
            ];
        }
        
        if (isset($old['notes']) && $old['notes'] !== $this->notes) {
            $discrepancies['notes'] = [
                'before' => $old['notes'],
                'after' => $this->notes
            ];
        }

        $oldScreenshot = $this->old_screenshot_url;
        $newScreenshot = $this->screenshot_url;
        if ($oldScreenshot !== $newScreenshot && ($oldScreenshot || $newScreenshot)) {
            $discrepancies['screenshot_url'] = [
                'before' => $oldScreenshot,
                'after' => $newScreenshot
            ];
        }

        return $discrepancies;
    }

    protected $casts = [
        'transfer_date' => 'date',
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
        'is_edited' => 'boolean',
        'old_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
