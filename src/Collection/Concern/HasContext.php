<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Collection\Concern
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Collection\Concern;

use One\Collection\Context;

/**
 * 上下文容器特征
 *
 * @since 0.2
 */
trait HasContext
{
    /**
     * 上下文容器
     *
     * @var \One\Collection\Context
     */
    private $context;

    /**
     * 设置容器
     *
     * @param \One\Collection\Context $context
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    /**
     * 获得上下文容器
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
