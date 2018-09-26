<?php

namespace BenSampo\Enum;

use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use BenSampo\Enum\Commands\MakeEnumCommand;

class EnumServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->bootCommands();
        $this->bootValidators();
    }

    /**
     * Boot the custom commands
     *
     * @return void
     */
    private function bootCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeEnumCommand::class,
            ]);
        }

        $this->loadTranslationsFrom(
            __DIR__ . '/lang',
            'laravel-enum'
        );
    }

    /**
     * Boot the custom validators
     *
     * @return void
     */
    private function bootValidators()
    {
        Validator::extend('enum_key', function($attribute, $value, $parameters, $validator) {
            $enum = array_get($parameters, 0, null);
            return (new EnumKey($enum))->passes($attribute, $value);
        });

        Validator::extend('enum_value', function($attribute, $value, $parameters, $validator) {
            $enum = array_get($parameters, 0, null);
            $strict = array_get($parameters, 1, null);

            if ($strict) {
                $strict = (boolean) json_decode(strtolower($strict));
                return (new EnumValue($enum, $strict))->passes($attribute, $value);
            }

            return (new EnumValue($enum))->passes($attribute, $value);
        });
    }

    /**
     * Register any package services.
     */
    public function register()
    {
    }
}
