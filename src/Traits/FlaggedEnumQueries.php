<?php

namespace BenSampo\Enum\Traits;

use BenSampo\Enum\FlaggedEnum;

trait FlaggedEnumQueries
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param int $flag
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasFlag($query, $column, $flag)
    {
        return $query->whereRaw("$column & ? > 0", [$flag]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param int $flag
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotHasFlag($query, $column, $flag)
    {
        return $query->whereRaw("not $column & ? > 0", [$flag]);
    }
}
