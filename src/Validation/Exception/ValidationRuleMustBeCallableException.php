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

use One\Exception\InvalidArgumentException;

/**
 * 规则必须可回调异常类 (1802)
 *
 * @since 0.2
 */
class ValidationRuleMustBeCallableException extends InvalidArgumentException
{
    /**
     * 代码: 1802
     *
     * @var int
     */
    protected $code = 1802;

    /**
     * 构造
     *
     * @param string $rule
     */
    public function __construct(string $rule)
    {
        parent::__construct(static::formatMessage('校验规则 "{rule}" 必须 "可回调".', [
            'rule' => $rule
        ]));
    }
}
