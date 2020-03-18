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

use One\Filesystem\Finder;
use One\Filesystem\Filesystem;
use One\Filesystem\Adapter\Local;

class FinderTest extends \PHPUnit\Framework\TestCase
{
    private $fs;

    public function setUp()
    {
        $this->fs = new Filesystem(
            new Local(__DIR__ . '/Fixtures')
        );

        $this->fs->createDir('finder');
        $this->fs->createDir('finder/foo');
        $this->fs->createDir('finder/bar');

        $this->fs->put('finder/FooTest.xml', 'test');
        $this->fs->put('finder/BarTest.xml', 'test');
        $this->fs->put('finder/TarTest.xml', 'test');
        $this->fs->put('finder/foo/a.xml', 'test');
        $this->fs->put('finder/bar/a.xml', 'test');
    }

    public function tearDown()
    {
        $this->fs->deleteDir('finder');
        $this->fs = null;
    }

    public function testName()
    {
        $finder = $this->buildFinder()->files()->name('*.xml');
        $this->assertCount(5, $finder);

        $finder = $this->buildFinder()->files()->name('*.php');
        $this->assertCount(0, $finder);
    }

    public function testNotName()
    {
        $finder = $this->buildFinder()->files()->notName('/Test\.xml$/');
        $this->assertCount(2, $finder);
    }

    public function testIn()
    {
        $finder = $this->buildFinder(__DIR__ . '/Fixtures/*/')->files();
        $this->assertCount(5, $finder);

        $finder = $this
            ->buildFinder(__DIR__ . '/Fixtures/finder/foo/')
            ->in(__DIR__ . '/Fixtures/finder/bar/')
            ->files()
        ;
        $this->assertCount(2, $finder);
    }

    public function testExclude()
    {
        $finder = $this->buildFinder()->files()->exclude('foo')->exclude('bar');
        $this->assertCount(3, $finder);
    }

    public function testDirs()
    {
        $finder = $this->buildFinder()->dirs();
        $this->assertCount(2, $finder);
    }

    public function testFilter()
    {
        $finder = $this->buildFinder()
            ->files()
            ->filter(function ($file) {
                return $file->getRelativePath() === 'foo';
            })
        ;
        $this->assertCount(1, $finder);
    }

    /**
     * @expectedException One\Filesystem\Exception\FilesystemException
     */
    public function testFilesystemExceptions()
    {
        $finder = new Finder;
        $finder->getIterator();
    }

    /**
     * @expectedException One\Filesystem\Exception\DirectoryException
     * @dataProvider dirExceptions
     */
    public function testDirectoryExceptions($method, $params)
    {
        $finder = $this->buildFinder();

        call_user_func_array([$finder, $method], $params);
    }

    public function dirExceptions()
    {
        return [
            ['in', ['bad']],
            ['in', ['/Fixtures/*/*']]
        ];
    }

    private function buildFinder($in = __DIR__ . '/Fixtures/finder')
    {
        return (new Finder)->in($in);
    }
}
