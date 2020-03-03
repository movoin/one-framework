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

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends SymfonyApplication
{
    /**
     * 命令行工具版本
     */
    const VERSION = '0.2.1';
    /**
     * 命令行更新版本
     */
    const RELEASE_DATE = '';

    /**
     * 初始化目录位置
     *
     * @var string
     */
    private $initialWorkingDirectory = '';

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

        $this->setDispatcher(new EventDispatcher);
        $this->initialWorkingDirectory = getcwd();
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
            "<info>%s</info> version <comment>%s</comment> %s",
            $this->getName(),
            $this->getVersion(),
            ! empty(self::RELEASE_DATE) ? self::RELEASE_DATE : 'dev'
        );
    }

    /**
     * 获得所有命令
     */
    protected function getDefaultCommands(): array
    {
        $commands = array_merge(parent::getDefaultCommands(), [
            new Command\AboutCommand()
        ], $this->getAppCommands());

        return $commands;
    }

    /**
     * 获得应用级命令
     *
     * @return array
     */
    protected function getAppCommands(): array
    {
        return [];
    }
}
