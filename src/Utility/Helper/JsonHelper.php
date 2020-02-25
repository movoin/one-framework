<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Utility\Helper
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Utility\Helper;

use One\Utility\Exception\JsonException;

/**
 * JSON 助手类
 *
 * @method string encode($value, int $options, int $depth = 512)
 * @method string decode(string $json, bool $assoc = true, int $depth = 512, int $options)
 *
 * @static
 */
class JsonHelper
{
    /**
     * 对变量进行 JSON 编码
     *
     * @static
     *
     * @param mixed $value
     * @param int $options
     * @param int $depth
     *
     * @return string
     */
    public static function encode(
        $value,
        int $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION,
        int $depth = 512
    ): string
    {
        $json = json_encode($value, $options, $depth);

        if (JSON_ERROR_NONE !== ($error = json_last_error())) {
            throw new JsonException(json_last_error_msg(), $error);
        }

        return $json;
    }

    /**
     * 对 JSON 格式的字符串进行解码
     *
     * @static
     *
     * @param string $json
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     *
     * @return mixed
     */
    public static function decode(
        string $json,
        bool $assoc = true,
        int $depth = 512,
        int $options = JSON_BIGINT_AS_STRING
    ) {
        $data = json_decode($json, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== ($error = json_last_error())) {
            throw new JsonException(json_last_error_msg(), $error);
        }

        return $data;
    }
}
