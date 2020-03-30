<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Filesystem\Iterator
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Filesystem\Iterator;

use RecursiveIterator;
use RecursiveArrayIterator;
use UnexpectedValueException;
use One\Filesystem\Iterator\FileInfo;

/**
 * 递归目录迭代类
 *
 * @since 0.2
 */
class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    /**
     * @var string
     */
    private $rootPath;
    /**
     * @var string
     */
    private $subPath;
    /**
     * @var string
     */
    private $directorySeparator = '/';
    /**
     * @var bool
     */
    private $rewindable;

    /**
     * 构造
     *
     * @param string $path
     * @param int $flags
     */
    public function __construct(string $path, int $flags)
    {
        parent::__construct($path, $flags);
        $this->rootPath = $path;
    }

    /**
     * 返回当前文件 SplFileInfo 实例
     *
     * @return \One\Filesystem\FileInfo
     */
    public function current(): FileInfo
    {
        if (null === $subPathname = $this->subPath) {
            $subPathname = $this->subPath = (string) $this->getSubPath();
        }

        if ('' !== $subPathname) {
            $subPathname .= $this->directorySeparator;
        }

        $subPathname .= $this->getFilename();

        if ('/' !== $basePath = $this->rootPath) {
            $basePath .= $this->directorySeparator;
        }

        return new FileInfo($basePath.$subPathname, $this->subPath, $subPathname);
    }

    /**
     * @return \RecursiveIterator
     */
    public function getChildren(): RecursiveIterator
    {
        try {
            $children = parent::getChildren();

            if ($children instanceof self) {
                $children->rewindable = &$this->rewindable;
                $children->rootPath = $this->rootPath;
            }

            return $children;
        // @codeCoverageIgnoreStart
        } catch (UnexpectedValueException $e) {
            return new RecursiveArrayIterator([]);
        }
        // @codeCoverageIgnoreEnd
    }

    public function rewind(): void
    {
        if (false === $this->isRewindable()) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        parent::rewind();
    }

    public function isRewindable(): bool
    {
        if (null !== $this->rewindable) {
            return $this->rewindable;
        }

        if (false !== $stream = @opendir($this->getPath())) {
            $infos = stream_get_meta_data($stream);
            closedir($stream);

            if ($infos['seekable']) {
                return $this->rewindable = true;
            }
        }

        // @codeCoverageIgnoreStart
        return $this->rewindable = false;
        // @codeCoverageIgnoreEnd
    }
}
