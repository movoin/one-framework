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

use One\Event\Event;
use One\Event\Listener;
use One\Event\OnceListener;
use One\Event\Contract\EventInterface;
use One\Event\Contract\ListenerInterface;
use One\Event\Exception\EventTypeErrorException;
use One\Event\Exception\ListenerTypeErrorException;
use One\Utility\Assert;
use One\Utility\Reflection;
use One\Utility\Helper\ArrayHelper;

/**
 * 事件触发类
 *
 * @since 0.2
 */
class Emitter
{
    /**
     * 优先级，数值越大排序越前
     */
    const PRI_LOW = 0;
    const PRI_NORMAL = 10;
    const PRI_HIGH = 100;

    /**
     * 事件监听句柄
     *
     * @var array
     */
    protected $listeners;
    /**
     * 按优先级排序的事件监听句柄
     *
     * @var array
     */
    protected $sortedListeners;

    /**
     * 构造
     */
    public function __construct()
    {
        $this->listeners = [];
        $this->sortedListeners = [];
    }

    /**
     * 绑定事件监听句柄
     *
     * @param \One\Event\Contract\EventInterface|string $event
     * @param \One\Event\Contract\ListenerInterface|\Closure|array|string $listener
     * @param int $priority
     *
     * @return self
     * @throws \One\Event\Exception\EventTypeErrorException
     * @throws \One\Event\Exception\ListenerTypeErrorException
     */
    public function on($event, $listener, int $priority = Emitter::PRI_NORMAL): self
    {
        $this->addListener($event, $listener, $priority);

        return $this;
    }

    /**
     * 绑定一次性事件监听句柄
     *
     * @param \One\Event\Contract\EventInterface|string $event
     * @param \One\Event\Contract\ListenerInterface|\Closure|array|string $listener
     * @param int $priority
     *
     * @return self
     * @throws \One\Event\Exception\EventTypeErrorException
     * @throws \One\Event\Exception\ListenerTypeErrorException
     */
    public function once($event, $listener, int $priority = Emitter::PRI_NORMAL): self
    {
        $this->addListener($event, $listener, $priority, true);

        return $this;
    }

    /**
     * 解绑指定事件监听
     *
     * @param \One\Event\Contract\EventInterface|string $event
     *
     * @return self
     * @throws \One\Event\Exception\EventTypeErrorException
     */
    public function off($event): self
    {
        $event = $this->getEventName($event);
        if ($this->hasListener($event)) {
            unset($this->sortedListeners[$event], $this->listeners[$event]);
        }

        return $this;
    }

    /**
     * 触发事件
     *
     * @param \One\Event\Contract\EventInterface|string $event
     * @param array $parameters
     *
     * @return void
     * @throws \One\Event\Exception\EventTypeErrorException
     */
    public function emit($event, array $parameters = []): void
    {
        $event = $this->ensureEvent($event);
        $event->setEmitter($this)->setContexts($parameters + ['time' => microtime(true)]); // 毫秒级浮点时间戳

        $listeners = $this->getListeners($event);

        array_walk($listeners, function ($listener) use ($event) {
            $this->ensureListener($listener)->handle($event);
        });

        unset($event, $listeners);
    }

    /**
     * 获得指定事件监听
     *
     * @param \One\Event\Contract\EventInterface|string $event
     *
     * @return array
     * @throws \One\Event\Exception\EventTypeErrorException
     */
    public function getListeners($event): array
    {
        $event = $this->getEventName($event);

        if (! $this->hasListener($event)) {
            return [];
        }

        if (! ArrayHelper::has($this->sortedListeners, $event)) {
            $listeners = $this->listeners[$event];
            ksort($listeners);
            $this->sortedListeners[$event] = call_user_func_array('array_merge', $listeners);
            unset($listeners);
        }

        return $this->sortedListeners[$event];
    }

    /**
     * 添加事件监听
     *
     * @param \One\Event\Contract\EventInterface|string $event
     * @param \One\Event\Contract\ListenerInterface|\Closure|array|string $listener
     * @param int $priority
     * @param bool $once
     *
     * @return void
     * @throws \One\Event\Exception\EventTypeErrorException
     * @throws \One\Event\Exception\ListenerTypeErrorException
     */
    public function addListener($event, $listener, int $priority = Emitter::PRI_NORMAL, bool $once = false): void
    {
        $event = $this->getEventName($event);

        if (! $this->hasListener($event)) {
            $this->listeners[$event] = [];
        }
        if (! isset($this->listeners[$event][$priority])) {
            $this->listeners[$event][$priority] = [];
        }

        if ($once && Assert::instanceOf($listener, Listener::class)) {
            $listener = new OnceListener($listener->getHandler());
        }
        if (Assert::instanceOf($listener, OnceListener::class)) {
            $once = true;
        }

        $this->listeners[$event][$priority][] = $this->ensureListener($listener, $once);

        unset($this->sortedListeners[$event]);
    }

    /**
     * 移除事件监听
     *
     * @param \One\Event\Contract\EventInterface|string $event
     * @param \One\Event\Contract\ListenerInterface|\Closure|array|string $listener
     *
     * @return bool
     */
    public function removeListener($event, $listener): bool
    {
        if (! $this->hasListener($event)) {
            return true;
        }

        $event = $this->getEventName($event);
        $listeners = $this->listeners[$event];

        foreach ($listeners as $priority => $registed) {
            $listeners[$priority] = array_filter($registed, function ($reg) use ($listener) {
                return ! $reg->isSelf($listener);
            });
        }

        $this->listeners[$event] = $listeners;
        unset($this->sortedListeners[$event], $listeners, $event);

        return true;
    }

    /**
     * 是否存在指定事件监听
     *
     * @param \One\Event\Contract\EventInterface|string $event
     *
     * @return bool
     */
    public function hasListener($event): bool
    {
        $event = $this->getEventName($event);

        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listeners) {
                if (count($listeners) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 获得事件名称
     *
     * @param \One\Event\Contract\EventInterface|string $event
     *
     * @return string
     * @throws \One\Event\Exception\EventTypeErrorException
     */
    protected function getEventName($event): string
    {
        if (Assert::instanceOf($event, EventInterface::class)) {
            return $event->getName();
        }
        if (Assert::string($event)) {
            return $event;
        }

        throw new EventTypeErrorException;
    }

    /**
     * 创建事件实例
     *
     * @param \One\Event\Contract\EventInterface|string $event
     *
     * @return \One\Event\Contract\EventInterface
     * @throws \One\Event\Exception\EventTypeErrorException
     */
    protected function ensureEvent($event): EventInterface
    {
        if (Assert::instanceOf($event, EventInterface::class)) {
            return $event;
        }
        if (Assert::string($event)) {
            return new Event($event);
        }

        throw new EventTypeErrorException;
    }

    /**
     * 创建事件监听实例
     *
     * @param \One\Event\Contract\ListenerInterface|\Closure|array|string $listener
     * @param bool $once
     *
     * @return \One\Event\Contract\ListenerInterface
     * @throws \One\Event\Exception\ListenerTypeErrorException
     */
    protected function ensureListener($listener, bool $once = false): ListenerInterface
    {
        $className = $once ? OnceListener::class : Listener::class;

        if (Assert::instanceOf($listener, $className)) {
            return $listener;
        }
        if (Assert::instanceOf($listener, '\\Closure') || Assert::array($listener)) {
            return new $className($listener);
        }

        unset($className);

        if (Assert::string($listener)) {
            return Reflection::newInstance($listener); // 创建失败将抛出 ReflectionException 异常
        }

        throw new ListenerTypeErrorException;
    }
}
