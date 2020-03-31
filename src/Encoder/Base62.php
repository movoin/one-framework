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

use One\Exception\RuntimeException;
use One\Encoder\Exception\DecodeException;

/**
 * Base62 编码类
 *
 * @since 0.2
 */
class Base62
{
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
     * @param string $data
     *
     * @return string
     * @throws \One\Exception\RuntimeException
     */
    public static function encode(string $data): string
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
     * 根据数据类型解码 Base62
     *
     * @static
     *
     * @param string $data
     *
     * @return string
     * @throws \One\Exception\RuntimeException
     * @throws \One\Encoder\Exception\DecodeException
     */
    public static function decode(string $data)
    {
        static::checkGMP();

        if (strlen($data) !== strspn($data, static::BASE62)) {
            throw new DecodeException('Base62', '数据包含无效字符');
        }

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
     * 检查 GMP 扩展
     *
     * @static
     *
     * @return void
     * @throws \One\Exception\RuntimeException
     */
    private static function checkGMP(): void
    {
        if (! function_exists('gmp_init')) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('未检测到 GMP 扩展，可以通过添加 `--with-gmp` PHP 编译参数来开启扩展.');
            // @codeCoverageIgnoreEnd
        }
    }
}
