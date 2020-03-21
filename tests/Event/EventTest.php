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
use One\Event\Contract\EventInterface;
use One\Utility\Assert;

class EventTest extends \PHPUnit\Framework\TestCase
{
    private $event;

    public function setUp()
    {
        $this->event = new Event('test');
        $this->event->setEmitter(new Emitter);
        $this->event->setContexts([
            'foo' => true,
            'bar' => false
        ]);
    }

    public function tearDown()
    {
        $this->event = null;
    }

    public function testInstance()
    {
        $this->assertTrue(Assert::instanceOf($this->event, EventInterface::class));
    }

    public function testGetName()
    {
        $this->assertEquals($this->event->getName(), 'test');
    }

    public function testGetEmitter()
    {
        $this->assertInstanceOf(Emitter::class, $this->event->getEmitter());
    }

    public function testGetContext()
    {
        $this->assertTrue($this->event->getContext()->get('foo'));
        $this->assertFalse($this->event->getContext()->get('bar'));
    }
}
