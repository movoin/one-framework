<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Utility
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Utility;

class MimeTypeExtensionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideAll
     */
    public function testAll($method, $attribute, $result)
    {
        $this->assertEquals(MimeTypeExtension::$method($attribute), $result);
    }

    public function provideAll()
    {
        return [
            ['getExtension', 'video/x-smv', 'smv'],
            ['getExtension', 'bad', null],
            ['getMimeType', 'smv', 'video/x-smv'],
            ['getMimeType', 'bad', 'text/plain'],
            ['getMimeTypeByFilePath', __DIR__ . '/Fixtures/test.txt', 'text/plain'],
            ['getMimeTypeByFilePath', __DIR__ . '/Fixtures/test', 'text/plain'],
        ];
    }
}
