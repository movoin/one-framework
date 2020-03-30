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
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
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
     * 接收 Http 请求
     *
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     *
     * @return void
     */
    public function onRequest(SwooleHttpRequest $request, SwooleHttpResponse $response): void
    {
    }

    /**
     * 获得 Swoole Server
     *
     * @return \Swoole\Http\Server
     */
    protected function getSwooleServer(): SwooleHttpServer
    {
        if ($this->swoole === null) {
            $uri = $this->getListenUri();

            $this->swoole = new SwooleHttpServer(
                $uri['host'],
                $uri['port'],
                SWOOLE_PROCESS,
                SWOOLE_SOCK_TCP
            );

            unset($uri);

            $this->swoole->set($this->config->get('swoole'));
        }

        return $this->swoole;
    }
}
