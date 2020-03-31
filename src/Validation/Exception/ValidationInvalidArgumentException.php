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
 * 校验规则参数异常类 (1800)
 *
 * @since 0.2
 */
class ValidationInvalidArgumentException extends InvalidArgumentException
{
    /**
     * 代码: 1800
     *
     * @var int
     */
    protected $code = 1800;

    /**
     * 构造
     *
     * @param array $rule
     * @param string $argument
     * @param string $invalid
     */
    public function __construct(array $rule, string $argument, string $invalid = '未设置')
    {
        parent::__construct(static::formatMessage('校验规则 "{rule}" 参数 "{argument}" {invalid}.', [
            'rule' => implode(', ', $rule),
            'argument' => $argument,
            'invalid' => $invalid
        ]));
    }
}
