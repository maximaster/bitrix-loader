<?php

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/var/cache/.php-cs-fixer.cache')
    ->setFinder((new PhpCsFixer\Finder())->in(['src']))
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'single_blank_line_at_eof' => true,
        'no_superfluous_phpdoc_tags' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
		'concat_space' => ['spacing' => 'one'],
        'single_line_throw' => false,
        'yoda_style' => false,
        'phpdoc_align' => false,
        'global_namespace_import' => true,
    ]);
