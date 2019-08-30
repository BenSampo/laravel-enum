<?php

namespace BenSampo\Enum;

use Doctrine\DBAL\Types\Type;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use BenSampo\Enum\Commands\MakeEnumCommand;
use BenSampo\Enum\Commands\EnumAnnotateCommand;
use BenSampo\Enum\Commands\ModelAnnotateCommand;

class EnumServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootCommands();
        $this->bootValidators();
        $this->bootDoctrineType();
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
                EnumAnnotateCommand::class,
                ModelAnnotateCommand::class,
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
        Validator::extend('enum_key', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;

            return (new EnumKey($enum))->passes($attribute, $value);
        });

        Validator::extend('enum_value', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;

            $strict = $parameters[1] ?? null;

            if (! $strict) {
                return (new EnumValue($enum))->passes($attribute, $value);
            }

            $strict = !! json_decode(strtolower($strict));

            return (new EnumValue($enum, $strict))->passes($attribute, $value);
        });
    }

    /**
     * Boot the Doctrine type.
     *
     * @return void
     */
    private function bootDoctrineType()
    {
        // Not included by default in Laravel
        if (class_exists('Doctrine\DBAL\Types\Type')) {
            if (! Type::hasType(EnumType::ENUM)) {
                Type::addType(EnumType::ENUM, EnumType::class);
            }
        }
    }
}
