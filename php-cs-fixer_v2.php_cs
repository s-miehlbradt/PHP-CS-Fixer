<?php

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setUsingLinter(true)
    ->setCacheFile('.php_cs.cache.v2')
    ->setRules(array(
        'encoding' => true,
        'full_opening_tag' => true,
        'braces' => true,
        'elseif' => true,
        'single_blank_line_at_eof' => true,
        'no_spaces_after_function_name' => true,
        'function_declaration' => true,
        'no_tab_indentation' => true,
        'blank_line_after_namespace' => true,
        'unix_line_endings' => true,
        'lowercase_constants' => true,
        'lowercase_keywords' => true,
        'method_argument_space' => true,
        'single_import_per_statement' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_closing_tag' => true,
        'single_line_after_imports' => true,
        'no_trailing_whitespace' => true,
        'visibility_required' => true,

    ))
    ->finder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/src2')
    )
;
