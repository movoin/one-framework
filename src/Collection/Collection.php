<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Collection
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use One\Collection\Contract\Arrayable;
use One\Collection\Contract\Jsonable;
use One\Utility\Helper\ArrayHelper;
use One\Utility\Helper\JsonHelper;

/**
 * 数据集合类
 *
 * @since 0.2
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate, Arrayable, Jsonable
{
    /**
     * 集合数据
     *
     * @var array
     */
    private $items = [];

    /**
     * 构造
     *
     * @param \One\Collection\Contract\Arrayable|array $items
     */
    final public function __construct($items = [])
    {
        $this->items = ArrayHelper::toArray($items);
    }

    /**
     * 返回是否存在指定键名的数据
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * 设置集合数据
     *
     * @param string|null $key
     * @param mixed $value
     *
     * @return self
     */
    public function set($key, $value): self
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * 获得指定键名的数据，不存在则返回默认值
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return ($value = $this->offsetGet($key)) === null ? $default : $value;
    }

    /**
     * 获得容器中的全部数据
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * 获得指定键名的容器对象
     *
     * @param array $keys
     *
     * @return self
     */
    public function only(...$keys): self
    {
        return new static(ArrayHelper::only($this->items, $keys));
    }

    /**
     * 获得指定数量数据的容器对象
     *
     * @param int $limit
     *
     * @return self
     */
    public function take(int $limit): self
    {
        return new static(array_slice($this->items, 0, $limit, true));
    }

    /**
     * 当条件成立时执行回调
     *
     * @param mixed $value
     * @param callable $callback
     * @param callable $default
     *
     * @return mixed
     */
    public function when($value, callable $callback, callable $default = null)
    {
        if ($value) {
            return $callback($this, $value);
        }

        if ($default) {
            return $default($this, $value);
        }

        return $this;
    }

    /**
     * 当集合为空时执行回调
     *
     * @param callable $callback
     * @param callable $default
     *
     * @return mixed
     */
    public function whenEmpty(callable $callback, callable $default = null)
    {
        return $this->when($this->isEmpty(), $callback, $default);
    }

    /**
     * 当集合不为空时执行回调
     *
     * @param callable $callback
     * @param callable $default
     *
     * @return mixed
     */
    public function whenNotEmpty(callable $callback, callable $default = null)
    {
        return $this->when($this->isNotEmpty(), $callback, $default);
    }

    /**
     * 获得过滤后的新集合实例
     *
     * @param callable $callback
     *
     * @return self
     */
    public function filter(callable $callback = null): self
    {
        if ($callback) {
            return new static(ArrayHelper::where($this->items, $callback));
        }

        return new static(array_filter($this->items));
    }

    /**
     * 遍历集合并将修改后的结果创建新的集合实例
     *
     * @param callable $callback
     *
     * @return self
     */
    public function map(callable $callback): self
    {
        $keys = array_keys($this->items);
        $values = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $values));
    }

    /**
     * 遍历集合
     *
     * @param callable $callback
     *
     * @return self
     */
    public function each(callable $callback): self
    {
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * 获得键值反转后的新集合实例
     *
     * @return self
     */
    public function flip(): self
    {
        return new static(array_flip($this->items));
    }

    /**
     * 获得仅包含键名数据的新集合实例
     *
     * @return self
     */
    public function keys(): self
    {
        return new static(array_keys($this->items));
    }

    /**
     * 合并数据并返回新的集合实例
     *
     * @param mixed $items
     *
     * @return self
     */
    public function merge($items): self
    {
        return new static(ArrayHelper::merge($this->items, $items));
    }

    /**
     * 删除集合中的指定数据
     *
     * @param string ...$keys
     *
     * @return self
     */
    public function remove(...$keys): self
    {
        foreach ($keys as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * 获得并删除集合中最后一个数据
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * 获得并删除集合中首个数据
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * 判断容器是否为空
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * 判断容器是否不为空
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * 获得集合总计数量
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * 获得数组对象
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * 转换为 JSON
     *
     * @return string
     */
    public function toJson(): string
    {
        return JsonHelper::encode($this->items);
    }

    /**
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return (bool) array_key_exists($offset, $this->items);
    }

    /**
     * @param string $offset
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @param string $offset
     * @param string $value
     *
     * @throws \One\Collection\Exception\InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * 获得集合迭代器
     *
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
