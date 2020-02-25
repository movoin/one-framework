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

use Closure;
use stdClass;
use One\Collection\Contract\Arrayable;

/**
 * 数组助手类
 *
 * @method bool  has(array $array, $key)
 * @method mixed get(array $array, $key, $default = null)
 * @method array where(array $array, callable $callback)
 * @method array only(array $array, array $keys)
 * @method array getColumn(array $array, ...$keys)
 * @method array map(array $array, $from, $to, $group = null)
 * @method array group(array $array, $group)
 * @method array set(array &$array, $key, $value)
 * @method void  insert(array &$array, int $index, ...$insert)
 * @method mixed remove(array &$array, $key, $default = null)
 * @method array merge(array $array, array ...$arrays)
 * @method array wrap($value)
 * @method bool  isAssociative(array $array)
 * @method bool  isIndexed(array $array)
 * @method array toArray($object, $recursive = true)
 *
 * @static
 */
class ArrayHelper
{
    /**
     * 判断数组中是否存在指定键名
     *
     * 示例：
     *
     * ```
     * $array = [
     *     'foo' => 'bar',
     *     'zar' => [
     *         'tar' => 'war'
     *     ],
     *     '你好'
     * ];
     * ```
     *
     * ```
     * ArrayHelper::has($array, 'foo');
     * ```
     * > 输出：true
     *
     * ```
     * ArrayHelper::has($array, 'zar.tar');
     * ```
     * > 输出：true
     *
     * ```
     * ArrayHelper::has($array, 0);
     * ```
     * > 输出：true
     *
     * @static
     *
     * @param array $array
     * @param string|int $key
     *
     * @return bool
     */
    public static function has(array $array, $key): bool
    {
        if ($array === [] || null === $key) {
            return false;
        }

        if (isset($array[$key]) || array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * 获得数组中指定键名的值，支持闭包回调及 'foo.bar.tar' 的简便路径获得层级数组
     *
     * 示例：
     *
     * ```
     * $array = [
     *     'foo' => 'bar',
     *     'zar' => [
     *         'tar' => 'war'
     *     ],
     *     'std' => (new stdClass)->foo = 'bar',
     *     '你好'
     * ];
     * ```
     *
     * ```
     * ArrayHelper::get($array, 'bad', 'oops');
     * ```
     * > 输出: 'oops'
     *
     * ```
     * ArrayHelper::get($array, 0);
     * ```
     * > 输出: '你好'
     *
     * ```
     * ArrayHelper::get($array, 'foo');
     * ```
     * > 输出: 'bar'
     *
     * ```
     * ArrayHelper::get($array, 'zar.tar');
     * ```
     * > 输出: 'war'
     *
     * ```
     * ArrayHelper::get($array, 'std.foo');
     * ```
     * > 输出: 'bar'
     *
     * ```
     * ArrayHelper::get($array, function ($arr, $default) {
     *     return $arr->foo === 'bar' ? 'tar' : $default;
     * });
     * > 输出: 'tar'
     * ```
     *
     * @static
     *
     * @param array $array
     * @param \Closure|string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if ($array === [] || $key === null) {
            return $default;
        }

        if ($key instanceof Closure) {
            return $key($array, $default);
        }

        if (isset($array[$key]) || array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::get($array, substr($key, 0, $pos), $default);
            $key = (string) substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return  $array[$key];
        }

        return $default;
    }

    /**
     * 根据回调过滤数组
     *
     * 示例：
     *
     * ```
     * $array = [
     *     ['id' => 1, 'name' => 'foo', 'type' => 'hihao'],
     *     ['id' => 2, 'name' => 'bar', 'type' => 'hihao'],
     *     ['id' => 3, 'name' => 'zar', 'type' => 'hihao'],
     * ];
     * ```
     *
     * ```
     * ArrayHelper::where($array, function ($elm) {
     *     return $elm['name'] !== 'zar';
     * });
     * ```
     * > 输出：
     * ```
     * [
     *     ['id' => 1, 'name' => 'foo', 'type' => 'hihao'],
     *     ['id' => 2, 'name' => 'bar', 'type' => 'hihao'],
     * ]
     * ```
     *
     * @static
     *
     * @param array $array
     * @param callable $callback
     *
     * @return array
     */
    public static function where(array $array, callable $callback): array
    {
        if ($array === []) {
            return [];
        }

        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 仅获得指定键名的数据
     *
     * 示例：
     *
     * ```
     * $array = ['foo' => 'bar', 'zar' => 'tar', 'nihao' => 'konichiwa'];
     * ```
     *
     * ```
     * ArrayHelper::only($array, ['foo', 'nihao']);
     * ```
     * > 输出：['foo' => 'bar', 'nihao' => 'konichiwa']
     *
     * @static
     *
     * @param array $array
     * @param array $keys
     *
     * @return array
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * 获得指定键名的数据，并支持多种方式操作提高灵活性：
     *
     * ### 示例：
     *
     * ```
     * $array = [
     *     ['id' => 1, 'profile' => ['name' => 'foo'], 'updated' => '2020-02-17 17:48:25'],
     *     ['id' => 2, 'profile' => ['name' => 'bar'], 'updated' => '2020-02-17 17:48:25'],
     * ];
     * ```
     *
     * ```
     * ArrayHelper::getColumn($array, 'id');
     * ```
     * > 输出: [1, 2]
     *
     * ```
     * ArrayHelper::getColumn($array, 'profile.name');
     * ```
     * > 输出: ['foo', 'bar']
     *
     * ```
     * ArrayHelper::getColumn($array, function ($item) {
     *     return $item['id'] . ':' . $item['profile']['name'];
     * });
     * ```
     * > 输出: ['1:foo', '2:bar']
     *
     * ```
     * ArrayHelper::getColumn($array, 'id', 'profile.name', 'updated');
     * ```
     * > 输出: ['id' => 1, 'profile.name' => 'foo', 'updated' => '2020-02-17 17:48:25', ...]
     * ```
     *
     * @static
     *
     * @param array $array
     * @param \Closure|string|int ...$keys 不能为 null
     *
     * @return array
     */
    public static function getColumn(array $array, ...$keys): array
    {
        if ($array === [] || (count($keys) === 1 && $keys[0] === null)) {
            return [];
        }

        $result = [];
        $needKey = count($keys) > 1;

        if ($needKey) {
            foreach ($array as $item) {
                $elm = [];
                foreach ($keys as $key) {
                    $elm[$key] = static::get($item, $key);
                }
                $result[] = $elm;
                unset($elm);
            }
        } else {
            foreach ($array as $item) {
                $result[] = static::get($item, $keys[0]);
            }
        }
        unset($needKey);

        return $result;
    }

    /**
     * 将数组根据映射关系，映射为新的数组
     *
     * 示例：
     *
     * ```
     * $array = [
     *     ['id' => 1, 'name' => 'foo', 'type' => 'hihao'],
     *     ['id' => 2, 'name' => 'bar', 'type' => 'konichiwa'],
     *     ['id' => 3, 'name' => 'tar', 'type' => 'hihao'],
     *     ['id' => 4, 'name' => 'zar', 'type' => 'konichiwa'],
     * ];
     * ```
     *
     * ```
     * ArrayHelper::map($array, 'name', 'id');
     * ```
     * > 输出：['foo' => 1, 'bar' => 2, 'tar' => 3, 'zar' => 4]
     *
     * ```
     * ArrayHelper::map($array, 'id', 'name', 'type');
     * ```
     * > 输出：
     * ```
     * [
     *     'hihao' => [
     *         1 => 'foo',
     *         3 => 'tar'
     *     ],
     *     'konichiwa' => [
     *         2 => 'bar',
     *         4 => 'zar'
     *     ]
     * ]
     * ```
     *
     * @static
     *
     * @param array $array
     * @param \Closure|string $from
     * @param \Closure|string $to
     * @param \Closure|string $group
     *
     * @return array
     */
    public static function map(array $array, $from, $to, $group = null): array
    {
        if ($array === []) {
            return [];
        }

        $result = [];

        foreach ($array as $elm) {
            $key = static::get($elm, $from);
            $value = static::get($elm, $to);
            if ($group !== null) {
                $result[static::get($elm, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * 根据指定键名对数组进行分组
     *
     * 示例：
     *
     * ```
     * $array = [
     *     ['id' => 1, 'name' => 'foo'],
     *     ['id' => 2, 'name' => 'foo'],
     * ];
     * ```
     *
     * ```
     * ArrayHelper::group($array, 'name');
     * ```
     * > 输出：
     * ```
     * [
     *     'foo' => [
     *         ['id' => 1, 'name' => 'foo'],
     *         ['id' => 2, 'name' => 'foo'],
     *     ]
     * ]
     * ```
     *
     * @static
     *
     * @param array $array
     * @param \Closure|string $group
     *
     * @return array
     */
    public static function group(array $array, $group): array
    {
        if ($array === []) {
            return [];
        }

        $result = [];

        foreach ($array as $elm) {
            $result[(string) static::get($elm, $group)][] = $elm;
        }

        return $result;
    }

    /**
     * 设置数组内容，支持 'foo.bar' 形式操作，当键名相同时，则替换键值内容。
     *
     * 示例：
     *
     * ```
     * $array = [
     *     'foo' => [
     *         'bar' => 'foobar',
     *         'tar' => 'footar'
     *     ],
     *     'zar' => 'foozar'
     * ];
     * ```
     *
     * ```
     * ArrayHelper::set($array, 'zar', 'new');
     * ```
     * > 输出：['zar' => 'new']
     *
     * ```
     * ArrayHelper::set($array, 'foo.zar', 'new');
     * ```
     * > 输出：['zar' => 'new']
     *
     * ```
     * ArrayHelper::set($array, 'foo.tar', 'modifed');
     * ```
     * > 输出：['tar' => 'modifed']
     *
     * ```
     * ArrayHelper::set($array, null, 'new');
     * ```
     * > 输出：['new']
     *
     * @static
     *
     * @param array $array
     * @param string|int|null $key
     * @param mixed $value
     *
     * @return array
     */
    public static function set(array &$array, $key, $value): array
    {
        if (null === $key) {
            return $array = static::wrap($value);
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * 往数组的指定位置插入数据
     *
     * 示例：
     *
     * ```
     * $array = ['foo', 'bar', 'tar', 'zar'];
     * ```
     *
     * ```
     * ArrayHelper::insert($array, 2, 'war');
     * ```
     * > 结果：['foo', 'bar', 'war', 'tar', 'zar']
     *
     * ```
     * ArrayHelper::insert($array, 3, 'a', 'b', 'c');
     * ```
     * > 结果：['foo', 'bar', 'war', 'a', 'b', 'c', 'tar', 'zar']
     *
     * @static
     *
     * @param array $array
     * @param int $index
     * @param array ...$insert
     *
     * @return void
     */
    public static function insert(array &$array, int $index, ...$insert): void
    {
        $firstArray = array_splice($array, 0, $index);
        $array = array_merge($firstArray, $insert, $array);

        unset($firstArray);
    }

    /**
     * 从数组中删除指定键名，删除成功则返回该键名对应的键值，否则返回默认值
     *
     * 示例：
     *
     * ```
     * $array = [
     *     'foo' => 'bar',
     *     'zar' => [
     *         'tar' => 'war'
     *     ],
     *     '你好'
     * ];
     * ```
     *
     * ```
     * ArrayHelper::remove($array, 'foo');
     * ```
     * > 输出：'bar'
     *
     * ```
     * ArrayHelper::remove($array, 'zar.tar');
     * ```
     * > 输出：'war'
     *
     * ```
     * ArrayHelper::remove($array, 'bad', 'boom');
     * ```
     * > 输出：'boom'
     *
     * ```
     * ArrayHelper::remove($array, 0);
     * ```
     * > 输出：'你好'
     *
     * @static
     *
     * @param array $array
     * @param string|int $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function remove(array &$array, $key, $default = null)
    {
        if ($array === [] || $key === null) {
            return $default;
        }

        if ($key instanceof Closure) {
            return $key($array, $default);
        }

        if (static::has($array, $key)) {
            if (isset($array[$key]) || array_key_exists($key, $array)) {
                $value = $array[$key];
                unset($array[$key]);

                return $value;
            }

            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                }
            }

            $value = $array[array_shift($parts)];
            unset($array[array_shift($parts)], $parts);

            return $value;
        }

        return $default;
    }

    /**
     * 合并数组
     *
     * 当合并多个数组出现相同键名时，排在后面的数据将覆盖前面的数据；
     * 当数值键名相同时，重复的数据将插入到最末。
     *
     * 示例：
     *
     * ```
     * $array = [
     *     'foo' => 'bar',
     *     'zar' => [ 'tar', 'war' ],
     *     '你好'
     * ];
     * ```
     *
     * ```
     * ArrayHelper::merge($array, ['foo'], ['foo' => 'zar'], ['zar' => ['tar']]);
     * ```
     * > 输出：['foo' => 'zar', 'zar' => ['tar', 'war', 'tar'], '你好', 'foo']
     *
     * @static
     *
     * @param array $array
     * @param array $arrays...
     *
     * @return array
     */
    public static function merge(array $array, array ...$arrays): array
    {
        $merged = $array;

        while (! empty($arrays)) {
            $next = array_shift($arrays);

            foreach ($next as $key => $value) {
                if (is_int($key)) {
                    if (isset($merged[$key])) {
                        $merged[] = $value;
                    } else {
                        $merged[$key] = $value;
                    }
                } elseif (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = self::merge($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * 将内容包装为数组，如果内容本身为数组则直接返回，否则返回包装后的数组
     *
     * 示例：
     *
     * ```
     * ArrayHelper::wrap('string');
     * ```
     * > 输出：['string']
     *
     * ```
     * ArrayHelper::wrap(123);
     * ```
     * > 输出：[123]
     *
     * ```
     * ArrayHelper::wrap(['array']);
     * ```
     * > 输出：['array']
     *
     * @static
     *
     * @param mixed $value
     *
     * @return array
     */
    public static function wrap($value): array
    {
        if ($value === null) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    /**
     * 判断是否为键值数组 ['key' => 'value']
     *
     * @static
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssociative(array $array): bool
    {
        if ($array === []) {
            return false;
        }

        foreach ($array as $key => $value) {
            if (! is_string($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 判断是否为序列数组 [0, 1, 2...]
     *
     * @static
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isIndexed(array $array): bool
    {
        if ($array === []) {
            return true;
        }

        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * 转换为数组格式
     *
     * @static
     *
     * @param object|string|array $object
     * @param bool $recursive
     *
     * @return array
     */
    public static function toArray($object, $recursive = true): array
    {
        if ($object instanceof stdClass) {
            return (array) $object;
        }

        if (is_array($object)) {
            if ($recursive) {
                foreach ($object as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $object[$key] = static::toArray($value, $recursive);
                    }
                }
            }

            return $object;
        }

        if (is_object($object)) {
            if ($object instanceof Arrayable) {
                $result = $object->toArray();
            } else {
                $result = [];

                foreach ($object as $key => $value) {
                    $result[$key] = $value;
                }
            }

            return $recursive ? static::toArray($result, $recursive) : $result;
        }

        return [$object];
    }
}
