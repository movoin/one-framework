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
use One\Exception\RuntimeException;

/**
 * 解析异常类
 *
 * @since 0.2
 */
class ParseException extends RuntimeException
{
    /**
     * 构造
     *
     * @param string $type
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(string $type, string $message = '', int $code = 0, Throwable $previous = null)
    {
        $message = $message === '' ?? '%s 解析失败';
        $message = strpos('%s', $message) === false ?? '解析 %s: ' . $message;

        parent::__construct(sprintf($message, $type), $code, $previous);
    }
}
