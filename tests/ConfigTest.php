<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests;

use One\Config;

define('ROOT_PATH', __DIR__ . '/Fixtures');

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    private $config;

    public function setUp()
    {
        $this->config = new Config(ROOT_PATH . '/Config/config', 'local');
    }

    public function tearDown()
    {
        $this->config = null;
    }

    public function testGet()
    {
        $this->assertEquals($this->config->get('app.name'), 'one');
    }

    public function testLoad()
    {
        $this->config->load();
        $this->config->reload();
        $this->assertIsArray($this->config->all());
    }

    public function testSwoole()
    {
        $config = new Config(ROOT_PATH . '/Config/config', 'test');
        $this->assertEquals($config->get('protocol.http.swoole.daemonize'), 0);
    }

    /**
     * @expectedException One\Exception\RuntimeException
     */
    public function testException()
    {
        $config = new Config('path/to/file');
    }
}
