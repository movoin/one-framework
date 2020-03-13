<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Annotation\Contract
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Annotation\Contract;

/**
 * 类型接口
 *
 * @since 0.2
 */
interface TypeInterface
{
    /**
     * 解析类型
     *
     * @param string $value
     *
     * @return mixed
     */
    public function parse(string $value);
}
