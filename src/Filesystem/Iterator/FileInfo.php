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

use SplFileInfo;
use One\Exception\RuntimeException;

/**
 * 文件信息对象
 *
 * @since 0.2
 */
class FileInfo extends SplFileInfo
{
    /**
     * 相对路径
     *
     * @var string
     */
    private $relativePath;
    /**
     * 相对路径名称
     *
     * @var string
     */
    private $relativePathname;

    /**
     * 构造
     *
     * @param string $file
     * @param string $relativePath
     * @param string $relativePathname
     */
    public function __construct(string $file, string $relativePath, string $relativePathname)
    {
        parent::__construct($file);

        $this->relativePath = $relativePath;
        $this->relativePathname = $relativePathname;
    }

    /**
     * 获得相对路径
     *
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    /**
     * 获得相对路径名称
     *
     * @return string
     */
    public function getRelativePathname(): string
    {
        return $this->relativePathname;
    }

    /**
     * 获得不含扩展名的文件名
     *
     * @return string
     */
    public function getFilenameWithoutExtension(): string
    {
        return pathinfo($this->getFilename(), PATHINFO_FILENAME);
    }

    /**
     * 获得文件内容
     *
     * @return string
     * @throws \One\Exception\RuntimeException
     */
    public function getContents(): string
    {
        set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });

        $content = file_get_contents($this->getPathname());

        restore_error_handler();

        if (false === $content) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException($error);
            // @codeCoverageIgnoreEnd
        }

        return $content;
    }
}
