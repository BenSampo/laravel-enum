<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Traits\CastsEnums;
use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    public $timestamps = false;

    use CastsEnums;

    protected $casts = [
        'user_type' => 'int',
    ];

    protected $enumCasts = [
        'user_type' => UserType::class,
    ];

    protected $fillable = [
        'user_type',
    ];
}
