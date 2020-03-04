<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Collection\Contract
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Collection\Contract;

/**
 * 数组类型接口
 *
 * @since 0.2
 */
interface Arrayable
{
    /**
     * 转换数据为数组类型
     *
     * @return array
     */
    public function toArray(): array;
}
