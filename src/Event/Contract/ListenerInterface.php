<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Event\Contract
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Event\Contract;

use Closure;
use One\Event\Contract\EventInterface;

/**
 * 事件监听接口
 *
 * @since 0.2
 */
interface ListenerInterface
{
    /**
     * 响应事件
     *
     * @param \One\Event\Contract\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event): void;

    /**
     * 获得事件响应句柄
     *
     * @return \Closure|array
     */
    public function getHandler();

    /**
     * 设置事件响应句柄
     *
     * @param \Closure|callable|array $handler
     *
     * @return void
     */
    public function setHandler($handler): void;

    /**
     * 判断是否为自己
     *
     * @param mixed $listener
     *
     * @return bool
     */
    public function isSelf($listener): bool;
}
