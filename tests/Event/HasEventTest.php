<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Event
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Event;

use One\Event\Event;
use One\Event\Emitter;
use One\Event\Concern\HasEvent;
use One\Event\Concern\HasEventBehavior;
use One\Event\Contract\EventInterface;
use One\Utility\Assert;

class HasEventTest extends \PHPUnit\Framework\TestCase
{
    protected $test;

    public function setUp()
    {
        $this->test = new Test;
        $this->test->initializeEventBehavior();
    }

    public function tearDown()
    {
        $this->test = null;
    }

    public function testInstance()
    {
        $this->assertInstanceOf(EventInterface::class, $this->test->newEvent('test'));
    }

    public function testEmit()
    {
        $event = $this->test->newEvent('test.start', ['test' => 'new']);
        $this->test->emit($event, ['test' => 'emit']);
        $this->assertEquals($event->getContext()->get('test'), 'start');
    }

    public function testOnOff()
    {
        $this->test->on('test', function () {});
        $this->assertTrue($this->test->getEmitter()->hasListener('test'));

        $this->test->off('test');
        $this->assertFalse($this->test->getEmitter()->hasListener('test'));

        $this->test->once('test', function () {});
        $this->assertTrue($this->test->getEmitter()->hasListener('test'));
    }
}

class Test
{
    use HasEvent;
    use HasEventBehavior;

    public function onTestStart(EventInterface $event)
    {
        $event->getContext()->set('test', 'start');
    }
}
