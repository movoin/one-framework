<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Server\Protocol\Http
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Server\Protocol\Http;

use Swoole\Http\Server as SwooleHttpServer;
use One\Server\AbstractServer;

/**
 * HTTP 服务器
 *
 * @since 0.2
 */
class Server extends AbstractServer
{
    /**
     * Swoole Server
     *
     * @var \Swoole\Http\Server
     */
    private $swoole;

    /**
     * 绑定 Swoole Server 事件
     *
     * @return self
     */
    protected function bindSwooleEvents(): self
    {
        return $this;
    }

    /**
     * 获得 Swoole Server
     *
     * @return \Swoole\Http\Server
     */
    protected function getSwooleServer(): SwooleHttpServer
    {
        if ($this->swoole === null) {
            // $this->swoole = new SwooleHttpServer(
            //     $config['host'],
            //     $config['port'],
            //     SWOOLE_PROCESS,
            //     SWOOLE_SOCK_TCP
            // );
        }

        return $this->swoole;
    }
}
