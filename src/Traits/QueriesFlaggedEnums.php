<?php declare(strict_types=1);

namespace BenSampo\Enum\Traits;

use Illuminate\Database\Eloquent\Builder;

trait QueriesFlaggedEnums
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeHasFlag(Builder $query, string $column, int $flag): Builder
    {
        return $query->whereRaw("$column & ? > 0", [$flag]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeNotHasFlag(Builder $query, string $column, int $flag): Builder
    {
        return $query->whereRaw("not $column & ? > 0", [$flag]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     * @param  array<int> $flags
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeHasAllFlags(Builder $query, string $column, array $flags): Builder
    {
        $mask = array_sum($flags);

        return $query->whereRaw("$column & ? = ?", [$mask, $mask]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     * @param  array<int> $flags
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeHasAnyFlags(Builder $query, string $column, array $flags): Builder
    {
        $mask = array_sum($flags);

        return $query->whereRaw("$column & ? > 0", [$mask]);
    }
}
