<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Annotation\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Annotation\Exception;

use Throwable;
use One\Utility\Assert;
use One\Exception\NotFoundException;

/**
 * 注释解析错误异常类 (101 ~ 104)
 *
 * @since 0.2
 */
class AnnotationTargetNotFoundException extends NotFoundException
{
    const CODE_FUNC = 101;
    const CODE_CLASS = 102;
    const CODE_PROPERTY = 103;
    const CODE_METHOD = 104;

    /**
     * 函数未找到 (1001)
     *
     * @param mixed $value
     * @param \Throwable $previous
     *
     * @return self
     */
    public static function functionNotFound($value, Throwable $previous = null): self
    {
        $message = static::formatMessage(
            '目标函数 "{value}" 未找到.',
            [
                'value' => Assert::string($value) ? $value : '[Closure]'
            ]
        );

        return new static($message, static::CODE_FUNC, $previous);
    }

    /**
     * 类未找到 (1002)
     *
     * @param mixed $value
     * @param \Throwable $previous
     *
     * @return self
     */
    public static function classNotFound($value, Throwable $previous = null): self
    {
        $message = static::formatMessage(
            '目标类 "{value}" 未找到.',
            [
                'value' => Assert::string($value) ? $value : '[object]'
            ]
        );

        return new static($message, static::CODE_CLASS, $previous);
    }

    /**
     * 类属性未找到 (1003)
     *
     * @param mixed $class
     * @param \Throwable $previous
     *
     * @return self
     */
    public static function propertyNotFound($class, string $property, Throwable $previous = null): self
    {
        $message = static::formatMessage(
            '目标类 "{class}" 中, 未找到 "{property}" 属性, 或目标类 "{class}" 未找到.',
            [
                'class' => Assert::string($class) ? $class : '[object]',
                'property' => $property
            ]
        );

        return new static($message, static::CODE_PROPERTY, $previous);
    }

    /**
     * 类方法未找到 (1004)
     *
     * @param mixed $class
     * @param \Throwable $previous
     *
     * @return self
     */
    public static function methodNotFound($class, string $method, Throwable $previous = null): self
    {
        $message = static::formatMessage(
            '目标类 "{class}" 中, 未找到 "{method}" 方法, 或目标类 "{class}" 未找到.',
            [
                'class' => Assert::string($class) ? $class : '[object]',
                'method' => $method
            ]
        );

        return new static($message, static::CODE_METHOD, $previous);
    }
}
