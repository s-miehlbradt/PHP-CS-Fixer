<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Linter;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * Handle PHP code linting.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TokenizerLinter implements LinterInterface
{
    /**
     * {@inheritdoc}
     */
    public function lintFile($path)
    {
        return new LintingResult(file_get_contents($path));
    }

    /**
     * {@inheritdoc}
     */
    public function lintSource($source)
    {
        try {
            // it will throw ParseError on syntax error
            // if not, it will cache the tokenized version of code, which is great for Runner
            Tokens::fromCode($source);

            return new TokenizerLintingResult();
        } catch (\ParseError $e) {
            return new TokenizerLintingResult($e);
        }
    }
}
