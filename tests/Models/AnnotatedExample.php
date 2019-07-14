<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Traits\CastsEnums;
use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Database\Eloquent\Model;

/**
 * Description should be kept
 *
 * @property \BenSampo\Enum\Tests\Enums\UserType|null $user_type
 * @see https://github.com/BenSampo/laravel-enum/pull/71
 */
class AnnotatedExample extends Model
{
    use CastsEnums;

    protected $enumCasts = [
        'user_type' => UserType::class,
    ];

    protected $fillable = [
        'user_type',
    ];
}
