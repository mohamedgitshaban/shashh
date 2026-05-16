<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends User
{
    use HasFactory;
    protected $table = 'users';
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->type = self::ROLE_CLIENT;
        });
    }
}
