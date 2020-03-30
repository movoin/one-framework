<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Event\Concern
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Event\Concern;

use One\Event\Event;
use One\Event\Emitter;
use One\Event\Contract\EventInterface;

/**
 * 事件管理特征
 *
 * @since 0.2
 */
trait HasEvent
{
    /**
     * 事件触发对象
     *
     * @var \One\Event\Emitter
     */
    private $emitter;

    /**
     * 获得事件触发对象
     *
     * @return \One\Event\Emitter
     */
    public function getEmitter(): Emitter
    {
        if ($this->emitter === null) {
            $this->emitter = new Emitter;
        }

        return $this->emitter;
    }

    /**
     * 绑定事件监听句柄
     *
     * @param \One\Event\Contract\EventInterface|string $event
     * @param \One\Event\Contract\ListenerInterface|\Closure|array|string $listener
     * @param int $priority
     *
     * @return self
     * @throws \One\Event\Exception\EventInvalidArgumentException
     * @throws \One\Event\Exception\ListenerInvalidArgumentException
     */
    public function on($event, $listener, int $priority = Emitter::PRI_NORMAL): self
    {
        $this->getEmitter()->on($event, $listener, $priority);

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
     * @throws \One\Event\Exception\EventInvalidArgumentException
     * @throws \One\Event\Exception\ListenerInvalidArgumentException
     */
    public function once($event, $listener, int $priority = Emitter::PRI_NORMAL): self
    {
        $this->getEmitter()->once($event, $listener, $priority);

        return $this;
    }

    /**
     * 解绑指定事件监听
     *
     * @param \One\Event\Contract\EventInterface|string $event
     *
     * @return self
     * @throws \One\Event\Exception\EventInvalidArgumentException
     */
    public function off($event): self
    {
        $this->getEmitter()->off($event);

        return $this;
    }

    /**
     * 触发事件
     *
     * @param \One\Event\Contract\EventInterface|string $event
     * @param array $parameters
     *
     * @return void
     * @throws \One\Event\Exception\EventInvalidArgumentException
     */
    public function emit($event, array $parameters = []): void
    {
        $this->getEmitter()->emit($event, $parameters);
    }

    /**
     * 创建事件对象
     *
     * @param string $name
     * @param array $contexts
     *
     * @return \One\Event\Contract\EventInterface
     */
    public function newEvent(string $name, array $contexts = []): EventInterface
    {
        return (new Event($name))->setContexts($contexts);
    }
}
