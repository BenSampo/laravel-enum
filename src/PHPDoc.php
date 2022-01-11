<?php

namespace BenSampo\Enum;

class PHPDoc
{
    public static function unwrapDocblock(string $docBlock): string
    {
        $docBlock = substr($docBlock, 3); // strip leading /**
        $docBlock = substr($docBlock, 0, -2); // strip trailing */

        $lines = explode("\n", $docBlock);
        $lines = array_map(function (string $line): string {
            $line = trim($line);

            $line = preg_replace('/^\* /', '$1$2', $line); // strip *

            return trim($line);
        }, $lines);

        return trim(implode("\n", $lines));
    }
}
