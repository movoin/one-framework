<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Server\Contract
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Server\Contract;

/**
 * 协议消息接口
 *
 * @since 0.2
 */
interface MessageInterface
{
    /**
     * 状态码
     *
     * @return int
     */
    public function getCode(): int;

    /**
     * 消息文本
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * 内容结果
     *
     * @return array
     */
    public function getResult(): array;
}
