<?php declare(strict_types=1);

use BenSampo\Enum\Commands\EnumToNativeCommand;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rector\ToNativeUsagesRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ToNativeUsagesRector::class, [
        env(EnumToNativeCommand::CLASS_ENV, Enum::class),
    ]);
};
