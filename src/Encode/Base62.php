<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Encode
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Encode;

use One\Encode\Exception\EncodeException;

/**
 * Base62 编码类
 *
 * @since 0.2
 */
class Base62
{
    const TYPE_INT = 0;
    const TYPE_STRING = 1;
    /**
     * Base62 字符串
     */
    const BASE62 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * 根据数据类型编码 Base62
     *
     * @static
     *
     * @param string|int $data
     *
     * @return string
     * @throws \One\Encode\Exception\EncodeException
     */
    public static function encode($data): string
    {
        if (is_int($data)) {
            return static::encodeInteger($data);
        }

        if (is_string($data)) {
            return static::encodeString($data);
        }

        throw new EncodeException('Base62', '编码仅支持 `string` 和 `Integer` 类型的数据');
    }
    /**
     * 根据数据类型解码 Base62
     *
     * @static
     *
     * @param string $data
     * @param int $type TYPE_STRING, TYPE_INT
     *
     * @return string|int
     * @throws \One\Encode\Exception\EncodeException
     */
    public static function decode(string $data, int $type = Base62::TYPE_STRING)
    {
        if ($type === static::TYPE_INT) {
            return static::decodeInteger($data);
        }

        if ($type === static::TYPE_STRING) {
            return static::decodeString($data);
        }

        throw new EncodeException('Base62', '编码仅支持 `string` 和 `Integer` 类型的数据');
    }

    /**
     * 编码 Base62 字符串
     *
     * @static
     *
     * @param string $data
     *
     * @return string
     * @throws \One\Encode\Exception\EncodeException
     */
    public static function encodeString(string $data): string
    {
        static::checkGMP();

        $hex = bin2hex($data);

        $leadZeroBytes = 0;
        while ('' !== $hex && 0 === strpos($hex, '00')) {
            $leadZeroBytes++;
            $hex = substr($hex, 2);
        }

        if ('' === $hex) {
            $base62 = str_repeat(static::BASE62[0], $leadZeroBytes);
        } else {
            $base62 = str_repeat(static::BASE62[0], $leadZeroBytes) . gmp_strval(gmp_init($hex, 16), 62);
        }

        unset($leadZeroBytes, $hex);

        return $base62;
    }

    /**
     * 解码 Base62 字符串
     *
     * @static
     *
     * @param string $data
     *
     * @return string
     * @throws \One\Encode\Exception\EncodeException
     */
    public static function decodeString(string $data): string
    {
        static::checkGMP();
        static::validate($data);

        $leadZeroBytes = 0;
        while ('' !== $data && 0 === strpos($data, static::BASE62[0])) {
            $leadZeroBytes++;
            $data = substr($data, 1);
        }

        if ('' === $data) {
            return str_repeat('\x00', $leadZeroBytes);
        }

        $hex = gmp_strval(gmp_init($data, 62), 16);
        if (strlen($hex) % 2) {
            $hex = '0' . $hex;
        }

        return (string) hex2bin(str_repeat('00', $leadZeroBytes) . $hex);
    }

    /**
     * 编码 Base62 数值
     *
     * @static
     *
     * @param int $data
     *
     * @return string
     * @throws \One\Encode\Exception\EncodeException
     */
    public static function encodeInteger(int $data): string
    {
        static::checkGMP();
        return gmp_strval(gmp_init($data, 10), 62);
    }

    /**
     * 解码 Base62 数值
     *
     * @static
     *
     * @param string $data
     *
     * @return int
     */
    public static function decodeInteger(string $data): int
    {
        static::checkGMP();
        static::validate($data);

        $hex = gmp_strval(gmp_init($data, 62), 16);
        if (strlen($hex) % 2) {
            $hex = '0' . $hex;
        }

        return (int) hexdec($hex);
    }

    /**
     * 检查 GMP 扩展
     *
     * @static
     *
     * @return void
     * @throws \One\Encode\Exception\EncodeException
     */
    private static function checkGMP(): void
    {
        if (! function_exists('gmp_init')) {
            // @codeCoverageIgnoreStart
            throw new EncodeException('Base62', '未检测到 GMP 扩展，可以通过添加 `--with-gmp` PHP 编译参数来开启扩展');
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * 校验 Base62
     *
     * @static
     *
     * @param string $data
     *
     * @return void
     * @throws \One\Encode\Exception\EncodeException
     */
    private static function validate(string $data): void
    {
        if (strlen($data) !== strspn($data, static::BASE62)) {
            throw new EncodeException('Base62', '数据包含无效字符');
        }
    }
}
