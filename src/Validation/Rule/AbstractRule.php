<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Validation\Rule
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Validation\Rule;

use One\Utility\Assert;
use One\Validation\Validator;
use One\Validation\Contract\Rule;

/**
 * 校验规则抽象类
 *
 * @abstract
 * @since 0.2
 */
abstract class AbstractRule implements Rule
{
    /**
     * 是否忽略未定义字段，默认为 true
     *
     * @var bool
     */
    protected $ignoreUndefined = true;

    /**
     * 规则校验器实例
     *
     * @var \One\Validation\Validator
     */
    private $validator;

    /**
     * 构造
     *
     * @param \One\Validation\Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    // @codeCoverageIgnoreStart
    /**
     * 获得规则名称、别名
     *
     * @static
     *
     * @return array
     */
    public static function getNames(): array
    {
        return [];
    }
    // @codeCoverageIgnoreEnd

    /**
     * 执行校验规则
     *
     * @param array $attributes 校验数据
     * @param string $name      校验规则名称
     * @param array $parameters 校验参数
     *
     * @return bool
     */
    public function __invoke(array $attributes, string $name, array $parameters = []): bool
    {
        if ($this->ignoreUndefined && ! array_key_exists($name, $attributes)) {
            return true;
        }

        return $this->validate($attributes, $name, $parameters);
    }

    /**
     * 校验规则
     *
     * @param array $attributes 校验数据
     * @param string $name      校验规则名称
     * @param array $parameters 校验参数
     *
     * @abstract
     *
     * @return bool
     */
    abstract public function validate(array $attributes, string $name, array $parameters = []): bool;

    /**
     * 写入错误信息
     *
     * @param string $name
     * @param array $parameters
     * @param string $message
     *
     * @return void
     */
    protected function addError(string $name, array $parameters = [], string $message = ''): void
    {
        if (array_key_exists('message', $parameters)
            && Assert::stringNotEmpty($parameters['message'])
        ) {
            $message = trim($parameters['message']);
        }

        $this->validator->addError(sprintf($message, $name));
    }
}
