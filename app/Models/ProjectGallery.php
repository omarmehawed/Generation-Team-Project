<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectGallery extends Model
{
    protected $table = 'project_gallery';
    protected $fillable =
    
     ['team_id', 'file_path', 'type', 'caption', 'uploaded_by'];
}
