<?php

namespace BenSampo\Enum\Tests\Models;

use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;

class WithTraitButNoCasts extends Model
{
    use CastsEnums;
}
