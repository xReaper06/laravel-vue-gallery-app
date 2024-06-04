<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_info extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'firstname',
        'middlename',
        'lastname',
        'birthday',
        'gender',
        'street',
        'baranggay',
        'city',
        'province'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function user_info_belong()
    {
        return $this->belongsTo(User::class, 'id');
    }
}