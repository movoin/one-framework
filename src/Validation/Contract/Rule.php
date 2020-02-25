<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Validation\Contract
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Validation\Contract;

interface Rule
{
    /**
     * 获得规则名称、别名
     *
     * @return array
     */
    public static function getNames(): array;

    /**
     * 校验规则
     *
     * @param array $attributes 校验数据
     * @param string $name      校验规则名称
     * @param array $parameters 校验参数
     *
     * @return bool
     */
    public function validate(array $attributes, string $name, array $parameters = []): bool;
}
