<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Collection\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Collection\Exception;

use Throwable;
use One\Exception\NotExistsException;

/**
 * 容器对象创建异常类 (200)
 *
 * @since 0.2
 */
class ContainerException extends NotExistsException
{
    /**
     * 代码: 200
     *
     * @var int
     */
    protected $code = 200;

    /**
     * 构造
     *
     * @param mixed $concrete
     * @param \Throwable $previous
     */
    public function __construct($concrete, Throwable $previous = null)
    {
        parent::__construct(static::formatMessage(
            '创建对象时出现容器错误 "{concrete}".',
            [
                'concrete' => (string) $concrete
            ]
        ));
    }
}
