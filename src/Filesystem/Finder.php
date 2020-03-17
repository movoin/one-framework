<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Filesystem
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Filesystem;

use Closure;
use Countable;
use Iterator;
use AppendIterator;
use IteratorAggregate;
use CallbackFilterIterator;
use RecursiveIteratorIterator;
use One\Filesystem\Exception\DirectoryException;
use One\Filesystem\Exception\FilesystemException;
use One\Filesystem\Iterator\FileInfo;
use One\Filesystem\Iterator\RecursiveDirectoryIterator;
use One\Filesystem\Iterator\ExcludeDirectoryFilterIterator;
use One\Utility\Helper\RegexHelper;

/**
 * 文件查找类
 *
 * **示例**
 *
 * ```
 * $finder = new Finder;
 * $finder->files()
 *      ->name('*.php')
 *      ->filter(function ($file) {
 *          return true;
 *      })
 *      ->exclude(__DIR__ . '/path/to/excluded')
 *      ->in(__DIR__)
 *      ->in(__DIR__ . '/../other/path)
 * ;
 *
 * foreach ($finder as $file) {
 *      echo $file->getFilePath();
 * }
 * ```
 *
 * @since 0.2
 */
class Finder implements IteratorAggregate, Countable
{
    /**
     * 匹配模式定义
     *
     * - 仅文件: ONLY_FILES
     * - 仅目录: ONLY_DIRECTORIES
     */
    const ONLY_FILES = 1;
    const ONLY_DIRECTORIES = 2;

    /**
     * 匹配模式
     *
     * @var int
     */
    private $mode;
    /**
     * 匹配名称
     *
     * @var array
     */
    private $names = [];
    /**
     * 排除名称
     *
     * @var array
     */
    private $notNames = [];
    /**
     * 匹配过滤器
     *
     * @var array
     */
    private $filters = [];
    /**
     * 查找目录
     *
     * @var array
     */
    private $dirs = [];
    /**
     * 排除目录
     *
     * @var array
     */
    private $excludes = ['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg', '.composer'];

    /**
     * 仅匹配文件
     *
     * @return self
     */
    public function files(): self
    {
        $this->mode = static::ONLY_FILES;

        return $this;
    }

    /**
     * 仅匹配目录
     *
     * @return self
     */
    public function dirs(): self
    {
        $this->mode = static::ONLY_DIRECTORIES;

        return $this;
    }

    /**
     * 匹配文件名
     *
     * @param string $pattern
     *
     * @return self
     */
    public function name(string $pattern): self
    {
        $pattern = RegexHelper::isRegex($pattern) ? $pattern : RegexHelper::globToRegex($pattern);

        $this->names = array_merge(
            $this->names,
            (array) $pattern
        );

        unset($pattern);

        return $this;
    }

    /**
     * 排除文件名
     *
     * @param string $pattern
     *
     * @return self
     */
    public function notName(string $pattern): self
    {
        $pattern = RegexHelper::isRegex($pattern) ? $pattern : RegexHelper::globToRegex($pattern);

        $this->notNames = array_merge(
            $this->notNames,
            (array) $pattern
        );

        unset($pattern);

        return $this;
    }

    /**
     * 过滤结果
     *
     * @param \Closure $closure
     *
     * @return self
     */
    public function filter(Closure $closure): self
    {
        $this->filters[] = $closure;

        return $this;
    }

    /**
     * 查询目录
     *
     * @param string $dir
     *
     * @return self
     */
    public function in(string $dir): self
    {
        $resolvedDirs = [];

        if (is_dir($dir)) {
            $resolvedDirs[] = $this->normalizeDir($dir);
        } elseif ($glob = glob($dir, (defined('GLOB_BRACE') ? GLOB_BRACE : 0) | GLOB_ONLYDIR | GLOB_NOSORT)) {
            sort($glob);
            $resolvedDirs = array_merge($resolvedDirs, array_map([$this, 'normalizeDir'], $glob));
        } else {
            throw new DirectoryException([
                '目录 "{dir}" 不存在' => [
                    'dir' => $dir
                ]
            ]);
        }

        $this->dirs = array_merge($this->dirs, $resolvedDirs);

        return $this;
    }

    /**
     * 排除目录
     *
     * @param string $dir
     *
     * @return self
     */
    public function exclude(string $dir): self
    {
        $this->excludes = array_merge($this->excludes, (array) $dir);

        return $this;
    }

    /**
     * 获得结果总数
     *
     * @return int
     */
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    /**
     * 迭代结果
     *
     * @return \Iterator
     * @throws \One\Filesystem\Exception\FilesystemException
     */
    public function getIterator(): Iterator
    {
        if (0 === count($this->dirs)) {
            throw new FilesystemException('必须设置查询目录 in()');
        }

        if (1 === count($this->dirs)) {
            return $this->searchInDirectory($this->dirs[0]);
        }

        $iterator = new AppendIterator;
        foreach ($this->dirs as $dir) {
            $iterator->append($this->searchInDirectory($dir));
        }

        return $iterator;
    }

    /**
     * 搜索目录
     *
     * @param string $dir
     *
     * @return \Iterator
     */
    private function searchInDirectory(string $dir): Iterator
    {
        $iterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);

        // 排除目录
        if ($this->excludes) {
            $iterator = new ExcludeDirectoryFilterIterator($iterator, $this->excludes);
        }

        $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

        // 类型
        if ($this->mode) {
            $iterator = new CallbackFilterIterator($iterator, function (FileInfo $current) {
                return $this->mode === self::ONLY_FILES ?
                    $current->isFile() :
                    $current->isDir()
                ;
            });
        }

        // 文件名称
        if ($this->names || $this->notNames) {
            $iterator = new CallbackFilterIterator($iterator, function (FileInfo $current) {
                if ($this->notNames) {
                    foreach ($this->notNames as $regex) {
                        if (preg_match($regex, $current->getFilename())) {
                            return false;
                        }
                    }
                }

                if ($this->names) {
                    foreach ($this->names as $regex) {
                        if (preg_match($regex, $current->getFilename())) {
                            return true;
                        }
                    }
                    return false;
                }

                return true;
            });
        }

        // 自定义过滤
        if ($this->filters) {
        }

        return $iterator;
    }

    /**
     * 标准化目录路径
     *
     * @param string $dir
     *
     * @return string
     * @throws \One\Filesystem\Exception\DirectoryException
     * @throws \One\Filesystem\Exception\FilesystemException
     */
    private function normalizeDir(string $dir): string
    {
        if (! is_dir($dir)) {
            throw new DirectoryException([
                '目录 "{dir}" 不存在' => [
                    'dir' => $dir
                ]
            ]);
        }

        if ('/' === $dir) {
            return $dir;
        }

        $dir = rtrim($dir, '/' . DIRECTORY_SEPARATOR);

        // 去除空格
        while (preg_match('#\p{C}+|^\./#u', $dir)) {
            $dir = preg_replace('#\p{C}+|^\./#u', '', $dir);
        }

        $parts = [];

        foreach (explode('/', $dir) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;

                case '..':
                    if (empty($parts)) {
                        throw new FilesystemException(['路径超出根目录范围 "{dir}"' => ['dir' => $dir]]);
                    }
                    array_pop($parts);
                    break;

                default:
                    $parts[] = $part;
                    break;
            }
        }

        return implode('/', $parts);
    }
}
