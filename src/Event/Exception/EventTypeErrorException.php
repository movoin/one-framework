<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Event\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Event\Exception;

use One\Exception\TypeErrorException;

/**
 * 事件类型错误异常类 (300)
 *
 * @since 0.2
 */
class EventTypeErrorException extends TypeErrorException
{
    /**
     * 代码: 300
     *
     * @var int
     */
    protected $code = 300;

    /**
     * 构造
     */
    public function __construct()
    {
        parent::__construct('事件必须为 "字符串" 或 "EventInterface 实例".');
    }
}
