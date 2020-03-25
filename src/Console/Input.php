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

use One\Console\ConfigLoader;
use One\Collection\Collection;
use Symfony\Component\Console\Input\ArgvInput;

class Input extends ArgvInput
{
    /**
     * 应用配置
     *
     * @var \One\Colleciton\Collection
     */
    private $config;

    /**
     * 载入应用配置
     *
     * @param string $root
     * @param string $mode
     *
     * @return void
     */
    public function loadConfig(string $root, string $mode): void
    {
        $configs = ConfigLoader::make($root, $mode)->all();

        $this->config = new Collection($configs);

        unset($configs);
    }

    /**
     * 获得应用配置容器实例
     *
     * @return \One\Collection\Collection
     */
    public function getConfigCollection(): Collection
    {
        return $this->config;
    }

    /**
     * 读取配置
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getConfig(string $name, $default = null)
    {
        return $this->config->get($name, $default);
    }
}
