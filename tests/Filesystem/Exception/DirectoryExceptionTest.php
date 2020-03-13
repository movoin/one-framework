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

use One\Filesystem\Exception\DirectoryException;

class DirectoryExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testAll()
    {
        $ex = DirectoryException::directoryNotExistsException('Test', 'test');
        $this->assertEquals(
            $ex->getMessage(),
            '适配器 Test: 目录 test 不存在'
        );
        $this->assertEquals(
            $ex->getDirName(),
            'test'
        );

        $ex = DirectoryException::directoryExistsException('Test', 'test');
        $this->assertEquals(
            $ex->getMessage(),
            '适配器 Test: 目录 test 已存在'
        );
    }
}
