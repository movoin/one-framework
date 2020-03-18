<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Filesystem\Iterator
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Filesystem\Iterator;

use One\Filesystem\Iterator\FileInfo;

class FileInfoTest extends \PHPUnit\Framework\TestCase
{
    private $file;

    public function setUp()
    {
        $this->file = new FileInfo(
            __DIR__ . '/../Fixtures/file.txt',
            'Fixtures',
            'Fixtures/file.txt'
        );
    }

    public function tearDown()
    {
        $this->file = null;
    }

    /**
     * @dataProvider getterPrivider
     */
    public function testGetters($method, $params, $result)
    {
        $this->assertEquals(call_user_func_array([$this->file, $method], $params), $result);
    }

    public function getterPrivider()
    {
        return [
            ['getRelativePath', [], 'Fixtures'],
            ['getRelativePathname', [], 'Fixtures/file.txt'],
            ['getFilenameWithoutExtension', [], 'file'],
            ['getContents', [], 'b'],
        ];
    }
}
