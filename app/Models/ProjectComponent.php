<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectComponent extends Model
{
    protected $fillable = ['team_id', 'name', 'description', 'image_path'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
