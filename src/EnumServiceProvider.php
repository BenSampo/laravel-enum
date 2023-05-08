<?php declare(strict_types=1);

namespace BenSampo\Enum;

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\ServiceProvider;
use BenSampo\Enum\Commands\MakeEnumCommand;
use BenSampo\Enum\Commands\EnumAnnotateCommand;

class EnumServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->bootCommands();
        $this->bootValidationTranslation();
        $this->bootDoctrineType();
    }

    protected function bootCommands(): void
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

    protected function bootValidationTranslation(): void
    {
        $this->publishes([
            __DIR__ . '/../lang' => lang_path('vendor/laravelEnum'),
        ], 'translations');

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'laravelEnum');
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
}
