<?php

namespace BenSampo\Enum;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use BenSampo\Enum\Commands\MakeEnumCommand;

class EnumServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeEnumCommand::class,
            ]);
        }

        Validator::extend('enum_value', function ($attribute, $value, $parameters, $validator) {
            $enum = array_get($parameters, 0, null);
            $validValues = app($enum)::getValues();

            $strict = array_get($parameters, 1, true);
            $strict = (boolean)json_decode(strtolower($strict));
            if (!$strict) {
                return in_array((string)$value, array_map('strval', $validValues), true);
            }

            return in_array($value, $validValues, true);
        });
    }

    /**
     * Register any package services.
     */
    public function register()
    {
    }
}
