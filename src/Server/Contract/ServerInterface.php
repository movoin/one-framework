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
 * 服务器接口
 *
 * @since 0.2
 */
interface ServerInterface
{
    /**
     * 启动服务器
     *
     * @return void
     */
    public function start(): void;

    /**
     * 关闭服务器
     *
     * @return void
     */
    public function shutdown(): void;

    /**
     * 关闭服务器
     *
     * @return bool
     */
    public function isRunning(): bool;
}
