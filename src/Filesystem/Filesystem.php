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
use One\Filesystem\ContentListingFormatter;
use One\Filesystem\Contract\AdapterInterface;
use One\Filesystem\Exception\DirectoryNotExistsException;
use One\Filesystem\Exception\FileExistsException;
use One\Filesystem\Exception\FileNotExistsException;
use One\Filesystem\Exception\FilesystemPathOutOfRangeException;
use One\Filesystem\Exception\RewindResourceTypeErrorException;

/**
 * 文件系统类
 *
 * @since 0.2
 */
class Filesystem
{
    /**
     * 文件系统适配器
     *
     * @var \One\Filesystem\Contract\AdapterInterface
     */
    protected $adapter;
    /**
     * 文件适配器名称
     *
     * @var string
     */
    protected $adapterName;
    /**
     * 配置
     *
     * @var array
     */
    protected $config = [];

    /**
     * 构造
     *
     * @param \One\Filesystem\Contract\AdapterInterface $adapter
     * @param array $config
     */
    public function __construct(AdapterInterface $adapter, array $config = [])
    {
        $this->adapter = $adapter;
        $this->adapterName = get_class($adapter);
        $this->config = $config;
    }

    /**
     * 获得文件系统适配器实例
     *
     * @return \One\Filesystem\Contract\AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * 判断路径是否存在
     *
     * @param string $path
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     */
    public function exists(string $path): bool
    {
        $path = $this->normalizePath($path);

        return strlen($path) === 0 ? false : $this->getAdapter()->exists($path);
    }

    /**
     * 读取文件内容
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     * @throws \One\Filesystem\Exception\FileReadFailureException
     */
    public function read(string $path): string
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->read($path);
    }

    /**
     * 读取文件流
     *
     * @param string $path
     *
     * @return resource
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     * @throws \One\Filesystem\Exception\FileReadFailureException
     */
    public function readStream(string $path)
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->readStream($path);
    }

    /**
     * 读取文件内容并删除文件
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     * @throws \One\Filesystem\Exception\FileReadFailureException
     */
    public function readAndDelete(string $path): string
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);
        $contents = $this->read($path);
        $this->delete($path);

        return $contents;
    }

    /**
     * 返回目录内容
     *
     * @param string $directory
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents(string $directory = '', bool $recursive = false): array
    {
        $directory = $this->normalizePath($directory);
        $list = $this->getAdapter()->listContents($directory, $recursive);

        return (new ContentListingFormatter($directory, $recursive))->formatListing($list);
    }

    /**
     * 写入新文件
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileExistsException
     */
    public function write(string $path, string $contents, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $this->assertAbsent($path);
        $config = $this->prepareConfig($config);

        return $this->getAdapter()->write($path, $contents, $config);
    }

    /**
     * 从 Stream 写入新文件
     *
     * @param string $path
     * @param resource $resource
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileExistsException
     * @throws \One\Filesystem\Exception\RewindResourceTypeErrorException
     */
    public function writeStream(string $path, $resource, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $this->assertAbsent($path);
        $config = $this->prepareConfig($config);
        $this->rewindStream($resource);

        return $this->getAdapter()->writeStream($path, $resource, $config);
    }

    /**
     * 更新文件
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function update(string $path, string $contents, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);
        $config = $this->prepareConfig($config);

        return $this->getAdapter()->update($path, $contents, $config);
    }

    /**
     * 从 Stream 更新文件
     *
     * @param string $path
     * @param resource $resource
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     * @throws \One\Filesystem\Exception\RewindResourceTypeErrorException
     */
    public function updateStream(string $path, $resource, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);
        $config = $this->prepareConfig($config);
        $this->rewindStream($resource);

        return $this->getAdapter()->updateStream($path, $resource, $config);
    }

    /**
     * 创建或更新文件
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     */
    public function put(string $path, string $contents, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $config = $this->prepareConfig($config);

        if ($this->exists($path)) {
            return $this->getAdapter()->update($path, $contents, $config);
        }

        return $this->getAdapter()->write($path, $contents, $config);
    }

    /**
     * 从 Stream 创建或更新文件
     *
     * @param string $path
     * @param resource $resource
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\RewindResourceTypeErrorException
     */
    public function putStream(string $path, $resource, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $config = $this->prepareConfig($config);
        $this->rewindStream($resource);

        if ($this->exists($path)) {
            return $this->getAdapter()->updateStream($path, $resource, $config);
        }

        return $this->getAdapter()->writeStream($path, $resource, $config);
    }

    /**
     * 重命名文件
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     * @throws \One\Filesystem\Exception\FileExistsException
     */
    public function rename(string $path, string $newpath): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        $newpath = $this->normalizePath($newpath);
        $this->assertAbsent($newpath);

        return $this->getAdapter()->rename($path, $newpath);
    }

    /**
     * 删除文件
     *
     * @param string $path
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function delete(string $path): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->delete($path);
    }

    /**
     * 创建目录
     *
     * @param string $dirname
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryExistsException
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     */
    public function createDir(string $dirname, array $config = []): bool
    {
        $dirname = $this->normalizePath($dirname);
        $config = $this->prepareConfig($config);

        return $this->getAdapter()->createDir($dirname, $config);
    }

    /**
     * 删除目录
     *
     * @param string $dirname
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryNotExistsException
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     */
    public function deleteDir(string $dirname): bool
    {
        $dirname = $this->normalizePath($dirname);

        if ($dirname === '') {
            throw new DirectoryNotExistsException($dirname);
        }

        return $this->getAdapter()->deleteDir($dirname);
    }

    /**
     * 获得文件类型信息
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function getMimeType(string $path): string
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getMimeType($path);
    }

    /**
     * 获得路径源信息
     *
     * @param string $path
     *
     * @return array
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function getMetaData(string $path): array
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getMetaData($path);
    }

    /**
     * 获得路径可见性
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function getVisibility(string $path): string
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getVisibility($path);
    }

    /**
     * 设置路径可见性
     *
     * @param string $path
     * @param string $visibility
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function setVisibility(string $path, string $visibility): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->setVisibility($path, $visibility);
    }

    /**
     * 断言文件存在
     *
     * @param string $path
     *
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    protected function assertPresent(string $path)
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }
    }

    /**
     * 断言文件不存在
     *
     * @param string $path
     *
     * @throws \One\Filesystem\Exception\FileExistsException
     */
    protected function assertAbsent(string $path)
    {
        if ($this->exists($path)) {
            throw new FileExistsException($path);
        }
    }

    /**
     * 预处理配置信息
     *
     * @param array $config
     *
     * @return array
     */
    protected function prepareConfig(array $config): array
    {
        return array_merge_recursive($this->config, $config);
    }

    /**
     * 标准化路径
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FilesystemPathOutOfRangeException
     */
    protected function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = $this->removePathWhiteSpace($path);

        $parts = [];

        foreach (explode('/', $path) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;

                case '..':
                    if (empty($parts)) {
                        throw new FilesystemPathOutOfRangeException($path);
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

    /**
     * 删除路径空格
     *
     * @param string $path
     *
     * @return string
     */
    protected function removePathWhiteSpace(string $path): string
    {
        while (preg_match('#\p{C}+|^\./#u', $path)) {
            $path = preg_replace('#\p{C}+|^\./#u', '', $path);
        }

        return $path;
    }

    /**
     * 倒回文件指针的位置
     *
     * @param resource $resource
     *
     * @return void
     * @throws \One\Filesystem\Exception\RewindResourceTypeErrorException
     */
    protected function rewindStream($resource): void
    {
        if (! Assert::resource($resource)) {
            throw new RewindResourceTypeErrorException;
        }

        if (ftell($resource) !== 0 && $this->isSeekableStream($resource)) {
            rewind($resource);
        }
    }

    /**
     * 返回是否可以在当前流中定位
     *
     * @param resource $resource
     *
     * @return bool
     */
    protected function isSeekableStream($resource): bool
    {
        $metadata = stream_get_meta_data($resource);
        return $metadata['seekable'];
    }
}
