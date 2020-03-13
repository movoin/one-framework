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
use One\Annotation\Type\FloatType;
use One\Annotation\Type\IntegerType;
use One\Annotation\Type\JsonType;
use One\Annotation\Type\StringType;

/**
 * 动态类型
 *
 * @since 0.2
 */
class DynamicType implements TypeInterface
{
    /**
     * 解析动态类型
     *
     * @param string $value
     *
     * @return mixed
     */
    public function parse(string $value)
    {
        // Boolean
        if ($value === '') {
            return true;
        }

        // Json
        try {
            $json = (new JsonType)->parse($value);
            return $json;
        } catch (ParseException $e) {
        }
        if (isset($json) && ! empty($json)) return $json;

        // Int
        try {
            $int = (new IntegerType)->parse($value);
        } catch (ParseException $e) {
        }
        if (isset($int) && ! empty($int)) return $int;

        // Float
        try {
            $float = (new FloatType)->parse($value);
        } catch (ParseException $e) {
        }
        if (isset($float) && ! empty($float)) return $float;

        // String
        return (new StringType)->parse($value);
    }
}
