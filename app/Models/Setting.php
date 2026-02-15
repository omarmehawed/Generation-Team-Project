<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    //  ضيف السطر ده ضروري عشان الحفظ يشتغل
    protected $fillable = ['key', 'value'];
}
