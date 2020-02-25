<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Utility
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Utility;

use ArrayAccess;
use Countable;
use Traversable;

/**
 * 断言助手类
 *
 * @method bool string($value)
 * @method bool stringNotEmpty($value)
 * @method bool integer($value)
 * @method bool float($value)
 * @method bool numeric($value)
 * @method bool number($value)
 * @method bool natural($value)
 * @method bool boolean($value)
 * @method bool object($value)
 * @method bool datetime($value)
 * @method bool resource($value, $type = null)
 * @method bool callable($value)
 * @method bool array($value)
 * @method bool countable($value)
 * @method bool iterable($value)
 * @method bool instanceOf($value, $class)
 * @method bool instanceOfAny($value, array $classes)
 * @method bool namespace($value)
 * @method bool range($value, $min, $max)
 * @method bool oneOf($value, array $values)
 * @method bool contains($value, $needle)
 * @method bool startsWith(string $haystack, $needles)
 * @method bool ip($value)
 * @method bool email($value, $domains = null)
 * @method bool mobile($value)
 * @method bool phone($value)
 * @method bool url($value)
 * @method bool json($value)
 *
 * @static
 */
class Assert
{
    /**
     * 判断是否字符串
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function string($value): bool
    {
        return is_string($value);
    }

    /**
     * 判断是否非空字符串
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function stringNotEmpty($value): bool
    {
        return is_string($value) && ! empty($value);
    }

    /**
     * 判断是否整数
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function integer($value): bool
    {
        return is_int($value);
    }

    /**
     * 判断是否浮点数
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function float($value): bool
    {
        return is_float($value);
    }

    /**
     * 判断是否数值或数值和字符串
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function numeric($value): bool
    {
        return preg_match('/^\w+$/', (string) $value) ? true : false;
    }

    /**
     * 判断是否数值或数值字符串
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function number($value): bool
    {
        if ($value == (string) (float) $value) {
            return (bool) is_numeric($value);
        }

        return false;
    }

    /**
     * 判断是否正数
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function natural($value): bool
    {
        return is_int($value) && $value >= 0;
    }

    /**
     * 判断是否布尔值
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function boolean($value): bool
    {
        return is_bool($value);
    }

    /**
     * 判断是否对象类型
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function object($value): bool
    {
        return is_object($value);
    }

    /**
     * 判断是否日期类型
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function datetime($value): bool
    {
        if ($value instanceof \DateTime) {
            return true;
        } elseif (strtotime((string) $value) !== false) {
            return true;
        }

        return false;
    }

    /**
     * 判断是否资源类型
     *
     * @param  mixed  $value
     * @param  string $type
     *
     * @return bool
     */
    public static function resource($value, $type = null): bool
    {
        if ($type !== null) {
            return is_resource($value) && $type === get_resource_type($value);
        }

        return is_resource($value);
    }

    /**
     * 判断是否可调用
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function callable($value): bool
    {
        return is_callable($value);
    }

    /**
     * 判断是否数组
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function array($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * 判断是否可统计
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function countable($value): bool
    {
        return is_array($value) || $value instanceof Countable;
    }

    /**
     * 判断是否可迭代
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function iterable($value): bool
    {
        return is_array($value) || $value instanceof Traversable;
    }

    /**
     * 判断是否实现自指定类
     *
     * @param  mixed $value
     * @param  mixed $class
     *
     * @return bool
     */
    public static function instanceOf($value, $class): bool
    {
        return $value instanceof $class;
    }

    /**
     * 判断是否实现自指定类
     *
     * @param  mixed $value
     * @param  array $classes
     *
     * @return bool
     */
    public static function instanceOfAny($value, array $classes): bool
    {
        foreach ($classes as $class) {
            if ($value instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * 判断是否命名空间
     *
     * @param  string $value
     *
     * @return bool
     */
    public static function namespace($value): bool
    {
        return static::stringNotEmpty($value) && strpos(trim($value), '\\') !== false;
    }

    /**
     * 判断数值是否在范围内
     *
     * @param  int|float $value
     * @param  int|float $min
     * @param  int|float $max
     *
     * @return bool
     */
    public static function range($value, $min, $max): bool
    {
        return $value >= $min && $value <= $max;
    }

    /**
     * 判断是否在数组内
     *
     * @param  mixed $value
     * @param  array $values
     *
     * @return bool
     */
    public static function oneOf($value, array $values): bool
    {
        return in_array($value, $values, true);
    }

    /**
     * 判断是否包含指定内容
     *
     * @param  mixed  $value
     * @param  string $needle
     *
     * @return bool
     */
    public static function contains($value, $needle): bool
    {
        return strpos($value, $needle) !== false;
    }

    /**
     * 判断是否由指定字符开头
     *
     * @param  string       $haystack
     * @param  string|array $needles
     *
     * @return bool
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * 判断是否 IP
     *
     * @param  mixed  $value
     *
     * @return bool
     */
    public static function ip($value): bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_IP);
    }

    /**
     * 判断是否 E-Mail
     *
     * @param  mixed $value
     * @param  mixed $domains
     *
     * @return bool
     */
    public static function email($value, $domains = null): bool
    {
        $isEmail = (bool) filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($isEmail && $domains !== null) {
            list(, $host) = explode('@', $value);
            $domains = (array) $domains;

            if (in_array($host, $domains)) {
                return true;
            }

            return false;
        }

        return $isEmail;
    }

    /**
     * 判断是否手机号
     *
     * 支持: 13X, 14X, 15X, 16X, 17X, 18X, 19X 号段
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function mobile($value): bool
    {
        return (bool) preg_match(
            '/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(166)|(17([2-3]|[5-8]))|(18[0-9])|(19[1|3|8|9]))\\d{8}$/i',
            $value
        );
    }

    /**
     * 判断是否电话号码
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function phone($value): bool
    {
        return (bool) preg_match('/^(\d{4}-|\d{3}-)?(\d{8}|\d{7})$/', $value);
    }

    /**
     * 判断是否 URL
     *
     * @param  mixed  $value
     *
     * @return bool
     */
    public static function url($value): bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_URL);
    }

    /**
     * 判断是否 JSON
     *
     * @param  mixed  $value
     *
     * @return bool
     */
    public static function json($value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
