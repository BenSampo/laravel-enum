<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Traits\CastsEnums;
use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Database\Eloquent\Model;

class ExampleArrayable extends Model
{
    use CastsEnums;

    protected $casts = [
        'user_types' => 'array',
    ];

    protected $enumCasts = [
        'user_types' => UserType::class,
    ];

    protected $fillable = [
        'user_types',
    ];
}
