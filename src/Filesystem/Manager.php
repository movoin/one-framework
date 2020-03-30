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

use One\Utility\Assert;
use One\Filesystem\Filesystem;
use One\Filesystem\Exception\FilesystemBadMethodCallException;
use One\Filesystem\Exception\FilesystemMethodArgumentsUndefinedException;
use One\Filesystem\Exception\FilesystemPrefixTypeErrorException;
use One\Filesystem\Exception\FilesystemPathTypeErrorException;
use One\Filesystem\Exception\FilesystemPrefixUndefinedException;

/**
 * 文件系统管理类
 *
 * 文件系统被抽象为三层：管理器、文件系统、适配器。
 *
 * - 管理器：负责全局操作，文件系统中的所有方法均可以直接通过管理器访问，并提供文件系统中不支持的高级方法，如：跨文件系统的复制与移动目标。
 * - 文件系统：所有适配器的高级封装，在配置上的优先级高于适配器，并提供更便捷的路径访问方式。
 * - 适配器：底层文件系统的具体实现，如：本地文件系统、(s)FTP、HDFS、FastDFS、阿里云等，与文件系统系一对一关系。
 *
 * **说明**
 *
 * ```
 * 不同的文件系统间，可以使用相同的适配器，可根据业务灵活配置，最终目的是为了更便捷的操作文件目录。
 * ```
 *
 * **如 「上传文件」场景：**
 *
 * ```
 * 我们需要用到至少两个文件系统，「tmp」和「uploads」两都都可以是 Local，也可以一个是 Local，另一个是 HDFS 或其它适配器。
 * 先将文件上传至「tmp」再将文件从「tmp」移动到「uploads」，最后删除「tmp」中的文件。
 * ```
 *
 * **示例**
 *
 * ```
 * $localAdapter = new Local(
 *     __DIR__ . '/path/to/local',
 *     ['visibility' => 'public]
 * );
 *
 * $localFS = new Filesystem($localAdapter);
 *
 * $fileManager = new Manager([
 *     'local' => $localFS,
 *     'hdfs' => $hdfs
 * ]);
 * ```
 *
 * **访问文件系统**
 *
 * ```
 * // 读取本地文件
 * $fileManager->read('local://path/to/file');
 * // 跨文件系统拷贝文件
 * $fileManager->copy('local://path/to/file', 'hdfs://path/to/file');
 * // 删除文件
 * $fileManager->delete('local://path/../file');
 * ```
 *
 * @since 0.2
 */
class Manager
{
    /**
     * 已挂载的文件系统
     *
     * @var array
     */
    protected $fs = [];

    /**
     * 构造
     *
     * @param array $filesystems
     *
     * @throws \One\Filesystem\Exception\AdapterPrefixNotExistsException
     * @throws \One\Filesystem\Exception\AdapterPrefixTypeErrorException
     */
    public function __construct(array $filesystems = [])
    {
        $this->mountFilesystems($filesystems);
    }

    /**
     * 获得指定文件系统实例
     *
     * @param  string $prefix
     *
     * @return \One\FileSystem\FileSystem
     * @throws \One\Filesystem\Exception\FilesystemPrefixUndefinedException
     */
    public function getFileSystem(string $prefix): FileSystem
    {
        if (! isset($this->fs[$prefix])) {
            throw new FilesystemPrefixUndefinedException($prefix);
        }

        return $this->fs[$prefix];
    }

    /**
     * 挂载多个文件系统
     *
     * @param array $filesystems
     *
     * @return self
     * @throws \One\Filesystem\Exception\FilesystemPrefixTypeErrorException
     */
    public function mountFilesystems(array $filesystems = []): self
    {
        foreach ($filesystems as $prefix => $filesystem) {
            $this->mountFilesystem($prefix, $filesystem);
        }

        return $this;
    }

    /**
     * 挂载文件系统
     *
     * @param string $prefix
     * @param \One\Filesystem\Filesystem $filesystem
     *
     * @return self
     * @throws \One\Filesystem\Exception\FilesystemPrefixTypeErrorException
     */
    public function mountFilesystem(string $prefix, Filesystem $filesystem): self
    {
        if (! Assert::stringNotEmpty($prefix)) {
            throw new FilesystemPrefixTypeErrorException;
        }

        $this->fs[$prefix] = $filesystem;

        return $this;
    }

    /**
     * 返回目录内容
     *
     * !!! 请慎重使用，需要考虑到目录中的 inode 一旦过多，将会发生跑满磁盘 IO 的情况。
     * !!! 所以一定要注意目录管理，按年、月分级管理文件，将在一定程度上避免出现上述情况。
     *
     * @param  string $directory
     * @param  bool   $recursive
     *
     * @return array
     * @throws \One\Filesystem\Exception\FilesystemPrefixUndefinedException
     * @throws \One\Filesystem\Exception\FilesystemPrefixTypeErrorException
     */
    public function listContents(string $directory = '', bool $recursive = false): array
    {
        list($prefix, $directory) = $this->getPrefixAndPath($directory);

        $fs = $this->getFileSystem($prefix);
        $list = $fs->listContents($directory, $recursive);

        foreach ($list as &$file) {
            $file['filesystem'] = $prefix;
        }

        unset($prefix, $directory, $fs);

        return $list;
    }

    /**
     * 复制文件
     *
     * @param  string $from
     * @param  string $to
     * @param  array  $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPrefixUndefinedException
     * @throws \One\Filesystem\Exception\FilesystemPrefixTypeErrorException
     */
    public function copy(string $from, string $to, array $config = []): bool
    {
        list($prefixFrom, $from) = $this->getPrefixAndPath($from);

        $buffer = $this->getFileSystem($prefixFrom)->readStream($from);

        unset($prefixFrom, $from);

        list($prefixTo, $to) = $this->getPrefixAndPath($to);

        if ($this->getFileSystem($prefixTo)->writeStream($to, $buffer, $config)) {
            unset($prefixTo, $to, $buffer);
            return true;
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * 移动文件
     *
     * @param  string $from
     * @param  string $to
     * @param  array  $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPrefixTypeErrorException
     * @throws \One\Filesystem\Exception\FilesystemPrefixUndefinedException
     */
    public function move(string $from, string $to, array $config = []): bool
    {
        list($prefixFrom, $pathFrom) = $this->getPrefixAndPath($from);
        list($prefixTo, $pathTo) = $this->getPrefixAndPath($to);

        if ($prefixFrom === $prefixTo) {
            $fs = $this->getFileSystem($prefixFrom);
            $renamed = $fs->rename($pathFrom, $pathTo);

            unset($prefixFrom, $pathFrom, $prefixTo);

            if ($renamed && isset($config['visibility'])) {
                return $fs->setVisibility($pathTo, $config['visibility']);
            }

            unset($fs, $pathTo);

            return $renamed;
        }

        if ($this->copy($from, $to, $config)) {
            return $this->delete($from);
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * 调用适配器中的方法
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     * @throws \One\Filesystem\Exception\FilesystemBadMethodCallException
     * @throws \One\Filesystem\Exception\FilesystemMethodArgumentsUndefinedException
     * @throws \One\Filesystem\Exception\FilesystemPathTypeErrorException
     * @throws \One\Filesystem\Exception\FilesystemPrefixUndefinedException
     */
    public function __call(string $method, array $arguments)
    {
        list($prefix, $arguments) = $this->filterPrefix($method, $arguments);

        $fs = $this->getFileSystem($prefix);

        if (method_exists($fs, $method)) {
            return call_user_func_array([$fs, $method], $arguments);
        }

        unset($arguments, $fs);

        throw new FilesystemBadMethodCallException($prefix, $method);
    }

    /**
     * 从参数中获得文件系统前缀
     *
     * @param string $method
     * @param array $arguments
     *
     * @return array
     * @throws \One\Filesystem\Exception\FilesystemMethodArgumentsUndefinedException
     * @throws \One\Filesystem\Exception\FilesystemPathTypeErrorException
     */
    protected function filterPrefix(string $method, array $arguments): array
    {
        if (empty($arguments)) {
            throw new FilesystemMethodArgumentsUndefinedException($method);
        }

        $path = array_shift($arguments);

        if (! Assert::stringNotEmpty($path)) {
            throw new FilesystemPathTypeErrorException;
        }

        list($prefix, $path) = $this->getPrefixAndPath($path);
        array_unshift($arguments, $path);
        unset($path);

        return [$prefix, $arguments];
    }

    /**
     * 获得路径中的前缀和路径
     *
     * @param  string $path
     *
     * @return array
     * @throws \One\Filesystem\Exception\FilesystemPrefixTypeErrorException
     */
    protected function getPrefixAndPath(string $path): array
    {
        if (! Assert::contains($path, '://')) {
            throw new FilesystemPrefixTypeErrorException;
        }

        return explode('://', $path, 2);
    }
}
