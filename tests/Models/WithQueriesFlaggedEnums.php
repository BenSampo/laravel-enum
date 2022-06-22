<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Traits\QueriesFlaggedEnums;
use Illuminate\Database\Eloquent\Model;

class WithQueriesFlaggedEnums extends Model
{
    use QueriesFlaggedEnums;

    public $table = 'test_models';
    
    protected $guarded = [];
}
