<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Server
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Server;

use One\Exception\LogicException;
use One\Server\Contract\ProviderInterface;
use One\Server\Contract\ServerInterface;

/**
 * 服务器组件提供抽象类
 *
 * @abstract
 * @since 0.2
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * 服务器实例
     *
     * @var \One\Server\Contract\ServerInterface
     */
    private $server;

    /**
     * 构造
     *
     * @param \One\Server\Contract\ServerInterface $server
     */
    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * 注册
     *
     * @return void
     */
    public function register(): void
    {
        throw new LogicException(sprintf('你必须重载组件提供器 %s 的 register() 方法', __CLASS__));
    }

    /**
     * 启动
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * 关闭
     *
     * @return void
     */
    public function shutdown(): void
    {
    }

    /**
     * 获得服务器实例
     *
     * @return \One\Server\Contract\ServerInterface
     */
    final protected function getServer(): ServerInterface
    {
        return $this->server;
    }

    /**
     * 获得指定实例
     *
     * @param string $name
     *
     * @return object
     */
    final protected function make(string $name): object
    {
        return $this->server->get($name);
    }

    /**
     * 绑定对象申明过程，对象将在调用时实例化
     *
     * @param string $id
     * @param mixed $concrete
     *
     * @return void
     * @throws \One\Collection\Exception\ContainerException
     */
    final protected function bind(string $id, $concrete = null): void
    {
        $this->server->bind($id, $concrete);
    }

    /**
     * 映射对象别名
     *
     * @param string $id
     * @param string $alias
     *
     * @return void
     */
    final protected function alias(string $id, string $alias): void
    {
        $this->server->alias($id, $alias);
    }
}
