<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->notPath('vendor')
    ->notPath('tests/Enums/Annotate') // Generated
    ->notPath('tests/Enums/AnnotateFixtures') // Matches laminas/laminas-code
    ->ignoreDotFiles(false)
    ->ignoreVCS(true);

return MLL\PhpCsFixerConfig\risky($finder);
