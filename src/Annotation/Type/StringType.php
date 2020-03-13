<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Annotation\Type
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Annotation\Type;

use One\Annotation\Contract\TypeInterface;
use One\Annotation\Exception\ParseException;

/**
 * 字符串类型
 *
 * @since 0.2
 */
class StringType implements TypeInterface
{
    /**
     * 解析字符串类型
     *
     * @param string $value
     *
     * @return mixed
     */
    public function parse(string $value)
    {
        return trim($value);
    }
}
