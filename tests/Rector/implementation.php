<?php declare(strict_types=1);

use BenSampo\Enum\Rector\ToNativeImplementationRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ToNativeImplementationRector::class);
};
