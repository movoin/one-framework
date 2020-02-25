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

/**
 * 上下文容器
 *
 * @method bool  has(string $key)
 * @method array all()
 * @method mixed get(string $key, $default = null, ?string $class = null)
 * @method void  set(string $key, $value)
 * @method void  setMulti(array $items)
 * @method void  unset(string $key)
 */
class Context
{
    /**
     * 上下文数据
     *
     * @var array
     */
    protected $items = [];

    /**
     * 构造
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * 判断是否存在指定上下文
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        if ($this->items === []) {
            return false;
        }

        if (isset($this->items[$key]) || array_key_exists($key, $this->items)) {
            return true;
        }

        return false;
    }

    /**
     * 获得全部上下文
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * 获得上下文中的指定值
     *
     * @param string $key
     * @param mixed $default
     * @param string|null $class
     *
     * @return mixed
     */
    public function get(string $key, $default = null, ?string $class = null)
    {
        $key = $this->normalizeKey($key);

        if (! isset($this->items[$key])) {
            return $default;
        }

        if ($class  === null) {
            return $this->items[$key];
        }

        if ($this->items[$key] instanceof $class
            || is_subclass_of($this->items[$key], $class)
        ) {
            return $this->items[$key];
        }

        return $default;
    }

    /**
     * 设置上下文
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->items[$this->normalizeKey($key)] = $value;
    }

    /**
     * 批量设置上下文
     *
     * @param array $items
     *
     * @return void
     */
    public function setMulti(array $items): void
    {
        array_walk($items, function ($value, $key) {
            $this->set($key, $value);
        });
    }

    /**
     * 删除指定上下文
     *
     * @param string $key
     *
     * @return void
     */
    public function unset(string $key): void
    {
        unset($this->items[$this->normalizeKey($key)]);
    }

    /**
     * 标准化上下文键名
     *
     * @param string $key
     *
     * @return string
     */
    protected function normalizeKey(string $key): string
    {
        return trim(
            strtolower($key)
        );
    }
}
