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

use One\Collection\Contract\Arrayable as ArrayableInterface;

class Arrayable implements ArrayableInterface
{
    private $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * 转换数据为数组类型
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
