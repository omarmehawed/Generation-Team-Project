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
    ];

    protected $appends = [
        'screenshot_url',
    ];

    public function getScreenshotUrlAttribute()
    {
        if (!$this->screenshot_path) return null;
        if (str_starts_with($this->screenshot_path, 'http')) {
            return $this->screenshot_path;
        }
        return \Illuminate\Support\Facades\Storage::disk('r2')->url($this->screenshot_path);
    }

    protected $casts = [
        'transfer_date' => 'date',
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
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
