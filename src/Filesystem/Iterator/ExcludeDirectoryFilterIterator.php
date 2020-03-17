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

use Iterator;
use FilterIterator;
use RecursiveIterator;

/**
 * 排除目录过滤迭代器
 *
 * @since 0.2
 */
class ExcludeDirectoryFilterIterator extends FilterIterator implements RecursiveIterator
{
    /**
     * 迭代器
     *
     * @var \Iterator
     */
    private $iterator;
    /**
     * 排除目录
     *
     * @var array
     */
    private $directories = [];

    /**
     * 构造
     *
     * @param \Iterator $iterator
     * @param array $directories
     */
    public function __construct(Iterator $iterator, array $directories)
    {
        $this->iterator = $iterator;

        foreach ($directories as $directory) {
            $this->directories[rtrim($directory, '/')] = true;
        }

        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        if ($this->isDir() && isset($this->directories[$this->getFilename()])) {
            return false;
        }

        return true;
    }

    public function hasChildren(): bool
    {
        return $this->iterator->hasChildren();
    }

    public function getChildren(): Iterator
    {
        $children = new self($this->iterator->getChildren(), []);
        $children->directories = $this->directories;

        return $children;
    }
}
