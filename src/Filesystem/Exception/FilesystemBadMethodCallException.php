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

use One\Exception\BadMethodCallException;

/**
 * 文件系统非法方法调用异常类 (308)
 *
 * @since 0.2
 */
class FilesystemBadMethodCallException extends BadMethodCallException
{
    /**
     * 代码: 308
     *
     * @var int
     */
    protected $code = 308;

    /**
     * 构造
     *
     * @param string $filesystem
     * @param string $method
     */
    public function __construct(string $filesystem, string $method)
    {
        parent::__construct(static::formatMessage(
            '文件系统 {filesystem}: 方法 {$method}() 不存在.',
            [
                'filesystem' => $filesystem,
                'method' => $method
            ]
        ));
    }
}
