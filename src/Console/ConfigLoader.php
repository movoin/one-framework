<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Console
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Console;

use DirectoryIterator;
use One\Utility\Assert;
use One\Exception\RuntimeException;
use One\Utility\Helper\ArrayHelper;
use One\Utility\Helper\YamlHelper;

/**
 * 应用配置加载类
 *
 * @since 0.2
 */
class ConfigLoader
{
    /**
     * 运行模式定义
     */
    const RUN_MODES = ['local', 'devel', 'test', 'deploy'];

    /**
     * 应用根目录
     *
     * @var string
     */
    private $root;
    /**
     * 运行模式
     *
     * @var string
     */
    private $mode = 'local';
    /**
     * 配置项
     *
     * @var array
     */
    private $configs = [];
    /**
     * 路径占位符
     *
     * @var array
     */
    private $placeholders = [];

    /**
     * 创建配置类实例
     *
     * @param string $root
     * @param string $mode
     *
     * @return self
     */
    public static function make(string $root, string $mode = 'local'): self
    {
        return new static($root, $mode);
    }

    /**
     * 构造
     *
     * @param string $root
     * @param string $mode
     */
    public function __construct(string $root, string $mode = 'local')
    {
        $this->root = $root;
        $this->mode = $mode;

        $this->addPlaceholder('{ROOT_PATH}', $root);
        $this->addPlaceholder('{RUNTIME_PATH}', $root . '/runtime');
        $this->addPlaceholder('{CONFIG_PATH}', $root . '/runtime/config');
        $this->addPlaceholder('{LOG_PATH}', $root . '/runtime/log');
        $this->addPlaceholder('{VAR_PATH}', $root . '/runtime/var');

        $this->scan();
    }

    /**
     * 添加路径战友位符
     *
     * @param string $placeholder
     * @param string $value
     *
     * @return void
     */
    public function addPlaceholder(string $placeholder, string $value): void
    {
        $this->placeholders[trim($placeholder)] = trim($value);
    }

    /**
     * 获得服务配置项
     *
     * @return array
     */
    public function all(): array
    {
        return $this->transformPlaceholders($this->configs);
    }

    /**
     * 扫描配置文件
     *
     * @return void
     * @throws \One\Exception\RuntimeException
     */
    private function scan(): void
    {
        $excludes = ArrayHelper::where(static::RUN_MODES, function ($mode) {
            return $mode !== $this->mode;
        });

        $configs = $this->scanInPath($this->root . '/runtime/config', $excludes);

        if (! isset($configs[$this->mode])) {
            throw new RuntimeException([
                '未找到运行模式 {mode} 的配置文件' => [
                    'mode' => $this->mode
                ]
            ]);
        }

        if (! isset($configs[$this->mode]['server']) || count($configs[$this->mode]['server']) === 0) {
            throw new RuntimeException('没有找到任何服务配置');
        }

        $this->configs = $this->prefrom($configs);

        unset($excludes, $configs);
    }

    /**
     * 扫描指定目录的配置文件
     *
     * @param string $dir
     * @param array $excludes
     *
     * @return array
     */
    private function scanInPath(string $dir, array $excludes): array
    {
        $configs = [];
        $paths = new DirectoryIterator($dir);

        foreach ($paths as $path) {
            if ($path->isDot()) {
                continue;
            }

            if ($path->isDir() && ! in_array($path->getFileName(), $excludes)) {
                $configs[$path->getFileName()] = $this->scanInPath($path->getPathName(), $excludes);
            }

            if ($path->isFile()) {
                if ($path->getExtension() === 'yml') {
                    $name = pathinfo($path->getFilename(), PATHINFO_FILENAME);
                    $configs[$name] = YamlHelper::parseFile($path->getPathName(), []);

                    unset($name);
                }
            }
        }

        unset($paths);

        return $configs;
    }

    /**
     * 预处理
     *
     * @param array $configs
     *
     * @return array
     * @throws \One\Exception\RuntimeException
     */
    private function prefrom(array $configs): array
    {
        $prefromed = [];

        $_ = $configs[$this->mode];
        $servers = $_['server'];
        unset($_['server'], $configs[$this->mode]);

        $runtime = $this->root . '/runtime';

        foreach ($servers as $server => $config) {
            $prefromed[$server] = ArrayHelper::merge($_, $configs, $config);
            $prefromed[$server]['swoole']['log_file'] = $runtime . '/log/' . $server . '.error.log';
            $prefromed[$server]['swoole']['pid_file'] = $runtime . '/var/' . $server . '.pid';
        }

        unset($_, $servers, $runtime);

        return $prefromed;
    }

    /**
     * 转换路径占位符
     *
     * @param array $configs
     *
     * @return array
     */
    private function transformPlaceholders(array $configs): array
    {
        $data = [];

        foreach ($configs as $name => $value) {
            if (Assert::array($value)) {
                $data[$name] = $this->transformPlaceholders($value);
            } elseif (Assert::string($value) && preg_match('/{.*}/i', $value)) {
                $data[$name] = $value;
                foreach ($this->placeholders as $placeholder => $val) {
                    $data[$name] = str_replace($placeholder, $val, $data[$name]);
                }
            } else {
                $data[$name] = $value;
            }
        }

        return $data;
    }
}
