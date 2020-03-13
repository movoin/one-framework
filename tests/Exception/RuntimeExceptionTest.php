<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Exception;

use One\Exception\RuntimeException;

class RuntimeExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider messageProvider
     */
    public function testMessages($message, $result)
    {
        $ex = new RuntimeException($message);
        $this->assertEquals($ex->getMessage(), $result);
    }

    public function messageProvider()
    {
        return [
            ['hello', 'hello'],
            [['{foo} is {bar}' => [
                'foo' => 'A',
                'bar' => 'B'
            ]], 'A is B'],
            [['{foo}{foo}{foo} is {bar}{bar}{bar}' => [
                'foo' => 'A',
                'bar' => 'B'
            ]], 'AAA is BBB'],
            [['{foo} is {bar}' => []], '{foo} is {bar}'],
        ];
    }
}
