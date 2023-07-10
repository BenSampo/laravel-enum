<?php declare(strict_types=1);

namespace BenSampo\Enum;

use BenSampo\Enum\Commands\EnumAnnotateCommand;
use BenSampo\Enum\Commands\EnumToNativeCommand;
use BenSampo\Enum\Commands\MakeEnumCommand;
use BenSampo\Enum\Rules\Enum;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use Doctrine\DBAL\Types\Type;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\ServiceProvider;

class EnumServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootCommands();
        $this->bootValidationTranslation();
        $this->bootValidators();
        $this->bootDoctrineType();
    }

    protected function bootCommands(): void
    {
        $this->publishes([
            __DIR__ . '/Commands/stubs' => $this->app->basePath('stubs'),
        ], 'stubs');

        if ($this->app->runningInConsole()) {
            $this->commands([
                EnumAnnotateCommand::class,
                EnumToNativeCommand::class,
                MakeEnumCommand::class,
            ]);
        }
    }

    protected function bootValidators(): void
    {
        $this->app->extend(ValidationFactory::class, function (ValidationFactory $validationFactory): ValidationFactory {
            $validationFactory->extend('enum_key', function (string $attribute, $value, array $parameters, $validator): bool {
                $enum = $parameters[0] ?? null;

                return (new EnumKey($enum))->passes($attribute, $value);
            }, __('laravelEnum::messages.enum_key'));

            $validationFactory->extend('enum_value', function (string $attribute, $value, array $parameters, $validator): bool {
                $enum = $parameters[0] ?? null;
                $strict = $parameters[1] ?? null;

                if (! $strict) {
                    return (new EnumValue($enum))->passes($attribute, $value);
                }
                $strict = (bool) json_decode(strtolower($strict));

                return (new EnumValue($enum, $strict))->passes($attribute, $value);
            }, __('laravelEnum::messages.enum_value'));

            $validationFactory->extend('enum', function (string $attribute, $value, array $parameters, $validator): bool {
                $enum = $parameters[0] ?? null;

                return (new Enum($enum))->passes($attribute, $value);
            }, __('laravelEnum::messages.enum'));

            return $validationFactory;
        });
    }

    protected function bootDoctrineType(): void
    {
        // Not included by default in Laravel
        if (class_exists('Doctrine\DBAL\Types\Type')) {
            if (! Type::hasType(EnumType::ENUM)) {
                Type::addType(EnumType::ENUM, EnumType::class);
            }
        }
    }

    protected function bootValidationTranslation(): void
    {
        $this->publishes([
            __DIR__ . '/../lang' => lang_path('vendor/laravelEnum'),
        ], 'translations');

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'laravelEnum');
    }
}
