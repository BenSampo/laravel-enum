<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;

/**
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 * Apparently there's an issue with really long docblocks, they say!
 *
 *
 * @property \BenSampo\Enum\Tests\Enums\UserType|null $user_type
 * @see https://github.com/BenSampo/laravel-enum/issues/120
 *
 */
class AnnotatedLargeExample extends Model
{
    use CastsEnums;

    protected $enumCasts = [
        'user_type' => UserType::class,
    ];

    protected $fillable = [
        'user_type',
    ];
}
