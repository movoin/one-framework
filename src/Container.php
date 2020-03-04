<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One;

use Closure;
use ReflectionException;
use One\Exception\ContainerException;
use One\Utility\Reflection;

/**
 * 对象容器类
 *
 * @since 0.2
 */
class Container
{
    /**
     * 对象别名映射集
     *
     * @var array
     */
    private $alias = [];
    /**
     * 绑定对象集
     *
     * @var array
     */
    private $bindings = [];
    /**
     * 对象实例集
     *
     * @var array
     */
    private $objects = [];

    /**
     * 判断是否存在指定对象
     *
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->alias[trim($id)]) ||
                isset($this->bindings[trim($id)]) ||
                isset($this->objects[trim($id)]);
    }

    /**
     * 获取指定对象实例
     *
     * @param string $id
     *
     * @return object
     * @throws \One\Exception\ContainerException
     */
    public function get(string $id): object
    {
        if ($this->has($id)) {
            return $this->make($id);
        }

        throw new ContainerException(sprintf(
            '"%s" 未在容器中定义',
            trim($id)
        ));
    }

    /**
     * 映射对象别名
     *
     * @param string $id
     * @param string $alias
     *
     * @return void
     */
    public function alias(string $id, string $alias): void
    {
        $this->alias[trim($alias)] = trim($id);
    }

    /**
     * 绑定对象申明过程，对象将在调用时实例化
     *
     * @param string $id
     * @param mixed $concrete
     *
     * @return void
     * @throws \One\Exception\ContainerException
     */
    public function bind(string $id, $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = trim($id);
        }

        if (! $concrete instanceof Closure) {
            $concrete = function ($container, $parameters = []) use ($id, $concrete) {
                if ($id === $concrete) {
                    return $container->resolve($concrete, $parameters);
                }
                return $container->make($concrete, $parameters);
            };
        }

        $this->bindings[trim($id)] = $concrete;
    }

    /**
     * 获得对象实例
     *
     * @param string $id
     * @param array $parameters
     * @param boolean $createNew
     *
     * @return object
     * @throws \One\Exception\ContainerException
     */
    public function make(string $id, array $parameters = [], bool $createNew = false): object
    {
        if (isset($this->alias[trim($id)])) {
            $id = $this->alias[trim($id)];
        }

        if ($createNew || ! isset($this->objects[trim($id)])) {
            $concrete = isset($this->bindings[trim($id)]) ?
                        $this->bindings[trim($id)] :
                        $id;

            $this->objects[trim($id)] = $this->resolve($concrete, $parameters);
        }

        return $this->objects[trim($id)];
    }

    /**
     * 实例化对象
     *
     * @param mixed $concrete
     * @param array $parameters
     *
     * @return object
     * @throws \One\Exception\ContainerException
     */
    public function resolve($concrete, array $parameters = []): object
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        try {
            $object = Reflection::newInstance($concrete, $parameters);
        } catch (ReflectionException $e) {
            throw new ContainerException(
                sprintf('检索时出现容器错误 "%s"', $concrete),
                $e->getCode(),
                $e
            );
        }

        return $object;
    }
}
