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

use Swoole\Server;
use One\Collection\Container;
use One\Collection\Collection;
use One\Event\Concern\HasEvent;
use One\Server\Contract\ServerInterface;

/**
 * 服务器抽象类
 *
 * @abstract
 * @since 0.2
 */
abstract class AbstractServer extends Container implements ServerInterface
{
    use HasEvent;

    /**
     * 默认监听 IP
     */
    const DEFAULT_HOST = '0.0.0.0';
    /**
     * 默认监听端口
     */
    const DEFAULT_PORT = 9501;

    /**
     * 服务配置
     *
     * @var \One\Collection\Collection
     */
    protected $config;
    /**
     * 服务名称
     *
     * @var string
     */
    protected $serverName;
    /**
     * 核心组件
     *
     * @var array
     */
    protected $providers = [];

    /**
     * 构造
     *
     * @param string $serverName
     * @param array $config
     */
    public function __construct(string $serverName, array $config = [])
    {
        $this->serverName = strtolower(trim($serverName));
        $this->config = new Collection($config);

        $this->initialize();
    }

    /**
     * 启动服务器
     *
     * @return void
     */
    public function start(): void
    {
        if (false === $this->isRunning()) {
            $this->getEmitter()->emit('server.start', $this->config->all());

            $this
                ->getSwooleServer()
                ->bindSwooleEvents()
                ->bootProviders($this->getProviders())
                ->bootStartItems()
            ;

            $this->getSwooleServer()->start();
        }
    }

    /**
     * 关闭服务器
     *
     * @return void
     */
    public function shutdown(): void
    {
        if ($this->isRunning()) {
            $pid = $this->getPid();

            $this->getEmitter()->emit(
                'server.shutdown',
                [
                    'pid' => $pid,
                    'pid_file' => $this->config->get('swoole.pid_file')
                ]
            );

            $this
                ->shutdownStartItems()
                ->shutdownProviders($this->getProviders())
            ;

            posix_kill($pid, SIGTERM);

            unset($pid);
        }
    }

    /**
     * 获得服务是否运行
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $pid = $this->getPid();
        return $pid && posix_kill($pid, 0);
    }

    /**
     * 获得 Swoole Server
     *
     * @return \Swoole\Server
     */
    abstract protected function getSwooleServer(): Server;

    /**
     * 绑定 Swoole Server 事件
     *
     * @return self
     */
    protected function bindSwooleEvents(): self
    {
        $events = [
            // Common
            'Start', 'Shutdown', 'ManagerStart', 'WorkerStart', 'WorkerStop', 'WorkerError',
            'WorkerExit', 'PipeMessage', 'Connect', 'Close',
            // TCP
            'Receive',
            // UDP
            'Packet',
            // HTTP
            'Request',
            // WebSocket
            'Open', 'Message',
            // Async Tasks
            'Task', 'Finish'
        ];

        array_map($events, function ($event) {
            if (method_exists($this, "on{$event}")) {
                $this->getSwooleServer()->on($event, "on{$event}");
            }
        });

        unset($events);

        return $this;
    }

    /**
     * 初始化
     *
     * @return void
     */
    protected function initialize(): void
    {
        $this->getEmitter()->emit('server.init');

        // 注册核心组件
        $this->registerProviders([] + $this->providers);
    }

    /**
     * 获得监听 URI 数组，包含 host 及 port
     *
     * @return array
     */
    protected function getListenUri(): array
    {
        $uri = [];

        if (null !== ($listen = $this->config->get('listen'))) {
            $uri = parse_url($listen);
        }

        unset($listen);

        $uri['host'] = isset($uri['host']) ? $uri['host'] : static::DEFAULT_HOST;
        $uri['port'] = isset($uri['port']) ? $uri['port'] : static::DEFAULT_PORT;

        return $uri;
    }

    /**
     * 启动启动项
     *
     * @return self
     */
    protected function bootStartItems(): self
    {
        $startItems = $this->config->get('start_items', []);

        $this
            ->registerProviders($startItems)
            ->bootProviders($startItems)
        ;

        unset($startItems);

        return $this;
    }

    /**
     * 关停启动项
     *
     * @return self
     */
    protected function shutdownStartItems(): self
    {
        $this->shutdownProviders($this->config->get('start_items', []));

        return $this;
    }

    /**
     * 注册组件
     *
     * @param array $providers
     *
     * @return self
     */
    protected function registerProviders(array $providers = []): self
    {
        if ($providers !== []) {
            array_walk($providers, function ($id) {
                $provider = $this->make($id, [$this]);
                $provider->register();

                $this->getEmitter()->emit('server.register.provider', ['provider' => $id]);

                unset($provider);
            });
        }

        return $this;
    }

    /**
     * 启动组件
     *
     * @param array $providers
     *
     * @return self
     */
    protected function bootProviders(array $providers = []): self
    {
        if ($providers !== []) {
            array_walk($providers, function ($id) {
                $provider = $this->make($id, [$this]);
                $provider->boot();

                $this->getEmitter()->emit('server.boot.provider', ['provider' => $id]);

                unset($provider);
            });
        }

        return $this;
    }

    /**
     * 注销组件
     *
     * @param array $providers
     *
     * @return self
     */
    protected function shutdownProviders(array $providers = []): self
    {
        if ($providers !== []) {
            array_walk($providers, function ($id) {
                $provider = $this->make($id, [$this]);
                $provider->shutdown();

                $this->getEmitter()->emit('server.shutdown.provider', ['provider' => $id]);

                unset($provider);
            });
        }

        return $this;
    }

    /**
     * 获得主进程 PID
     *
     * @return int
     */
    protected function getPid(): int
    {
        $pid = 0;
        $pidFile = $this->config->get('swoole.pid_file');

        if (is_readable($pidFile)) {
            $pid = file_get_contents($pidFile);
        }

        unset($pidFile);

        return $pid;
    }

    /**
     * 设置进程名称
     *
     * @param string $name
     *
     * @return void
     */
    protected function setProcessName(string $name): void
    {
        swoole_set_process_name($name);
    }

    /**
     * 设置运行用户
     *
     * @param string|null $username
     *
     * @return void
     */
    protected function setRunUser(?string $username = null): void
    {
        if ($username !== null) {
            $user = posix_getpwnam($username);

            if ($user) {
                posix_setuid($user['uid']);
                posix_setgid($user['gid']);
            }

            unset($user);
        }
    }
}
