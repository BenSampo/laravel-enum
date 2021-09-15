<?php

namespace BenSampo\Enum;

use Doctrine\DBAL\Types\Type;
use BenSampo\Enum\Rules\Enum;
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
        $this->bootValidationTranslation();
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
        $this->publishes([
            __DIR__.'/Commands/stubs' => $this->app->basePath('stubs')
        ], 'stubs');

        if ($this->app->runningInConsole()) {
            $this->commands([
                EnumAnnotateCommand::class,
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
        $this->app['validator']->extend('enum_key', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;

            return (new EnumKey($enum))->passes($attribute, $value);
        }, __('laravelEnum::messages.enum_key'));

        $this->app['validator']->extend('enum_value', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;

            $strict = $parameters[1] ?? null;

            if (! $strict) {
                return (new EnumValue($enum))->passes($attribute, $value);
            }

            $strict = !! json_decode(strtolower($strict));

            return (new EnumValue($enum, $strict))->passes($attribute, $value);
        }, __('laravelEnum::messages.enum_value'));

        $this->app['validator']->extend('enum', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;

            return (new Enum($enum))->passes($attribute, $value);
        }, __('laravelEnum::messages.enum'));
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

    private function bootValidationTranslation()
    {
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravelEnum'),
        ], 'translations');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'laravelEnum');
    }
}
