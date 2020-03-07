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

use One\Annotation\Contract\Type;
use One\Annotation\Exception\ParseException;

/**
 * 浮点数值类型
 *
 * @since 0.2
 */
class FloatType implements Type
{
    /**
     * 解析数值类型
     *
     * @param string $value
     *
     * @return mixed
     */
    public function parse(string $value)
    {
        if (false === ($value = filter_var($value, FILTER_VALIDATE_FLOAT))) {
            throw new ParseException(__CLASS__, '必须为浮点数值类型');
        }

        return (float) $value;
    }
}
