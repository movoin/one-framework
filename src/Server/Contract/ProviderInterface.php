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

use One\Server\Contract\ServerInterface;

/**
 * 服务器组件提供器接口
 *
 * @since 0.2
 */
interface ProviderInterface
{
    /**
     * 构造
     *
     * @param \One\Server\Contract\ServerInterface $server
     */
    public function __construct(ServerInterface $server);

    /**
     * 注册
     *
     * @return void
     */
    public function register(): void;

    /**
     * 启动
     *
     * @return void
     */
    public function boot(): void;

    /**
     * 关闭
     *
     * @return void
     */
    public function shutdown(): void;
}
