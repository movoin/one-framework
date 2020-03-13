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

use Throwable;

/**
 * 运行时异常类
 *
 * @since 0.2
 */
class RuntimeException extends \RuntimeException
{
    /**
     * 构造
     *
     * @param string|array $message
     * @param int $code
     * @param Throwable $previous
     */
    public function __construct($message = '', int $code = 0, Throwable $previous = null)
    {
        if (is_array($message)) {
            $message = $this->formatMessage(key($message), $message[key($message)]);
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * 格式化异常消息
     *
     * @param string $format
     * @param array $attributes
     *
     * @return string
     */
    protected function formatMessage(string $format, array $attributes = []): string
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
