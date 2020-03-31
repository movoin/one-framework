<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Encoder
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Encoder;

use One\Encoder\Exception\DecodeException;
use One\Encoder\Exception\EncodeException;

/**
 * Json 编码类
 *
 * @since 0.2
 */
class Json
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

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
     * @throws \One\Encoder\Exception\EncodeException
     */
    public static function encode(
        $value,
        int $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION,
        int $depth = 512
    ): string
    {
        $json = json_encode($value, $options, $depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new EncodeException('Json', json_last_error_msg());
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
     * @throws \One\Encoder\Exception\DecodeException
     */
    public static function decode(
        string $json,
        bool $assoc = true,
        int $depth = 512,
        int $options = JSON_BIGINT_AS_STRING
    ) {
        $data = json_decode($json, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new DecodeException('Json', json_last_error_msg());
        }

        return $data;
    }

    /**
     * 读取并解码 JSON 文件
     *
     * @param string $filepath
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     *
     * @return mixed
     * @throws \One\Encoder\Exception\DecodeException
     */
    public static function readFile(
        string $filepath,
        bool $assoc = true,
        int $depth = 512,
        int $options = JSON_BIGINT_AS_STRING
    ) {
        if (file_exists($filepath)) {
            $json = file_get_contents($filepath);

            return static::decode($json, $assoc, $depth, $options);
        }

        throw new DecodeException('Json', "未找到文件: $filepath");
    }
}
