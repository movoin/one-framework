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

use One\Collection\Container;

/**
 * 对象容器特征
 *
 * @since 0.2
 */
trait HasContainer
{
    /**
     * 对象容器
     *
     * @var \One\Collection\Container
     */
    private $container;

    /**
     * 设置容器
     *
     * @param \One\Collection\Container $container
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * 获得容器
     *
     * @return \One\Collection\Container
     */
    public function getContainer(): Container
    {
        if ($this->container === null) {
            $this->container = new Container;
        }

        return $this->container;
    }
}
