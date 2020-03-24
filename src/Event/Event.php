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

use One\Collection\Concern\HasContext;
use One\Collection\Concern\HasContextGetter;
use One\Event\Emitter;
use One\Event\Contract\EventInterface;

/**
 * 事件类
 *
 * @since 0.2
 */
class Event implements EventInterface
{
    use HasContext;
    use HasContextGetter;

    /**
     * 事件名称
     *
     * @var string
     */
    protected $name;
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
}
