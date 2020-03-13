<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Filesystem
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Filesystem;

use One\Filesystem\Manager;
use One\Filesystem\Filesystem;
use One\Filesystem\Adapter\Local;

class ManagerTest extends \PHPUnit\Framework\TestCase
{
    private $fm;

    public function setUp()
    {
        $this->fm = new Manager([
            'foo' => new Filesystem(
                new Local(__DIR__ . '/Fixtures'),
                ['visibility' => 'public']
            ),
            'bar' => new Filesystem(
                new Local(__DIR__ . '/Fixtures'),
                ['visibility' => 'public']
            )
        ]);
    }

    public function tearDown()
    {
        $this->fm = null;
    }

    public function testGetFS()
    {
        $this->assertInstanceOf('One\\Filesystem\\Filesystem', $this->fm->getFilesystem('foo'));
    }

    public function testListContents()
    {
        $this->assertCount(2, $this->fm->listContents('foo://'));
    }

    /**
     * @dataProvider copyProvider
     */
    public function testCopy($from, $to, $result)
    {
        $this->fm->copy($from, $to);
        $this->assertEquals($this->fm->exists($to), $result);
        $this->fm->delete($to);
    }

    public function copyProvider()
    {
        return [
            ['foo://file', 'foo://test', true],
            ['foo://file', 'bar://test', true],
        ];
    }

    /**
     * @dataProvider moveProvider
     */
    public function testMove($from, $to, $config = [])
    {
        $this->fm->move($from, $to, $config);
        $this->assertTrue($this->fm->exists($to));
        $this->assertFalse($this->fm->exists($from));
        $this->fm->move($to, $from);
    }

    public function moveProvider()
    {
        return [
            ['foo://file', 'foo://test', ['visibility' => 'public']],
            ['foo://file', 'bar://test'],
        ];
    }

    /**
     * @dataProvider exceptionProvider
     * @expectedException One\Filesystem\Exception\FilesystemException
     */
    public function testExceptions($method, $params)
    {
        call_user_func_array([$this->fm, $method], $params);
    }

    public function exceptionProvider()
    {
        return [
            ['getFilesystem', ['bad']],
            ['mountFilesystem', ['', new Filesystem(
                new Local(__DIR__ . '/Fixtures'),
                ['visibility' => 'public']
            )]],
            ['exists', []],
            ['bad', ['foo']],
            ['bad', ['foo://file']],
            ['bad', [[]]],
            ['read', ['test']],
        ];
    }
}
