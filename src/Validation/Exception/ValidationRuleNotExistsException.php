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
 * 校验规则不存在异常类 (1803)
 *
 * @since 0.2
 */
class ValidationRuleNotExistsException extends InvalidArgumentException
{
    /**
     * 代码: 1803
     *
     * @var int
     */
    protected $code = 1803;

    /**
     * 构造
     *
     * @param string $rule
     */
    public function __construct(string $rule)
    {
        parent::__construct(static::formatMessage('校验规则 "{rule}" 不存在.', [
            'rule' => $rule
        ]));
    }
}
