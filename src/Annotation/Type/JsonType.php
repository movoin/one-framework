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
use One\Utility\Encode\Json;
use One\Utility\Encode\Exception\EncodeException;

/**
 * Json 类型
 *
 * @since 0.2
 */
class JsonType implements TypeInterface
{
    /**
     * 解析 Json 类型
     *
     * @param string $value
     *
     * @return mixed
     */
    public function parse(string $value)
    {
        try {
            $json = Json::decode($value);
        } catch (EncodeException $e) {
            throw new AnnotationParseErrorException($value, '必须为 JSON 类型');
        }

        return $json;
    }
}
