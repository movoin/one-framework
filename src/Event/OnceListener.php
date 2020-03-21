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
use One\Utility\Assert;

/**
 * 一次性事件监听类，触发事件后将被注销，即仅被触发一次
 *
 * @since 0.2
 */
class OnceListener extends Listener
{
    /**
     * 响应事件
     *
     * @param \One\Event\Contract\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event): void
    {
        $name = $event->getName();
        $emitter = $event->getEmitter();
        $emitter->removeListener($name, $this);

        unset($emitter, $name);

        call_user_func_array($this->handler, [$event]);
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
        if (Assert::instanceOf($listener, OnceListener::class)) {
            $listener = $listener->getHandler();
        }

        return $this->handler === $listener;
    }
}
