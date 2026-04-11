<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionArchive extends Model
{
    protected $fillable = ['name', 'color', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(JoinRequestQuestion::class, 'archive_id');
    }
}
