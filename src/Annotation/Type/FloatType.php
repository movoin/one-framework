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
use One\Annotation\Exception\AnnotationParseErrorException;

/**
 * 浮点数值类型
 *
 * @since 0.2
 */
class FloatType implements TypeInterface
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
            throw new AnnotationParseErrorException((string) $value, '必须为 Float 类型');
        }

        return (float) $value;
    }
}
