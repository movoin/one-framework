<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Filesystem\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Filesystem\Exception;

use Throwable;
use One\Filesystem\Exception\FilesystemException;

/**
 * 目录异常类
 *
 * @since 0.2
 */
class DirectoryException extends FilesystemException
{
    /**
     * 目录名
     *
     * @var string
     */
    private $dirName;

    /**
     * 目录异常
     *
     * @param string $message
     * @param string $adapterName
     * @param string $dirName
     *
     * @return \One\Filesystem\Exception\DirectoryException
     */
    public static function directoryException(string $message, string $adapterName, string $dirName): self
    {
        $ex = new static([
            $message => [
                'adapter' => $adapterName,
                'dir' => $dirName,
            ]
        ]);

        $ex->setAdapterName($adapterName);
        $ex->setDirName($dirName);

        return $ex;
    }

    /**
     * 目录不存在
     *
     * @param string $adapterName
     * @param string $dirName
     *
     * @return \One\Filesystem\Exception\DirectoryException
     */
    public static function directoryNotExistsException(string $adapterName, string $dirName): self
    {
        return static::directoryException('适配器 {adapter}: 目录 {dir} 不存在', $adapterName, $dirName);
    }

    /**
     * 目录已存在
     *
     * @param string $adapterName
     * @param string $dirName
     *
     * @return \One\Filesystem\Exception\DirectoryException
     */
    public static function directoryExistsException(string $adapterName, string $dirName): self
    {
        return static::directoryException('适配器 {adapter}: 目录 {dir} 已存在', $adapterName, $dirName);
    }

    /**
     * 设置目录名称
     *
     * @param string $dirName
     *
     * @return void
     */
    public function setDirName(string $dirName): void
    {
        $this->dirName = $dirName;
    }

    /**
     * 获得文件名
     *
     * @return string
     */
    public function getDirName(): string
    {
        return $this->dirName;
    }
}
