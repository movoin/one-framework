<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Filesystem\Adapter
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Filesystem\Adapter;

use One\Filesystem\Adapter\Local;

class LocalTest extends \PHPUnit\Framework\TestCase
{
    private $adapter;

    public function setUp()
    {
        $this->adapter = new Local(__DIR__ . '/../Fixtures');
    }

    public function tearDown()
    {
        if ($this->adapter->exists('file')) {
            $this->adapter->update('file', 'a', ['visibility' => 'public']);
        } else {
            $this->adapter->write('file', 'a', ['visibility' => 'public']);
        }

        if ($this->adapter->exists('file.txt')) {
            $this->adapter->update('file.txt', 'b', ['visibility' => 'public']);
        } else {
            $this->adapter->write('file.txt', 'b', ['visibility' => 'public']);
        }

        if ($this->adapter->exists('test')) {
            if (
                $this->adapter->getMetaData('test') === [] ||
                $this->adapter->getMetaData('test')['type'] === 'file'
            ) {
                $this->adapter->delete('test');
            } else {
                $this->adapter->deleteDir('test');
            }
        }

        $this->adapter = null;
    }

    public function testExists()
    {
        $this->assertTrue($this->adapter->exists('file'));
    }

    public function testRead()
    {
        $this->assertEquals($this->adapter->read('file.txt'), 'b');
    }

    public function testReadStream()
    {
        $this->assertIsResource($this->adapter->readStream('file'));
    }

    public function testListContents()
    {
        $list = $this->adapter->listContents();
        $this->assertEquals(count($list), 2);

        $list = $this->adapter->listContents('bad');
        $this->assertEquals(count($list), 0);

        $list = $this->adapter->listContents('', true);
        $this->assertEquals(count($list), 2);
    }

    public function testWrite()
    {
        $this->adapter->write('test', 'test', ['visibility' => 'public']);
        $this->assertTrue($this->adapter->getVisibility('test') === 'public');
    }

    public function testWriteStream()
    {
        $this->adapter->writeStream(
            'test',
            fopen($this->adapter->getBasePath() . '/file.txt', 'r'),
            ['visibility' => 'public']
        );
        $this->assertTrue($this->adapter->read('test') === $this->adapter->read('file.txt'));
    }

    public function testUpdate()
    {
        $this->adapter->update('file.txt', 'updated', ['visibility' => 'public']);
        $this->assertEquals($this->adapter->read('file.txt'), 'updated');
    }

    public function testUpdateStream()
    {
        $this->adapter->write('test', 'updated', ['visibility' => 'public']);
        if ($this->adapter->updateStream(
            'file.txt',
            fopen($this->adapter->getBasePath() . '/test', 'r'),
            ['visibility' => 'public']
        )) {
            $this->assertEquals($this->adapter->read('file.txt'), 'updated');
        }
    }

    public function testGetMetaData()
    {
        $this->assertEquals($this->adapter->getMetaData('file')['type'], 'file');
    }

    public function testGetMimeType()
    {
        $this->assertEquals($this->adapter->getMimeType('file'), 'text/plain');
    }

    public function testRename()
    {
        $this->adapter->rename('file.txt', 'renamed.txt');
        $this->assertTrue($this->adapter->exists('renamed.txt'));

        $this->adapter->rename('renamed.txt', 'file.txt');
        $this->assertTrue($this->adapter->exists('file.txt'));
    }

    public function testCreateDir()
    {
        $this->assertTrue($this->adapter->createDir('test'));
    }

    public function testDeleteDir()
    {
        $adapter = $this->adapter;

        $adapter->createDir('test');
        $adapter->createDir('test/foo');
        $adapter->createDir('test/foo/bar');
        $adapter->writeStream(
            'test/test.txt',
            fopen($adapter->getBasePath() . '/file.txt', 'r'),
            ['visibility' => 'public']
        );

        symlink($adapter->getBasePath() . '/test/test.txt', $adapter->getBasePath() . '/test/file.txt');

        $this->assertTrue($this->adapter->deleteDir('test'));
    }

    public function testLinkFileInfo()
    {
        symlink($this->adapter->getBasePath() . '/file.txt', $this->adapter->getBasePath() . '/test');
        $this->assertEquals($this->adapter->getMetaData('test'), []);
    }

    /**
     * @expectedException One\Filesystem\Exception\FileException
     */
    public function testSetVisibilityException()
    {
        $this->adapter->setVisibility('test.txt', 'public');
    }

    /**
     * @expectedException One\Filesystem\Exception\FileException
     * @dataProvider fileExceptions
     */
    public function testFileExceptions($method, $attributes)
    {
        call_user_func_array([$this->adapter, $method], $attributes);
    }

    public function fileExceptions()
    {
        return [
            ['read', ['bad']],
            ['readStream', ['bad']],
            ['write', ['file', '']],
            ['writeStream', ['file', '']],
            ['update', ['bad', '']],
            ['updateStream', ['bad', '']],
            ['getMetaData', ['bad']],
            ['getMimeType', ['bad']],
            ['rename', ['bad', 'newbad']],
            ['delete', ['bad']],
            ['getVisibility', ['bad']],
        ];
    }

    /**
     * @expectedException One\Filesystem\Exception\DirectoryException
     * @dataProvider dirExceptions
     */
    public function testDirectoryExceptions($method, $attributes)
    {
        call_user_func_array([$this->adapter, $method], $attributes);
    }

    public function dirExceptions()
    {
        return [
            ['createDir', ['']],
            ['deleteDir', ['bad']],
        ];
    }
}
