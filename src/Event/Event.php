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

use One\Collection\Context;
use One\Event\Emitter;
use One\Event\Contract\EventInterface;

/**
 * 事件类
 *
 * @since 0.2
 */
class Event implements EventInterface
{
    /**
     * 事件名称
     *
     * @var string
     */
    protected $name;
    /**
     * 事件上下文对象
     *
     * @var \One\Collection\Context
     */
    protected $context;
    /**
     * 事件触发对象
     *
     * @var \One\Event\Emitter
     */
    protected $emitter;

    /**
     * 构造
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * 获得事件名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获得事件触发对象
     *
     * @return \One\Event\Emitter
     */
    public function getEmitter(): Emitter
    {
        return $this->emitter;
    }

    /**
     * 设置事件触发对象
     *
     * @param \One\Event\Emitter $emitter
     *
     * @return self
     */
    public function setEmitter(Emitter $emitter): self
    {
        $this->emitter = $emitter;

        return $this;
    }

    /**
     * 获得事件上下文对象
     *
     * @return \One\Collection\Context
     */
    public function getContext(): Context
    {
        if ($this->context === null) {
            $this->context = new Context;
        }

        return $this->context;
    }

    /**
     * 设置事件上下文
     *
     * @param array $contexts
     *
     * @return self
     */
    public function setContexts(array $contexts): self
    {
        if ($contexts) {
            $this->getContext()->setMulti($contexts);
        }

        return $this;
    }
}
