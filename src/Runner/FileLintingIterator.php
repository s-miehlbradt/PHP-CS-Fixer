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

namespace PhpCsFixer\Runner;

use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingResultInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileLintingIterator extends \IteratorIterator
{
    /**
     * @var LintingResultInterface
     */
    private $currentResult;

    /**
     * @var LinterInterface
     */
    private $linter;

    public function __construct(\Iterator $iterator, LinterInterface $linter)
    {
        parent::__construct($iterator);

        $this->linter = $linter;
    }

    public function currentLintingResult()
    {
        return $this->currentResult;
    }

    public function next()
    {
        parent::next();

        $this->currentResult = $this->handleItem($this->current());
    }

    public function rewind()
    {
        parent::rewind();

        if ($this->valid()) {
            $this->currentResult = $this->handleItem($this->current());
        }
    }

    private function handleItem(\SplFileInfo $file)
    {
        return $this->linter->lintFile($file->getRealPath());
    }
}
