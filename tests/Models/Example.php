<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Database\Eloquent\Model;

/**
 * @property UserType $user_type
 */
class Example extends Model
{
    public $timestamps = false;

    protected $casts = [
        'user_type' => UserType::class,
    ];

    protected $fillable = [
        'user_type',
    ];
}
