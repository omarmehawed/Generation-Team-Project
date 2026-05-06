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
        'assigned_teams',
        'assigned_roles',
        'assigned_users',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_edit_response' => 'boolean',
        'deadline' => 'datetime',
        'assigned_teams' => 'array',
        'assigned_roles' => 'array',
        'assigned_users' => 'array',
    ];

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
