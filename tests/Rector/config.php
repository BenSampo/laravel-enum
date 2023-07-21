<?php declare(strict_types=1);

use BenSampo\Enum\Rector\ToNativeRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ToNativeRector::class);
};