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

interface Jsonable
{
    /**
     * 转换数据为 JSON 类型
     *
     * @return string
     */
    public function toJson(): string;
}
