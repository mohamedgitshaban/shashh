<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends User
{
    use HasFactory;

    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', self::ROLE_COMPANY);
        });

        static::creating(function ($model) {
            $model->type = self::ROLE_COMPANY;
        });
    }
}
