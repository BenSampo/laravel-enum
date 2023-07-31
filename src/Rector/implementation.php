<?php declare(strict_types=1);

use BenSampo\Enum\Commands\EnumToNativeCommand;
use BenSampo\Enum\Rector\ToNativeImplementationRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(env(EnumToNativeCommand::BASE_RECTOR_CONFIG_PATH_ENV));
    $rectorConfig->ruleWithConfiguration(ToNativeImplementationRector::class, [
        env(EnumToNativeCommand::TO_NATIVE_CLASS_ENV),
    ]);
};
