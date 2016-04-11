<?php

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->setUsingLinter(true)
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'encoding',
        'short_tag',
        'braces',
        'elseif',
        'eof_ending',
        'function_call_space',
        'function_declaration',
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'method_argument_space',
        'multiple_use',
        'parenthesis',
        'php_closing_tag',
        'single_line_after_imports',
        'trailing_spaces',
        'visibility',
    ])
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/src2')
    )
;
