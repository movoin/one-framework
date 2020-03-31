<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Encoder\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Encoder\Exception;

use Throwable;
use One\Exception\RuntimeException;

/**
 * 编码异常类 (1301)
 *
 * @since 0.2
 */
class EncodeException extends RuntimeException
{
    /**
     * 代码: 1301
     *
     * @var int
     */
    protected $code = 1301;

    /**
     * 构造
     *
     * @param string $encoder
     * @param string $message
     * @param \Throwable $previous
     */
    public function __construct(string $encoder, string $message, Throwable $previous = null)
    {
        parent::__construct(static::formatMessage('"{$encoder}" 编码错误, {message}.', [
            'encoder' => $encoder,
            'message' => $message,
        ]));
    }
}
