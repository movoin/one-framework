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
use One\Validation\Rule\AbstractRule;
use One\Validation\Exception\ValidationInvalidArgumentException;

/**
 * 规则：数组
 *
 * **规则名及别名**
 *
 * - `between`
 *
 * **配置**
 *
 * - `['name', 'between', 'min' => 0, 'max' => 10, 'on' => 'scenario', 'message' => '%s 不能这么写哦']`
 *
 * **配置参数**
 *
 * - `min`      最小值
 * - `max`      最大值
 * - `on`       应用场景，支持字符串及数组两种形式定义多个场景，如：'create, update' 或 ['create', 'update']
 * - `except`   排除场景，同上
 * - `message`  验证失败消息，格式：'%s 不能为空'，详细请参考 `One\Validation\Rule\AbstractRule::addError()`
 *
 * > `on` 与 `except` 互斥，请勿同时设置
 *
 * @since 0.2
 */
class BetweenRule extends AbstractRule
{
    /**
     * 获得规则名称、别名
     *
     * @return array
     */
    public static function getNames(): array
    {
        return ['between'];
    }

    /**
     * 校验规则
     *
     * @param array $attributes 校验数据
     * @param string $name 校验规则名称
     * @param array $parameters 校验参数
     *
     * @return bool
     * @throws \One\Validation\Exception\ValidationInvalidArgumentException
     */
    public function validate(array $attributes, string $name, array $parameters = []): bool
    {
        if (! isset($parameters['min'])) {
            throw new ValidationInvalidArgumentException(static::getNames(), 'min');
        }

        if (! isset($parameters['max'])) {
            throw new ValidationInvalidArgumentException(static::getNames(), 'max');
        }

        if (Assert::range($attributes[$name], $parameters['min'], $parameters['max'])) {
            return true;
        }

        $this->addError(
            $name,
            $parameters,
            "%s 必须于 {$parameters['min']} 与 {$parameters['max']} 之间"
        );

        return false;
    }
}
