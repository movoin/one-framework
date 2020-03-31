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

use One\Utility\Reflection;

/**
 * 事件行为特征
 *
 * !!! 依赖 HasEvent 特征，或实现有 getEmitter(): One\Event\Emitter 方法
 *
 * **示例**
 *
 * ```
 * public function onServerStart(EventInterface $event): void
 * {
 * }
 * ```
 *
 * onServerStart 将被初始化为 $emitter->on('server.start', 'onServerStart');
 *
 * @since 0.2
 */
trait HasEventBehavior
{
    /**
     * 初始化事件行为
     *
     * @return void
     * @throws \ReflectionException
     */
    public function initializeEventBehavior(): void
    {
        $methods = Reflection::getMethods($this, 'on');

        array_walk($methods, function ($method) {
            $handler = $method->getName();
            $event = $this->convertToEventName($handler);

            $this->getEmitter()->on($event, [$this, $handler]);

            unset($handler, $event);
        });

        unset($methods);
    }

    /**
     * 将 on 方法名转换为事件名
     *
     * @param string $methodName
     *
     * @return string
     */
    protected function convertToEventName(string $methodName): string
    {
        return ltrim(
            strtolower(
                preg_replace('/[A-Z]/', '.${0}', $methodName)
            ),
            'on.'
        );
    }
}
