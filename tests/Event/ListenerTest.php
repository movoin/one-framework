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
use One\Event\Listener;
use One\Event\Contract\ListenerInterface;

class ListenerTest extends \PHPUnit\Framework\TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(ListenerInterface::class, $this->getListener());
    }

    public function testSetHandler()
    {
        $listener = $this->getListener();
        $listener->setHandler(function ($event) {});
        $this->assertInstanceOf('\\Closure', $listener->getHandler());

        $listener = $this->getListener();
        $listener->setHandler([$this, 'testInstance']);
        $this->assertIsArray($listener->getHandler());
    }

    public function testGetHandler()
    {
        $listener = $this->getListener([$this, 'testInstance']);
        $this->assertIsArray($listener->getHandler());
    }

    public function testIsSelf()
    {
        $listener = $this->getListener([$this, 'testInstance']);
        $this->assertTrue($listener->isSelf($listener));
        $this->assertTrue($listener->isSelf([$this, 'testInstance']));
    }

    public function testHandle()
    {
        $listener = $this->getListener(function ($event) {
            $event->setContexts(['name' => $event->getName()]);
        });

        $event = new Event('test');

        $listener->handle($event);

        $this->assertEquals($event->getContext()->get('name'), 'test');
    }

    /**
     * @expectedException One\Event\Exception\ListenerTypeErrorException
     */
    public function testException()
    {
        $this->getListener('bad');
    }

    private function getListener($handler = null)
    {
        return new Listener($handler);
    }
}
