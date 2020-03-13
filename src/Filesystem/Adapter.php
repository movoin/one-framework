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

use One\Filesystem\Contract\AdapterInterface;
use One\Utility\Assert;

/**
 * 文件系统适配器抽象类
 *
 * @abstract
 * @since 0.2
 */
abstract class Adapter implements AdapterInterface
{
    /**
     * 根路径
     *
     * @var string
     */
    private $basePath = '';
    /**
     * 路径分隔符
     *
     * @var string
     */
    private $separator = DIRECTORY_SEPARATOR;

    /**
     * 获得根路径
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * 设置根路径
     *
     * @param string $path
     *
     * @return void
     */
    public function setBasePath(string $path): void
    {
        if (Assert::stringNotEmpty($path)) {
            $this->basePath = rtrim($path, '\\/') . $this->separator;
        }
    }

    /**
     * 获得拼接后的根路径
     *
     * @param string $path
     *
     * @return string
     */
    public function applyBasePath(string $path): string
    {
        return $this->getBasePath() . ltrim($path, '\\/');
    }

    /**
     * 获得移除根路径的路径
     *
     * @param string $path
     *
     * @return string
     */
    public function removeBasePath(string $path): string
    {
        return substr($path, strlen($this->getBasePath()));
    }
}
