<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Validation\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Validation\Exception;

use Throwable;
use One\Exception\RuntimeException;

/**
 * 数据校验异常类
 *
 * @since 0.2
 */
class ValidationException extends RuntimeException
{
    /**
     * 构造
     *
     * @param string $rule
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(string $rule, string $message = '', int $code = 0, Throwable $previous = null)
    {
        $message = $message === '' ?? '%s 校验失败';
        $message = strpos('%s', $message) === false ?? '校验规则 %s: ' . $message;

        parent::__construct(sprintf($message, $rule), $code, $previous);
    }
}
