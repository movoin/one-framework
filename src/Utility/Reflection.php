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

use ReflectionClass;

/**
 * 反射助手
 */
class Reflection
{
    /**
     * 创建实例
     *
     * @static
     *
     * @param mixed $abstract
     * @param array $args
     *
     * @return object
     */
    public static function newInstance($abstract, array $args = []): object
    {
        return (new ReflectionClass($abstract))->newInstanceArgs($args);
    }
}
