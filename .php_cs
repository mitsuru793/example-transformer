<?php
declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude(['bin'])
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'full_opening_tag' => false,
        'no_short_echo_tag' => false,
        'single_quote' => true,
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'function_typehint_space' => true,
        'binary_operator_spaces' => true,
        'no_spaces_around_offset' => true,
        'no_useless_else' => true,
        'hash_to_slash_comment' => true,
        'braces' => false,
        'method_argument_space' => false,
    ])
    ->setFinder($finder)
    ->setUsingCache(true);