<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Casts\EnumCast;
use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Database\Eloquent\Model;

class NativeCastModel extends Model
{
    protected $casts = [
        'user_type' => UserType::class,
    ];

    protected $fillable = [
        'user_type',
    ];
}
