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

use One\Collection\Exception\ContextValueNotFoundException;

/**
 * 上下文容器 Getter&Setter 特征
 *
 * !!! 依赖 HasContext 特征，或者实现了 getContext(): One\Collection\Context 方法
 *
 * @since 0.2
 */
trait HasContextGetter
{
    /**
     * __get()
     *
     * @param string $name
     *
     * @return mixed
     * @throws \One\Collection\Exception\ContextValueNotFoundException
     */
    public function __get(string $name)
    {
        if ($this->getContext()->has($name)) {
            return $this->getContext()->get($name);
        } elseif (method_exists(get_parent_class(), '__get')) {
            return parent::__get($name);
        }

        throw new ContextValueNotFoundException($name);
    }

    /**
     * __set()
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __set(string $name, $value): void
    {
        $this->getContext()->set($name, $value);
    }
}
