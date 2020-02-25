<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Utility\Helper\Fixtures
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Utility\Helper\Fixtures;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

class ObjectClass implements ArrayAccess, Countable, IteratorAggregate
{
    private $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
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
     */
    public function offsetSet($offset, $value): void
    {
        $this->items[$offset] = $value;
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
