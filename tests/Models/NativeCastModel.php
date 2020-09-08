<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\UserTypeCustomCast;
use BenSampo\Enum\Tests\Enums\UserTypeNullable;
use Illuminate\Database\Eloquent\Model;

class NativeCastModel extends Model
{
    protected $casts = [
        'user_type' => UserType::class,
        'user_type_custom' => UserTypeCustomCast::class,
        'user_type_nullable' => UserTypeNullable::class,
    ];

    protected $fillable = [
        'user_type',
        'user_type_custom',
        'user_type_nullable'
    ];
}
