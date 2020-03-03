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

use One\Validation\Validator;
use One\Validation\Rule\AbstractRule;

/**
 * 规则：自定义
 *
 * @final
 */
final class CustomRule extends AbstractRule
{
    /**
     * 回调
     *
     * @var callable
     */
    protected $callback;

    /**
     * 从数组回调创建自定义规则
     *
     * @param \One\Validation\Validator $validator
     * @param mixed $callback
     *
     * @return self
     */
    public static function createFromArray(Validator $validator, $callback): self
    {
        $self = new static($validator);
        $self->callback = function (array $attributes, string $name, array $parameters) use ($callback) {
            return call_user_func_array($callback, [$attributes, $name, $parameters]);
        };

        return $self;
    }

    /**
     * 从闭包回调创建自定义规则
     *
     * @param \One\Validation\Validator $validator
     * @param callable $callback
     *
     * @return self
     */
    public static function createFromClosure(Validator $validator, callable $callback): self
    {
        $self = new static($validator);
        $self->callback = function (array $attributes, string $name, array $parameters) use ($callback) {
            return $callback($attributes, $name, $parameters);
        };

        return $self;
    }

    /**
     * 校验规则
     *
     * @param array $attributes 校验数据
     * @param string $name      校验规则名称
     * @param array $parameters 校验参数
     *
     * @return bool
     */
    public function validate(array $attributes, string $name, array $parameters = []): bool
    {
        $callback = $this->callback;

        if (($result = $callback($attributes, $name, $parameters)) === false) {
            $this->addError($name, $parameters, 'verification failed');
        }

        unset($callback);

        return $result;
    }
}
