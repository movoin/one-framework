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
use One\Filesystem\Finder;
use One\Filesystem\Iterator\FileInfo;
use One\Utility\Encode\Json;
use One\Utility\Helper\ArrayHelper;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class Application extends SymfonyApplication
{
    /**
     * 命令行工具版本
     */
    const VERSION = '0.2.1';
    /**
     * 默认应用根目录
     */
    const DEFAULT_ROOT_PATH = ROOT_PATH;
    /**
     * 默认运行模式
     */
    const DEFAULT_RUN_MODE = 'local';

    /**
     * 应用配置
     *
     * @var array
     */
    private $config;
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
    private $mode;

    /**
     * LOGO
     *
     * @var string
     */
    private static $logo = '  ____             _____                   __      ______          __
 / __ \___  ___   / ___/__  ___  ___ ___  / /__   /_  __/__  ___  / /
/ /_/ / _ \/ -_) / /__/ _ \/ _ \(_-</ _ \/ / -_)   / / / _ \/ _ \/ /
\____/_//_/\__/  \___/\___/_//_/___/\___/_/\__/   /_/  \___/\___/_/

';

    /**
     * 构造
     */
    public function __construct()
    {
        parent::__construct('One Console Tool', Application::VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null): int
    {
        if (null === $output) {
            $formatter = new OutputFormatter(false,[
                'highlight' => new OutputFormatterStyle('red'),
                'warning' => new OutputFormatterStyle('black', 'yellow'),
            ]);

            $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
        }

        return parent::run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function getHelp(): string
    {
        return static::$logo . parent::getHelp();
    }

    /**
     * {@inheritDoc}
     */
    public function getLongVersion(): string
    {
        return sprintf(
            "<info>%s</info> version <comment>%s</comment> (Run in <info>%s</info> mode)",
            $this->getName(),
            $this->getVersion(),
            $this->mode
        );
    }

    /**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        $input = parent::getDefaultInputDefinition();
        $input->addOptions([
            new InputOption(
                'root',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Define one project root directory path',
                ROOT_PATH
            ),
            new InputOption(
                'mode',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Define one project running mode',
                'local'
            )
        ]);

        return $input;
    }

    /**
     * Configures the input and output instances based on the user arguments and options.
     */
    protected function configureIO(InputInterface $input, OutputInterface $output)
    {
        $this->root = realpath($input->getParameterOption(['--root', '-r'], static::DEFAULT_ROOT_PATH));
        $this->mode = $input->getParameterOption(['--mode', '-m'], static::DEFAULT_RUN_MODE);

        $input->loadConfig($this->root, $this->mode);

        parent::configureIO($input, $output);
    }

    /**
     * 获得所有命令
     */
    protected function getDefaultCommands(): array
    {
        $commands = [];

        $finder = new Finder;
        $finder
            ->files()
            ->name('/Command\.php$/')
            ->in(dirname(__DIR__) . '/**/Command')
        ;

        $namespaces = $this->getOneNamespace();

        foreach ($finder as $file) {
            $className = $this->getCommandClassName($namespaces, $file);
            $commands[] = new $className();
        }

        $commands = array_merge(parent::getDefaultCommands(), $commands, $this->getAppCommands());

        return $commands;
    }

    /**
     * 获得应用级命令
     *
     * @return array
     */
    protected function getAppCommands(): array
    {
        $commands = [];

        $finder = new Finder;
        $finder
            ->files()
            ->name('/Command\.php$/')
            ->exclude(['vendor', 'tests', 'docs'])
            ->in($this->root)
        ;

        $namespaces = $this->getAppNamespace();

        foreach ($finder as $file) {
            $className = $this->getCommandClassName($namespaces, $file);
            $commands[] = new $className();
        }

        return $commands;
    }

    /**
     * 获得命令完整类名
     *
     * @param array $namespaces
     * @param \One\Filesystem\Iterator\FileInfo $file
     *
     * @return string
     */
    private function getCommandClassName(array $namespaces, FileInfo $file): string
    {
        $filepath = $file->getPathInfo()->getRealPath();

        foreach ($namespaces as $root => $path) {
            if (false !== strpos($filepath, $path)) {
                $namespace =
                    $root .
                    str_replace($path, '', $filepath) .
                    '/' .
                    $file->getFilenameWithoutExtension()
                ;
            }
        }

        return str_replace('/', '\\', $namespace);
    }

    /**
     * 获得 One 命名空
     *
     * @return array
     */
    private function getOneNamespace(): array
    {
        $composer = Json::readFile(dirname(dirname(__DIR__)) . '/composer.json');
        $namespaces = ArrayHelper::get($composer, 'autoload.psr-4', []);

        foreach ($namespaces as $namespace => $path) {
            $namespaces[$namespace] = dirname(dirname(__DIR__)) . '/' . $path;
        }

        return $namespaces;
    }

    /**
     * 获得应用命名空
     *
     * @return array
     */
    private function getAppNamespace(): array
    {
        $composer = Json::readFile($this->root . '/composer.json');
        $namespaces = ArrayHelper::get($composer, 'autoload.psr-4', []);

        foreach ($namespaces as $namespace => $path) {
            $namespaces[$namespace] = $this->root . '/' . $path;
        }

        return $namespaces;
    }
}
