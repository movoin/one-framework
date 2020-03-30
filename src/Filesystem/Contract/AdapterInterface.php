<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Filesystem\Contract
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Filesystem\Contract;

/**
 * 文件系统适配器接口
 *
 * !!! 扩展接口时，请务必确保方法的第一个参数为 string $path 路径参数（名称随意），
 * !!! 否则在 Manager 中将出现不可预期的问题。详见（@see Manager::filterPrefix())
 *
 * @since 0.2
 */
interface AdapterInterface
{
    /**
     * 公有
     */
    const VIS_PUB = 'public';
    /**
     * 私有
     */
    const VIS_PRI = 'private';

    /**
     * 判断文件是否存在
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * 读取文件内容
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FileException
     */
    public function read(string $path): string;

    /**
     * 从文件中读取数据流
     *
     * @param string $path
     *
     * @return resource
     * @throws \One\Filesystem\Exception\FileException
     */
    public function readStream(string $path);

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
    public function listContents(string $directory = '', bool $recursive = false): array;

    /**
     * 写入文件内容
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemException
     * @throws \One\Filesystem\Exception\FileException
     */
    public function write(string $path, string $contents, array $config = []): bool;

    /**
     * 以数据流的方式写入文件内容
     *
     * @param string $path
     * @param resource $resource
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemException
     * @throws \One\Filesystem\Exception\FileException
     */
    public function writeStream(string $path, $resource, array $config = []): bool;

    /**
     * 更新文件内容
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemException
     * @throws \One\Filesystem\Exception\FileException
     */
    public function update(string $path, string $contents, array $config = []): bool;

    /**
     * 以数据流的方式更新文件内容
     *
     * @param string $path
     * @param resource $resource
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemException
     * @throws \One\Filesystem\Exception\FileException
     */
    public function updateStream(string $path, $resource, array $config = []): bool;

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
     * @throws \One\Filesystem\Exception\FileException
     */
    public function getMetaData(string $path): array;

    /**
     * 获得文件的类型
     *
     * @param string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FileException
     */
    public function getMimeType(string $path): string;

    /**
     * 重命名文件
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FilesystemException
     * @throws \One\Filesystem\Exception\FileException
     */
    public function rename(string $path, string $newpath): bool;

    /**
     * 删除文件
     *
     * @param string $path
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FileException
     */
    public function delete(string $path): bool;

    /**
     * 创建目录
     *
     * @param string $dirname
     * @param array $config
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryException
     */
    public function createDir(string $dirname, array $config = []): bool;

    /**
     * 删除目录
     *
     * @param string $dirname
     *
     * @return bool
     * @throws \One\Filesystem\Exception\DirectoryException
     */
    public function deleteDir(string $dirname): bool;

    /**
     * 返回文件可见性
     *
     * @param  string $path
     *
     * @return string
     * @throws \One\Filesystem\Exception\FileException
     */
    public function getVisibility(string $path): string;

    /**
     * 设置文件可见性
     *
     * @param string $path
     * @param string $visibility
     *
     * @return bool
     * @throws \One\Filesystem\Exception\FileException
     */
    public function setVisibility(string $path, string $visibility): bool;
}
