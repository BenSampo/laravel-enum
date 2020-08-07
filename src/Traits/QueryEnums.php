<?php

namespace BenSampo\Enum\Traits;

use BenSampo\Enum\Enum;

/**
 * This Trait will add HasFlag feature for every model, implement it.
 */
trait QueryEnums
{

/**
 * This Scope will add query to queryBuilder, Constraint to check if a field has a flag value or not
 *
 * @param  \Illuminate\Database\Eloquent\Builder $query    $query to Constraint
 * @param  string $enumField name of table`s column which is a Flag Enum
 * @param  \BenSampo\Enum\FlaggedEnum $enumValue [description]
 * @return \Illuminate\Database\Eloquent\Builder  Constrainted QueryBuilder
 */
  public function scopeWhereHasFlag($query,$enumField,$enumValue){
    return $this->whereFlag($query,$enumField,$enumValue,true);
  }

  /**
   * This Scope will add query to queryBuilder, Constraint to check if a field hasn`t a flag value or not
   *
   * @param  \Illuminate\Database\Eloquent\Builder $query    $query to Constraint
   * @param  string $enumField name of table`s column which is a Flag Enum
   * @param  \BenSampo\Enum\FlaggedEnum $enumValue [description]
   * @return \Illuminate\Database\Eloquent\Builder  Constrainted QueryBuilder
   */
  public function scopeWhereHasNotFlag($query,$enumField,$enumValue){
    return $this->whereFlag($query,$enumField,$enumValue,false);
  }

   /**
    * This function will add constraint to queryBuilder
    *
    * constraint is for a flaggedEnum column to check if it has a column or not
    *
    * @param  \Illuminate\Database\Eloquent\Builder $query    $query to Constraint
    * @param  string $enumField name of table`s column which is a Flag Enum
    * @param  \BenSampo\Enum\FlaggedEnum $enumValue [description]
    * @param  bool $exists    if the column has or hasnot The Flag
    * @return \Illuminate\Database\Eloquent\Builder  Constrainted QueryBuilder
    */
  private function whereFlag($query,$enumField,$enumValue,$exists){
    return $query->whereRaw(
                              ($exists?"":"not ( ").
                              $enumField." & ".$enumValue.
                              ($exists?"":")")
                            );
  }
}
