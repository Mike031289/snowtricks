<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/assets')
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'          => true,
        '@PSR12'            => true,

        // Tableaux
        'array_syntax'      => ['syntax' => 'short'],

        // Imports
        'no_unused_imports' => true,
        'ordered_imports'   => true,

        // Structures
        'no_useless_else'   => true,
        'no_useless_return' => true,
        'no_empty_statement' => true,

        // PHPDoc
        'phpdoc_align'      => true,
        'phpdoc_trim'       => true,
        'phpdoc_scalar'     => true,
        'phpdoc_separation' => true,
        'phpdoc_summary'    => false,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed'      => true,
            'remove_inheritdoc' => false,
        ],

        // Classes
        'ordered_class_elements' => true,
        'method_argument_space'  => ['on_multiline' => 'ensure_fully_multiline'],
    ])
    ->setFinder($finder)
    ->setUsingCache(false)
;
