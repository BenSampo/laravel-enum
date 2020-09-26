<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Traits\FlaggedEnumQueries;
use Illuminate\Database\Eloquent\Model;

class ModelWithFlaggedQueries extends Model
{
    use FlaggedEnumQueries;

    public $table = 'test_models';
    
    protected $guarded = [];
}
