<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class supports caching information about state of fixing files.
 *
 * Cache is supported only for phar version and version installed via composer.
 *
 * File will be processed by PHP CS Fixer only if any of the following conditions is fulfilled:
 *  - cache is not available,
 *  - fixer version changed,
 *  - fixers list is changed,
 *  - file is new,
 *  - file changed.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileCacheManager
{
    private $cacheFile;
    private $isEnabled;
    private $fixers;
    private $newHashes = array();
    private $oldHashes = array();

    public function __construct($isEnabled, $cacheFile, array $fixers)
    {
        $this->isEnabled = $isEnabled;
        $this->cacheFile = $cacheFile;
        $this->fixers = array_map(function (FixerInterface $f) {
            return $f->getName();
        }, $fixers);
        sort($this->fixers);

        $this->readFromFile();
    }

    public function __destruct()
    {
        $this->saveToFile();
    }

    public function needFixing($file, $fileContent)
    {
        if (!$this->isCacheAvailable()) {
            return true;
        }

        if (!isset($this->oldHashes[$file])) {
            return true;
        }

        if ($this->oldHashes[$file] !== $this->calcHash($fileContent)) {
            return true;
        }

        // file do not change - keep hash in new collection
        $this->newHashes[$file] = $this->oldHashes[$file];

        return false;
    }

    public function setFile($file, $fileContent)
    {
        if (!$this->isCacheAvailable()) {
            return;
        }

        $this->newHashes[$file] = $this->calcHash($fileContent);
    }

    private function calcHash($content)
    {
        return crc32($content);
    }

    private function isCacheAvailable()
    {
        static $result;

        if (null === $result) {
            $result = $this->isEnabled && (ToolInfo::isInstalledAsPhar() || ToolInfo::isInstalledByComposer());
        }

        return $result;
    }

    private function isCacheStale($cacheVersion, $fixers)
    {
        if (!$this->isCacheAvailable()) {
            return true;
        }

        return ToolInfo::getVersion() !== $cacheVersion || $this->fixers !== $fixers;
    }

    private function readFromFile()
    {
        if (!$this->isCacheAvailable()) {
            return;
        }

        if (!file_exists($this->cacheFile)) {
            return;
        }

        $content = file_get_contents($this->cacheFile);
        $data = unserialize($content);

        // Set hashes only if the cache is fresh, otherwise we need to parse all files
        if (!$this->isCacheStale($data['version'], $data['fixers'])) {
            $this->oldHashes = $data['hashes'];
        }
    }

    private function saveToFile()
    {
        if (!$this->isCacheAvailable()) {
            return;
        }

        $data = serialize(
            array(
                'version' => ToolInfo::getVersion(),
                'fixers' => $this->fixers,
                'hashes' => $this->newHashes,
            )
        );

        if (false === @file_put_contents($this->cacheFile, $data, LOCK_EX)) {
            $error = error_get_last();

            if (null !== $error) {
                throw new IOException(sprintf('Failed to write file "%s", "%s".', $this->cacheFile, $error['message']), 0, null, $this->cacheFile);
            }

            throw new IOException(sprintf('Failed to write file "%s".', $this->cacheFile), 0, null, $this->cacheFile);
        }
    }
}
