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
 * 事件响应句柄设置异常 (301)
 *
 * @since 0.2
 */
class ListenerTypeErrorException extends TypeErrorException
{
    /**
     * 代码: 301
     *
     * @var int
     */
    protected $code = 301;

    /**
     * 构造
     */
    public function __construct()
    {
        parent::__construct('事件监听必须为 "类/方法名字符串", "ListenerInterface 实例", "Closure" 或 "可回调数组" 类型.');
    }
}
