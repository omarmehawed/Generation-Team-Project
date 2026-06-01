<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'created_by_id',
        'is_active',
        'deadline',
        'allow_edit_response',
        'is_required',
        'target_gender',
        'assigned_teams',
        'assigned_roles',
        'assigned_users',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_edit_response' => 'boolean',
        'is_required' => 'boolean',
        'deadline' => 'datetime',
        'assigned_teams' => 'array',
        'assigned_roles' => 'array',
        'assigned_users' => 'array',
    ];

    /**
     * Scope: mandatory forms that a specific user still needs to complete.
     * Filters by: is_required, is_active, target_gender match, and no existing response.
     */
    public function scopeMandatoryForUser($query, $user)
    {
        return $query->where('is_required', true)
            ->where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('target_gender', 'all');
                if ($user->gender) {
                    $q->orWhere('target_gender', $user->gender);
                }
            })
            ->whereDoesntHave('responses', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'asc');
    }

    /**
     * Scope: forms visible to a user based on their gender.
     */
    public function scopeVisibleToUser($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('target_gender', 'all');
            if ($user->gender) {
                $q->orWhere('target_gender', $user->gender);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function questions()
    {
        return $this->hasMany(FormQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }
}
