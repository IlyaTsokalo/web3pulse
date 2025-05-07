<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'no_empty_phpdoc' => true,
        // Disable removal of docblocks that might be considered "superfluous" when type hints exist
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'void_return' => true,
        'fully_qualified_strict_types' => false,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => false, 'import_functions' => false],
        'phpdoc_line_span' => ['const' => 'multi', 'method' => 'multi', 'property' => 'multi'],
        'php_unit_strict' => true,
        'visibility_required' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
