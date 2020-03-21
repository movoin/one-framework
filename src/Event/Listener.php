<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Event
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Event;

use Closure;
use One\Event\Contract\EventInterface;
use One\Event\Contract\ListenerInterface;
use One\Event\Exception\ListenerException;
use One\Utility\Assert;

/**
 * 事件监听类
 *
 * @since 0.2
 */
class Listener implements ListenerInterface
{
    /**
     * 事件响应句柄
     *
     * @var \Closure
     */
    protected $handler;

    /**
     * 构造
     *
     * @param \Closure|array $handler
     *
     * @throws One\Event\Exception\ListenerException
     */
    public function __construct($handler = null)
    {
        if ($handler !== null) {
            $this->setHandler($handler);
        }
    }

    /**
     * 响应事件
     *
     * @param \One\Event\Contract\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event): void
    {
        call_user_func_array($this->getHandler(), [$event]);
    }

    /**
     * 获得事件响应句柄
     *
     * @return \Closure|array
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * 设置事件响应句柄
     *
     * @param \Closure|array $handler
     *
     * @return void
     * @throws \One\Event\Exception\ListenerException
     */
    public function setHandler($handler): void
    {
        if (Assert::array($handler) || Assert::instanceOf($handler, '\\Closure')) {
            $this->handler = $handler;
        } else {
            throw new ListenerException('设置失败，事件响应句柄必须为 `Closure` 或 可回调数组类型!');
        }
    }

    /**
     * 判断是否为自己
     *
     * @param mixed $listener
     *
     * @return bool
     */
    public function isSelf($listener): bool
    {
        if (Assert::instanceOf($listener, Listener::class)) {
            $listener = $listener->getHandler();
        }

        return $this->handler === $listener;
    }
}
