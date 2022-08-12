<?php declare(strict_types=1);

namespace BenSampo\Enum\Traits;

use Illuminate\Database\Eloquent\Builder;

trait QueriesFlaggedEnums
{
    public function scopeHasFlag(Builder $query, string $column, int $flag): Builder
    {
        return $query->whereRaw("$column & ? > 0", [$flag]);
    }

    public function scopeNotHasFlag(Builder $query, string $column, int $flag): Builder
    {
        return $query->whereRaw("not $column & ? > 0", [$flag]);
    }

    /**
     * @param array<int> $flags
     */
    public function scopeHasAllFlags(Builder $query, string $column, array $flags): Builder
    {
        $mask = array_sum($flags);

        return $query->whereRaw("$column & ? = ?", [$mask, $mask]);
    }

    /**
     * @param array<int> $flags
     */
    public function scopeHasAnyFlags(Builder $query, string $column, array $flags): Builder
    {
        $mask = array_sum($flags);

        return $query->whereRaw("$column & ? > 0", [$mask]);
    }
}
