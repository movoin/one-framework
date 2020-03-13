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

use One\Exception\RuntimeException;

/**
 * 文件系统异常类
 *
 * @since 0.2
 */
class FilesystemException extends RuntimeException
{
    /**
     * 文件系统适配器名称
     *
     * @var string
     */
    private $adapterName = '';

    /**
     * 设置文件系统适配器名称
     *
     * @param string $adapterName
     *
     * @return void
     */
    public function setAdapterName(string $adapterName): void
    {
        $this->adapterName = $adapterName;
    }

    /**
     * 获得文件系统适配器名称
     *
     * @return string
     */
    public function getAdapterName(): string
    {
        return $this->adapterName;
    }
}
