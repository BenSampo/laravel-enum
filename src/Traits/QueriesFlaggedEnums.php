<?php declare(strict_types=1);

namespace BenSampo\Enum\Traits;

use BenSampo\Enum\FlaggedEnum;
use Illuminate\Database\Eloquent\Builder;

trait QueriesFlaggedEnums
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeHasFlag(Builder $query, string $column, int|FlaggedEnum $flag): Builder
    {
        return $query->whereRaw("{$column} & ? > 0", [$flag]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeNotHasFlag(Builder $query, string $column, int|FlaggedEnum $flag): Builder
    {
        return $query->whereRaw("not {$column} & ? > 0", [$flag]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     * @param  array<int|FlaggedEnum> $flags
     *
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeHasAllFlags(Builder $query, string $column, array $flags): Builder
    {
        $mask = $this->flagsSum($flags);

        return $query->whereRaw("{$column} & ? = ?", [$mask, $mask]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     * @param  array<int|FlaggedEnum> $flags
     *
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeHasAnyFlags(Builder $query, string $column, array $flags): Builder
    {
        $mask = $this->flagsSum($flags);

        return $query->whereRaw("{$column} & ? > 0", [$mask]);
    }

    /** @param  array<int|FlaggedEnum> $flags */
    protected function flagsSum(array $flags): int
    {
        return array_reduce(
            $flags,
            static fn (int $carry, int|FlaggedEnum $flag): int => $carry
                + ($flag instanceof FlaggedEnum
                    ? $flag->value
                    : $flag),
            0
        );
    }
}
