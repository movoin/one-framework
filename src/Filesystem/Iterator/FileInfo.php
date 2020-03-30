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
use One\Filesystem\Exception\FileReadFailureException;

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
     * @throws \One\Filesystem\Exception\FileReadFailureException
     */
    public function getContents(): string
    {
        $content = file_get_contents($this->getPathname());

        if (false === $content) {
            // @codeCoverageIgnoreStart
            throw new FileReadFailureException($this->getFilename());
            // @codeCoverageIgnoreEnd
        }

        return $content;
    }
}
