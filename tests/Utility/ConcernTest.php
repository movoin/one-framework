<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Utility
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Utility;

use One\Utility\Concern\HasGetter;
use One\Utility\Concern\HasSingleton;

class ConcernTest extends \PHPUnit\Framework\TestCase
{
    use HasGetter;
    use HasSingleton;

    public function testSingileton()
    {
        $self = ConcernTest::singleton();

        $this->assertInstanceOf(ConcernTest::class, $self);
    }

    public function getConcern()
    {
        return 'concern';
    }

    public function testGetter()
    {
        $this->assertEquals($this->concern, 'concern');
        $this->assertEquals($this->groups, ['default']);

        $b = new B;
        $this->assertEquals($b->balalalala, 'ok');
    }

    /**
     * @expectedException One\Exception\RuntimeException
     */
    public function testGetterException()
    {
        $this->bad;
    }
}

class A
{
    public function __get($name)
    {
        return 'ok';
    }
}

class B extends A
{
    use HasGetter;
}
