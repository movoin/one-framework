<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Filesystem\Adapter
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Filesystem\Adapter;

use Finfo;
use SplFileInfo;
use DirectoryIterator;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use One\Filesystem\Adapter;
use One\Filesystem\Exception\DirectoryCreateFailureException;
use One\Filesystem\Exception\DirectoryExistsException;
use One\Filesystem\Exception\DirectoryNotExistsException;
use One\Filesystem\Exception\DirectoryUnreadableException;
use One\Filesystem\Exception\FileExistsException;
use One\Filesystem\Exception\FileNotExistsException;
use One\Filesystem\Exception\FileReadFailureException;
use One\Utility\MimeTypeExtension;
use One\Utility\Helper\ArrayHelper;

/**
 * 本地文件系统
 *
 * **配置**
 *
 * - visibility 目录文件权限
 *   - public 公开 (默认)
 *   - private 私有
 *
 * 示例：
 *
 * ```
 * $config = [
 *     'visibility' => 'public'
 * ];
 * ```
 *
 * @since 0.2
 */
class Local extends Adapter
{
    /**
     * 目录文件权限
     *
     * @var array
     */
    protected $permissions = [
        'file' => [
            'public' => 0664,
            'private' => 0600,
        ],
        'dir' => [
            'public' => 0755,
            'private' => 0700,
        ],
    ];
    /**
     * 文件写入标识
     *
     * @var int
     */
    protected $writeFlags;

    /**
     * 构造
     *
     * @param string $basePath
     * @param int $writeFlags
     * @param array $permissions
     *
     * @throws \One\Filesystem\Exception\DirectoryCreateFailureException
     * @throws \One\Filesystem\Exception\DirectoryUnreadableException
     */
    public function __construct(
        string $basePath,
        int $writeFlags = LOCK_EX,
        array $permissions = []
    ) {
        $this->permissions = array_replace_recursive($this->permissions, $permissions);
        $this->writeFlags = $writeFlags;

        $basePath = is_link($basePath) ? realpath($basePath) : $basePath;

        $this->ensureDirectory($basePath);
        $this->setBasePath($basePath);

        unset($basePath);
    }

    /**
     * 判断文件是否存在
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        return file_exists($this->applyBasePath($path));
    }

    /**
     * 读取文件内容
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FileNotExistsException
     * @throws \One\Filesystem\Exception\FileReadFailureException
     */
    public function read(string $path): string
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        $location = $this->applyBasePath($path);

        if (($content = file_get_contents($location)) === false) {
            // @codeCoverageIgnoreStart
            throw new FileReadFailureException($path);
            // @codeCoverageIgnoreEnd
        }

        unset($location);

        return $content;
    }

    /**
     * 从文件中读取数据流
     *
     * @param string $path
     *
     * @return resource
     * @throws \One\Filesystem\Exception\FileNotExistsException
     * @throws \One\Filesystem\Exception\FileReadFailureException
     */
    public function readStream(string $path)
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        $location = $this->applyBasePath($path);

        if (($stream = @fopen($location, 'rb')) === false) {
            // @codeCoverageIgnoreStart
            throw new FileReadFailureException($path);
            // @codeCoverageIgnoreEnd
        }

        unset($location);

        return $stream;
    }

    /**
     * 返回目录中的内容
     *
     * **返回结果**
     * ```
     * [
     *     [
     *         'type' => 'file', // dir|file
     *         'path' => 'filepath', // 不包含 BasePath
     *         'timestamp' => 1583734838,
     *         'size' => 0, // 如果 type !== file，不会返回此字段
     *     ],
     *     ...
     * ]
     * ```
     *
     * @param string $directory
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents(string $directory = '', bool $recursive = false): array
    {
        $list = [];
        $location = $this->applyBasePath($directory);

        if (! is_dir($location)) {
            return $list;
        }

        if ($recursive) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($location, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
        } else {
            $iterator = new DirectoryIterator($location);
        }

        foreach ($iterator as $file) {
            $path = $this->getFilePath($file);
            if (preg_match('#(^|/|\\\\)\.{1,2}$#', $path)) {
                continue;
            }
            $list[] = $this->normalizeFileInfo($file);
            unset($path);
        }

        unset($location, $iterator);

        return array_filter($list);
    }

    /**
     * 写入文件内容
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryCreateFailureException
     * @throws \One\Filesystem\Exception\DirectoryUnreadableException
     * @throws \One\Filesystem\Exception\FileExistsException
     */
    public function write(string $path, string $contents, array $config = []): bool
    {
        if ($this->exists($path)) {
            throw new FileExistsException($path);
        }

        $location = $this->applyBasePath($path);
        $this->ensureDirectory(dirname($location));

        if (file_put_contents($location, $contents, $this->writeFlags) === false) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        if (($visibility = ArrayHelper::get($config, 'visibility')) !== null) {
            $this->setVisibility($path, $visibility);
        }

        unset($location, $visibility);

        return true;
    }

    /**
     * 以数据流的方式写入文件内容
     *
     * @param string $path
     * @param resource $resource
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryCreateFailureException
     * @throws \One\Filesystem\Exception\DirectoryUnreadableException
     * @throws \One\Filesystem\Exception\FileExistsException
     */
    public function writeStream(string $path, $resource, array $config = []): bool
    {
        if ($this->exists($path)) {
            throw new FileExistsException($path);
        }

        $location = $this->applyBasePath($path);
        $this->ensureDirectory(dirname($location));
        $stream = @fopen($location, 'w+b');

        if (! $stream) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        stream_copy_to_stream($resource, $stream);

        if (! fclose($stream)) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        if (($visibility = ArrayHelper::get($config, 'visibility')) !== null) {
            $this->setVisibility($path, $visibility);
        }

        unset($location, $stream, $visibility);

        return true;
    }

    /**
     * 更新文件内容
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryCreateFailureException
     * @throws \One\Filesystem\Exception\DirectoryUnreadableException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function update(string $path, string $contents, array $config = []): bool
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        $location = $this->applyBasePath($path);
        $this->ensureDirectory(dirname($location));

        if (file_put_contents($location, $contents, $this->writeFlags) === false) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        if (($visibility = ArrayHelper::get($config, 'visibility')) !== null) {
            $this->setVisibility($path, $visibility);
        }

        unset($location, $visibility);

        return true;
    }

    /**
     * 以数据流的方式更新文件内容
     *
     * @param string $path
     * @param resource $resource
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryCreateFailureException
     * @throws \One\Filesystem\Exception\DirectoryUnreadableException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function updateStream(string $path, $resource, array $config = []): bool
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        $location = $this->applyBasePath($path);
        $this->ensureDirectory(dirname($location));
        $stream = @fopen($location, 'w+b');

        if (! $stream) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        stream_copy_to_stream($resource, $stream);

        if (! fclose($stream)) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        if (($visibility = ArrayHelper::get($config, 'visibility')) !== null) {
            $this->setVisibility($path, $visibility);
        }

        unset($location, $stream, $visibility);

        return true;
    }

    /**
     * 获得文件的源数据
     *
     * **返回结果**
     *
     * ```
     * [
     *     'type' => 'file', // dir|file
     *     'path' => 'filepath', // 不包含 BasePath
     *     'timestamp' => 1583734838,
     *     'size' => 0, // 如果 type !== file，不会返回此字段
     * ]
     * ```
     *
     * @param string $path
     *
     * @return array
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function getMetaData(string $path): array
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        $location = $this->applyBasePath($path);
        $info = new SplFileInfo($location);
        $metadata = $this->normalizeFileInfo($info);

        unset($location, $info);

        return $metadata === null ? [] : $metadata;
    }

    /**
     * 获得文件的类型
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function getMimeType(string $path): string
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        $location = $this->applyBasePath($path);
        $finfo = new Finfo(FILEINFO_MIME_TYPE);
        $mimetype = $finfo->file($location);

        if (in_array($mimetype, ['application/octet-stream', 'inode/x-empty'])) {
            $mimetype = MimeTypeExtension::getMimeTypeByFilePath($location);
        }

        return $mimetype;
    }

    /**
     * 重命名文件
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryCreateFailureException
     * @throws \One\Filesystem\Exception\DirectoryUnreadableException
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function rename(string $path, string $newpath): bool
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        $location = $this->applyBasePath($path);
        $destination = $this->applyBasePath($newpath);

        $this->ensureDirectory(dirname($destination));

        return rename($location, $destination);
    }

    /**
     * 删除文件
     *
     * @param string $path
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function delete(string $path): bool
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        return unlink($this->applyBasePath($path));
    }

    /**
     * 创建目录
     *
     * @param string $dirname
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryExistsException
     */
    public function createDir(string $dirname, array $config = []): bool
    {
        $location = $this->applyBasePath($dirname);

        if (is_dir($location)) {
            throw new DirectoryExistsException($dirname);
        }

        $umask = umask(0);
        $visibility = ArrayHelper::get($config, 'visibility', 'public');

        if (mkdir($location, $this->permissions['dir'][$visibility], true)) {
            umask($umask);

            return true;
        }
        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * 删除目录
     *
     * @param string $dirname
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryNotExistsException
     */
    public function deleteDir(string $dirname): bool
    {
        $location = $this->applyBasePath($dirname);

        if (! is_dir($location)) {
            throw new DirectoryNotExistsException($dirname);
        }

        $contents = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($location, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($contents as $file) {
            if ($file->isReadable()) {
                switch ($file->getType()) {
                    case 'dir':
                        rmdir($file->getRealPath());
                        break;
                    case 'link':
                        unlink($file->getPathname());
                        break;
                    default:
                        unlink($file->getRealPath());
                        break;
                }
            }
        }

        unset($contents);

        return rmdir($location);
    }

    /**
     * 返回文件可见性
     *
     * @param  string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function getVisibility(string $path): string
    {
        $location = $this->applyBasePath($path);

        if (! is_dir($location) && ! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        clearstatcache(false, $location);
        $permission = octdec(substr(sprintf('%o', fileperms($location)), -4));

        return $permission & 0044 ? self::VIS_PUB : self::VIS_PRI;
    }

    /**
     * 设置文件可见性
     *
     * @param string $path
     * @param string $visibility
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FileNotExistsException
     */
    public function setVisibility(string $path, string $visibility): bool
    {
        $location = $this->applyBasePath($path);
        $type = is_dir($location) ? 'dir' : 'file';

        if ($type === 'file' && ! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }

        return chmod($location, $this->permissions[$type][$visibility]);
    }


    /**
     * 确认目录存在
     *
     * @param string $path
     *
     * @return void
     * @throws \One\Filesystem\Exception\DirectoryCreateFailureException
     * @throws \One\Filesystem\Exception\DirectoryUnreadableException
     */
    protected function ensureDirectory(string $path): void
    {
        // @codeCoverageIgnoreStart
        if (! is_dir($path)) {
            $umask = umask(0);
            @mkdir($path, $this->permissions['dir']['public'], true);
            umask($umask);

            if (! is_dir($path)) {
                throw new DirectoryCreateFailureException($path);
            }
        }

        if (! is_readable($path)) {
            throw new DirectoryUnreadableException($path);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * 标准化文件信息
     *
     * **返回结果**
     *
     * ```
     * [
     *     'type' => 'file', // dir|file
     *     'path' => 'filepath', // 不包含 BasePath
     *     'timestamp' => 1583734838,
     *     'size' => 0, // 如果 type !== file，不会返回此字段
     * ]
     * ```
     *
     * @param \SplFileInfo $file
     *
     * @return array|null
     */
    protected function normalizeFileInfo(SplFileInfo $file): ?array
    {
        if (! $file->isLink()) {
            $normalized = [
                'type' => $file->getType(),
                'path' => $this->getFilePath($file),
                'timestamp' => $file->getMTime()
            ];

            if ($normalized['type'] === 'file') {
                $normalized['size'] = $file->getSize();
            }

            return $normalized;
        }

        return null;
    }

    /**
     * 从 SplFileInfo 中返回完整文件路径
     *
     * @param \SplFileInfo $file
     *
     * @return string
     */
    protected function getFilePath(SplFileInfo $file): string
    {
        $location = $file->getPathName();
        $path = $this->removeBasePath($location);

        unset($location);

        return trim(str_replace('\\', '/', $path), '/');
    }
}
