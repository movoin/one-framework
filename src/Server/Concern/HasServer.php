<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Server\Concern
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Server\Concern;

use One\Server\Contract\ServerInterface;

trait HasServer
{
    /**
     * 服务器实例
     *
     * @var \One\Server\Contract\ServerInterface
     */
    private $server;

    /**
     * 设置服务器
     *
     * @param \One\Server\Contract\ServerInterface $server
     *
     * @return void
     */
    public function setServer(ServerInterface $server): void
    {
        $this->server = $server;
    }

    /**
     * 获得服务器
     *
     * @return \One\Server\Contract\ServerInterface
     */
    public function getServer(): ServerInterface
    {
        return $this->server;
    }
}
