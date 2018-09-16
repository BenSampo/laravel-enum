<?php

namespace BenSampo\Enum;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use BenSampo\Enum\Commands\MakeEnumCommand;
use BenSampo\Enum\Validations\EnumKey;
use BenSampo\Enum\Validations\EnumValue;

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
    }

    /**
     * Boot the custom validators
     *
     * @return void
     */
    private function bootValidators()
    {
        Validator::extend('enum_value', EnumValue::class . '@validate', EnumValue::$errorMessage);
        Validator::extend('enum_key', EnumKey::class . '@validate', EnumKey::$errorMessage);
    }

    /**
     * Register any package services.
     */
    public function register()
    {
    }
}
