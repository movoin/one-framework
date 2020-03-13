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
 * 文件异常类
 *
 * @since 0.2
 */
class FileException extends FilesystemException
{
    /**
     * 文件名
     *
     * @var string
     */
    private $fileName;

    /**
     * 文件异常
     *
     * @param string $message
     * @param string $adapterName
     * @param string $fileName
     *
     * @return \One\Filesystem\Exception\FileException
     */
    public static function fileException(string $message, string $adapterName, string $fileName): self
    {
        $ex = new static([
            $message => [
                'adapter' => $adapterName,
                'file' => $fileName,
            ]
        ]);

        $ex->setAdapterName($adapterName);
        $ex->setFileName($fileName);

        return $ex;
    }
    /**
     * 文件不存在
     *
     * @param string $adapterName
     * @param string $fileName
     *
     * @return \One\Filesystem\Exception\FileException
     */
    public static function fileNotExistsException(string $adapterName, string $fileName): self
    {
        return static::fileException('适配器 {adapter}: 文件 {file} 不存在', $adapterName, $fileName);
    }

    /**
     * 文件已存在
     *
     * @param string $adapterName
     * @param string $fileName
     *
     * @return \One\Filesystem\Exception\FileException
     */
    public static function fileExistsException(string $adapterName, string $fileName): self
    {
        return static::fileException('适配器 {adapter}: 文件 {file} 已存在', $adapterName, $fileName);
    }

    /**
     * 设置文件名
     *
     * @param string $fileName
     *
     * @return void
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * 获得文件名
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
