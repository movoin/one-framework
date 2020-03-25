<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Console\Command
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AboutCommand extends Command
{
    /**
     * 配置命令
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('about')
            ->setDescription('About One Console Tool')
            ->setHelp(
                <<<EOT
<info>php one about</info>
EOT
            )
        ;
    }

    /**
     * 执行命令
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            <<<EOT
<info>One Console Tool - One Framework 命令行工具集</info>
<comment>One Framework 是一款基于 Swoole 的服务开发框架，具备 Restful API、RPC 等项目的快速开发能力。</comment>
EOT
        );

        return 0;
    }
}
