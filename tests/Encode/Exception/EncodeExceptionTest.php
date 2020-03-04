<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Encode\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Encode\Exception;

use One\Encode\Exception\EncodeException;

class EncodeExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testAll()
    {
        $ex = new EncodeException('Json', 'Test');
        $this->assertEquals($ex->getEncoder(), 'Json');
        $this->assertEquals($ex->getMessage(), 'JSON: Test');

        $ex = new EncodeException('Json');
        $this->assertEquals($ex->getMessage(), 'JSON: 发生异常');
    }
}
