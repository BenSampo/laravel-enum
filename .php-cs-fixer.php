<?php declare(strict_types=1);

use function MLL\PhpCsFixerConfig\risky;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->notPath('vendor')
    ->notPath('tests/Enums/Annotate') // Generated
    ->notPath('tests/Enums/AnnotateFixtures') // Matches laminas/laminas-code
    ->notPath('tests/Enums/ToNative') // Generated
    ->notPath('tests/Enums/ToNativeFixtures') // Matches laminas/laminas-code
    ->ignoreDotFiles(false)
    ->ignoreVCS(true);

return risky($finder);
