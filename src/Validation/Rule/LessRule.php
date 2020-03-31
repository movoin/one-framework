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

use One\Validation\Rule\AbstractRule;
use One\Validation\Exception\ValidationInvalidArgumentException;

/**
 * 规则：小于等于
 *
 * **规则名及别名**
 *
 * - `<`
 * - `less`
 *
 * **配置**
 *
 * - `['name', '<', 'than' => 10, 'on' => 'scenario', 'message' => '%s 不能这么写哦']`
 *
 * **配置参数**
 *
 * - `than`     比较对象
 * - `on`       应用场景，支持字符串及数组两种形式定义多个场景，如：'create, update' 或 ['create', 'update']
 * - `except`   排除场景，同上
 * - `message`  验证失败消息，格式：'%s 不能为空'，详细请参考 `One\Validation\Rule\AbstractRule::addError()`
 *
 * > `on` 与 `except` 互斥，请勿同时设置
 *
 * @since 0.2
 */
class LessRule extends AbstractRule
{
    /**
     * 获得规则名称、别名
     *
     * @return array
     */
    public static function getNames(): array
    {
        return ['<', 'less'];
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
        if (! isset($parameters['than'])) {
            throw new ValidationInvalidArgumentException(static::getNames(), 'than');
        }

        if ($attributes[$name] <= $parameters['than']) {
            return true;
        }

        $this->addError($name, $parameters, "%s 必须小于等于 {$parameters['than']}");

        return false;
    }
}
