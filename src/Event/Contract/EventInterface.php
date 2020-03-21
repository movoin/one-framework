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

use One\Collection\Context;
use One\Event\Emitter;

/**
 * 事件接口
 *
 * @since 0.2
 */
interface EventInterface
{
    /**
     * 获得事件名称
     *
     * @return string
     */
    public function getName(): string;

    /**
     * 获得事件触发对象
     *
     * @return \One\Event\Emitter
     */
    public function getEmitter(): Emitter;

    /**
     * 设置事件触发对象
     *
     * @param \One\Event\Emitter $emitter
     *
     * @return self
     */
    public function setEmitter(Emitter $emitter);

    /**
     * 获得事件上下文对象
     *
     * @return \One\Collection\Context
     */
    public function getContext(): Context;
}
