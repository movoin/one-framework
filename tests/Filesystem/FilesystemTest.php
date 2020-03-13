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

use One\Filesystem\Filesystem;
use One\Filesystem\Adapter\Local;

class FilesystemTest extends \PHPUnit\Framework\TestCase
{
    protected $fs;

    public function setUp()
    {
        $this->fs = new Filesystem(
            new Local(__DIR__ . '/Fixtures')
        );
    }

    public function tearDown()
    {
         $this->fs->put('file', 'a', ['visibility' => 'public']);
         $this->fs->put('file.txt', 'b', ['visibility' => 'public']);

        if ($this->fs->exists('test')) {
            if (
                $this->fs->getMetaData('test') === [] ||
                $this->fs->getMetaData('test')['type'] === 'file'
            ) {
                $this->fs->delete('test');
            } else {
                $this->fs->deleteDir('test');
            }
        }

        $this->fs = null;
    }

    public function testGetAdapter()
    {
        $this->assertInstanceOf('One\\Filesystem\\Contract\\AdapterInterface', $this->fs->getAdapter());
    }

    public function testExists()
    {
        $this->assertTrue($this->fs->exists('test/../file'));
        $this->assertFalse($this->fs->exists('bad'));
    }

    public function testRead()
    {
        $this->assertEquals($this->fs->read('./file'), 'a');
        $this->assertIsResource($this->fs->readStream('file'));

        $this->fs->write('test', 'test');
        $this->assertEquals($this->fs->readAndDelete('test'), 'test');
    }

    public function testListContents()
    {
        $fs = new Filesystem(
            new Local(__DIR__ . '/Fixtures/test')
        );
        $fs->createDir('foo');
        $fs->createDir('foo/bar');
        $fs->write('foo/test', 'test');
        $fs->write('foo/bar/test', 'test');
        $fs->write('test', 'test');

        $this->assertCount(2, $fs->listContents(''));
        $this->assertCount(5, $fs->listContents('', true));
        $this->assertCount(3, $fs->listContents('./foo', true));
        $this->assertCount(1, $fs->listContents('./foo/bar', true));
    }

    public function testWriteAndUpdate()
    {
        $this->fs->write('test', 'test');
        $this->assertEquals($this->fs->readAndDelete('test'), 'test');

        $stream = $this->fs->readStream('file');
        fread($stream, 1);

        $this->fs->writeStream('test', $stream);
        $this->assertEquals($this->fs->read('test'), 'a');

        $this->fs->update('test', 'test');
        $this->assertEquals($this->fs->read('test'), 'test');

        $this->fs->updateStream('test', $this->fs->readStream('file'));
        $this->assertEquals($this->fs->read('test'), 'a');
    }

    public function testPut()
    {
        $this->fs->put('test', 'create');
        $this->assertEquals($this->fs->read('test'), 'create');

        $this->fs->put('test', 'update');
        $this->assertEquals($this->fs->readAndDelete('test'), 'update');

        $this->fs->putStream('test', $this->fs->readStream('file'));
        $this->assertEquals($this->fs->read('test'), 'a');

        $this->fs->putStream('test', $this->fs->readStream('file.txt'));
        $this->assertEquals($this->fs->readAndDelete('test'), 'b');
    }

    public function testRename()
    {
        $this->fs->put('new', 'create');
        $this->fs->rename('new', 'test');
        $this->assertTrue($this->fs->exists('test'));
    }

    public function testCreateAndDeleteDir()
    {
        $this->assertTrue($this->fs->createDir('test'));
        $this->assertTrue($this->fs->deleteDir('test'));
    }

    public function testGetMimeType()
    {
        $this->assertEquals($this->fs->getMimeType('file.txt'), 'text/plain');
    }

    public function testVisibility()
    {
        $this->fs->setVisibility('file', 'private');
        $this->assertEquals($this->fs->getVisibility('file'), 'private');

        $this->fs->setVisibility('file', 'public');
        $this->assertEquals($this->fs->getVisibility('file'), 'public');
    }

    /**
     * @expectedException One\Filesystem\Exception\FilesystemException
     * @dataProvider filesystemExceptions
     */
    public function testFilesystemExceptions($method, $attributes)
    {
        call_user_func_array([$this->fs, $method], $attributes);
    }

    public function filesystemExceptions()
    {
        return [
            ['exists', ['../file']],
            ['read', ['../../../file']],
            ['writeStream', ['test', '']],
        ];
    }

    /**
     * @expectedException One\Filesystem\Exception\FileException
     * @dataProvider fileExceptions
     */
    public function testFileExceptions($method, $attributes)
    {
        call_user_func_array([$this->fs, $method], $attributes);
    }

    public function fileExceptions()
    {
        return [
            ['read', ['bad']],
            ['write', ['file', '']],
        ];
    }

    /**
     * @expectedException One\Filesystem\Exception\DirectoryException
     * @dataProvider dirExceptions
     */
    public function testDirectoryExceptions($method, $attributes)
    {
        call_user_func_array([$this->fs, $method], $attributes);
    }

    public function dirExceptions()
    {
        return [
            ['createDir', ['']],
            ['deleteDir', ['']],
            ['deleteDir', ['bad']],
        ];
    }
}
