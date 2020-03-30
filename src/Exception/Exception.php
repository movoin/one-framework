<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Exception;

/**
 * 异常基类
 *
 * @since 0.2
 */
class Exception extends \Exception
{
    /**
     * 未知类型
     */
    const UNKNOW = 0;
    /**
     * 应用层
     */
    const APPLICATION = 10;
    /**
     * 逻辑层
     */
    const LOGIC = 20;
    /**
     * 运行时
     */
    const RUNTIME = 30;
    /**
     * 错误
     */
    const ERROR = 40;

    /**
     * 异常类型
     *
     * @var int
     */
    protected $type = self::UNKNOW;

    /**
     * 获得异常类型
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * 格式化异常消息
     *
     * @param string $format
     * @param array $attributes
     *
     * @return string
     */
    public static function formatMessage(string $format, array $attributes = []): string
    {
        if ($attributes === []) {
            return $format;
        }

        $message = $format;

        foreach ($attributes as $name => $value) {
            $message = str_replace("{{$name}}", $value, $message);
        }

        return $message;
    }
}
