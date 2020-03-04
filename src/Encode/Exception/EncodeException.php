<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Encode\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Encode\Exception;

use Throwable;
use One\Exception\RuntimeException;

/**
 * 编码异常类
 *
 * @since 0.2
 */
class EncodeException extends RuntimeException
{
    /**
     * 编码器名称
     *
     * @var string
     */
    protected $encoder;

    /**
     * 构造
     *
     * @param string $encoder
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(string $encoder, string $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->encoder = $encoder;

        $message = $message === '' ?
            strtoupper($encoder) . ': 发生异常' :
            strtoupper($encoder) . ': ' . $message;

        parent::__construct($message, $code, $previous);
    }

    /**
     * 获得编码器名称
     *
     * @return string
     */
    public function getEncoder(): string
    {
        return $this->encoder;
    }
}
