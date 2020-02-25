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
    public function testGetExtension()
    {
        $this->assertEquals(MimeTypeExtension::getExtension('video/x-smv'), 'smv');
        $this->assertEquals(MimeTypeExtension::getExtension('bad'), null);
    }

    public function testGetMimeType()
    {
        $this->assertEquals(MimeTypeExtension::getMimeType('smv'), 'video/x-smv');
        $this->assertEquals(MimeTypeExtension::getMimeType('bad'), null);
    }
}
