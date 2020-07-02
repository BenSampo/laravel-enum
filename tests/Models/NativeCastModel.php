<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Casts\EnumCast;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\UserTypeCustomCast;
use Illuminate\Database\Eloquent\Model;

class NativeCastModel extends Model
{
    protected $casts = [
        'user_type' => UserType::class,
        'user_type_custom' => UserTypeCustomCast::class,
    ];

    protected $fillable = [
        'user_type',
        'user_type_custom',
    ];
}
