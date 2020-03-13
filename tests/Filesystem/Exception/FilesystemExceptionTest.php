<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Filesystem\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Filesystem\Exception;

use One\Filesystem\Exception\FilesystemException;

class FilesystemExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testAll()
    {
        $ex = new FilesystemException('Test');
        $ex->setAdapterName('Test');

        $this->assertEquals(
            $ex->getAdapterName(),
            'Test'
        );

        $this->assertEquals(
            $ex->getMessage(),
            'Test'
        );

    }
}
