<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'image_path'];
    protected $hidden = [
        'updated_at',
        'user_id',
    ];
}