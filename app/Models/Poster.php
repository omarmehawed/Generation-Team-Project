<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'image_size',
        'template_type',
        'images',
        'links',
        'text_color',
        'text_position',
        'order',
        'layout_settings',
        'created_by',
    ];

    protected $casts = [
        'layout_settings' => 'array',
        'images' => 'array',
        'links' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
