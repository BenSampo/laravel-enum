<?php

namespace BenSampo\Enum\Traits;

trait QueriesFlaggedEnums
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

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param int[] $flags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasAllFlags($query, $column, $flags)
    {
        $mask = array_sum($flags);

        return $query->whereRaw("$column & ? = ?", [$mask, $mask]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param int[] $flags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasAnyFlags($query, $column, $flags)
    {
        $mask = array_sum($flags);

        return $query->whereRaw("$column & ? > 0", [$mask]);
    }
}
