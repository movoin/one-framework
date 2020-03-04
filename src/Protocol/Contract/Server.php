<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Contract
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Protocol\Contract;

/**
 * 服务器接口
 *
 * @since 0.2
 */
interface Server
{
    /**
     * 运行服务器
     *
     * @return void
     */
    public function run(): void;

    /**
     * 重载服务器配置
     *
     * @return void
     */
    public function reload(): void;

    /**
     * 关闭服务器
     *
     * @return void
     */
    public function shutdown(): void;
}
