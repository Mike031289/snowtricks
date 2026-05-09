<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/assets')
    ->in(__DIR__ . '/public') // Added to include your index.php
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'           => true,
        '@PSR12'            => true,

        // Arrays: force short syntax [] instead of array()
        'array_syntax'      => ['syntax' => 'short'],

        // Imports: clean up unused uses and sort them alphabetically
        'no_unused_imports' => true,
        'ordered_imports'   => true,

        // Control Structures: remove redundant code
        'no_useless_else'   => true,
        'no_useless_return' => true,
        'no_empty_statement' => true,

        // PHPDoc: standardize documentation blocks
        'phpdoc_align'      => true,
        'phpdoc_trim'       => true,
        'phpdoc_scalar'     => true,
        'phpdoc_separation' => true,
        'phpdoc_summary'    => false, // We don't force a summary line
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed'       => true,
            'remove_inheritdoc' => false,
        ],

        // Classes: organization and formatting
        'ordered_class_elements' => true,
        'method_argument_space'  => ['on_multiline' => 'ensure_fully_multiline'],

        // Quality: remove trailing whitespaces
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(false)
;
