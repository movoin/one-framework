<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One;

use One\Utility\Assert;
use One\Utility\Helper\ArrayHelper;
use One\Utility\Helper\YamlHelper;
use One\Exception\RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

/**
 * 配置类
 *
 * @since 0.2
 */
class Config
{
    /**
     * 运行模式
     */
    const RUN_MODES = ['test', 'local', 'devel', 'deploy'];

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
     * 应用模式配置
     *
     * @var string
     */
    private $mode = 'local';
    /**
     * 配置文件根目录
     *
     * @var string
     */
    private $path;

    /**
     * 构造
     *
     * @param string $path
     * @param string $mode
     *
     * @throws \One\Exception\RuntimeException
     */
    public function __construct(string $path = null, string $mode = 'local')
    {
        array_map(function ($placeholder) {
            if (defined($placeholder)) {
                $this->addPlaceholder("{{$placeholder}}", constant($placeholder));
            }
        }, ['ROOT_PATH']);

        $this->path = $path;
        $this->mode = $mode;

        $this->load();
    }

    /**
     * 读取指定配置
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return ArrayHelper::get($this->configs, $name, $default);
    }

    /**
     * 获得全部配置
     *
     * @return array
     */
    public function all(): array
    {
        return $this->configs;
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
     * 载入配置
     *
     * @param bool $force
     *
     * @return void
     */
    public function load(bool $force = false): void
    {
        if ($force === false && ! empty($this->configs)) {
            return;
        }

        // 扫描配置文件
        $configs = $this->scan($this->path);
        // 处理配置
        $this->configs = $this->prefrom($configs);

        unset($configs);
    }

    /**
     * 重新载入配置
     *
     * @return void
     */
    public function reload(): void
    {
        $this->load(true);
    }

    /**
     * 扫描配置目录
     *
     * @param string $path
     *
     * @return array
     * @throws \One\Exception\RuntimeException
     */
    public function scan(string $path): array
    {
        $exclude = array_filter(static::RUN_MODES, function ($mode) {
            return $mode !== $this->mode;
        }, ARRAY_FILTER_USE_BOTH);

        try {
            $finder = new Finder;
            $finder->files()
                ->ignoreVCS(true)
                ->name('*.yml')
                ->exclude($exclude)
                ->in($path)
            ;
        } catch (DirectoryNotFoundException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        unset($exclude);

        $data = [];
        foreach ($finder as $file) {
            $data[$file->getFilenameWithoutExtension()] = YamlHelper::parseFile($file->getPathName(), []);
        }

        unset($finder);

        return $data;
    }

    /**
     * 处理数据
     *
     * @param array $configs
     *
     * @return array
     */
    private function prefrom(array $configs): array
    {
        if (isset($configs['protocol']) && ! empty($configs['protocol'])) {
            foreach ($configs['protocol'] as $name => &$protocol) {
                // Swoole 配置
                $protocol['swoole'] = isset($protocol['swoole']) ?
                    array_merge($configs['swoole'], $protocol['swoole']) :
                    $configs['swoole'];
                // 协议
                $protocol['protocol'] = isset($protocol['protocol']) ? strtolower($protocol['protocol']) : 'http';
                // 运行时
                if (! isset($protocol['runtime_path'])) {
                    $protocol['runtime_path'] = $configs['app']['runtime_path'];
                }
                // Swoole 日志
                if (! isset($protocol['swoole']['log_file'])) {
                    $protocol['swoole']['log_file'] = $protocol['runtime_path'] . '/log/' . $name . '.error.log';
                }
                // TCP 监听
                if (! isset($protocol['sock'])) {
                    $protocol['sock'] = $protocol['runtime_path'] . '/var/' . $name . '.sock';
                }
                // 主进程 PID 文件位置
                $protocol['swoole']['pid_file'] = $protocol['runtime_path'] . '/var/' . $name . '.pid';
            }

            unset($configs['swoole']);
        }

        return $this->transformPlaceholders($configs);
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
