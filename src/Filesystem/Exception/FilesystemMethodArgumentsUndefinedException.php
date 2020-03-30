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

use One\Exception\InvalidArgumentException;

/**
 * 文件系统方法参数未定义异常 (309)
 *
 * @since 0.2
 */
class FilesystemMethodArgumentsUndefinedException extends InvalidArgumentException
{
    /**
     * 代码: 309
     *
     * @var int
     */
    protected $code = 309;

    /**
     * 构造
     *
     * @param string $method
     */
    public function __construct(string $method)
    {
        parent::__construct(static::formatMessage(
            '文件系统 "{method}" 方法参数未定义.',
            [
                'method' => $method
            ]
        ));
    }
}
