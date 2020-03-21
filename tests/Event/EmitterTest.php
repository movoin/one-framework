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
use One\Event\Listener;
use One\Event\OnceListener;

class EmitterTest extends \PHPUnit\Framework\TestCase
{
    private $emitter;

    public function setUp()
    {
        $this->emitter = new Emitter;
        $this->emitter
            ->on(new Event('test'), [$this, 'fakeHandler'])
            ->once(new Event('test'), FakeListener::class) // Once
            ->on(new Event('test'), function ($event) {})
            ->on(new Event('test'), new OnceListener(function ($event) {})) // Once
            ->once(new Event('test'), new Listener(function ($event) {})) // Once
        ;
    }

    public function tearDown()
    {
        $this->emitter = null;
    }

    public function testOnOff()
    {
        $emitter = new Emitter;
        $emitter->on('test', function ($event) {});
        $this->assertTrue($emitter->hasListener('test'));

        $emitter->off('test');
        $this->assertFalse($emitter->hasListener('test'));

        $emitter->on('test', new OnceListener(function ($event) {}));
        $emitter->once('test', new Listener(function ($event) {}));

        $this->assertCount(2, $emitter->getListeners('test'));
    }

    public function testGetListeners()
    {
        $this->assertCount(5, $this->emitter->getListeners('test'));
        $this->assertCount(0, $this->emitter->getListeners('bad'));
    }

    public function testRemoveListener()
    {
        $emitter = new Emitter;
        $event = new Event('test');
        $emitter
            ->on(new Event('test'), [$this, 'fakeHandler'])
            ->once(new Event('test'), FakeListener::class)
            ->on(new Event('test'), function ($event) {})
            ->on(new Event('test'), new OnceListener(function ($event) {})) // Once
            ->once(new Event('test'), new Listener(function ($event) {})) // Once
        ;

        if ($emitter->removeListener($event, [$this, 'fakeHandler'])) {
            $this->assertTrue($emitter->removeListener($event, [$this, 'fakeHandler']));
        }
        $this->assertCount(4, $emitter->getListeners($event));

        $emitter->off('test');
        $this->assertTrue($emitter->removeListener($event, [$this, 'fakeHandler']));
        $this->assertCount(0, $emitter->getListeners($event));
    }

    private $context;

    public function testEmit()
    {
        $event = new Event('test');

        $this->emitter->emit(
            $event,
            [
                'foo' => false,
                'bar' => true,
            ]
        );

        $context = $event->getContext();

        $this->assertFalse($context->get('foo'));

        $this->emitter->emit(
            'test',
            [
                'foo' => false,
                'bar' => true,
            ]
        );

        $this->assertFalse($context->get('bar'));
    }

    /**
     * @expectedException One\Event\Exception\EventException
     * @dataProvider eventExceptionProvider
     */
    public function testEventException($method, $params)
    {
        call_user_func_array([$this->emitter, $method], $params);
    }

    public function eventExceptionProvider()
    {
        return [
            ['emit', [[], []]],
            ['getListeners', [null]]
        ];
    }

    /**
     * @expectedException One\Event\Exception\ListenerException
     */
    public function testListenerException()
    {
        $this->emitter->addListener('bad', true);
    }

    public function fakeHandler($event)
    {
    }
}

class FakeListener extends Listener
{
    public function getHandler()
    {
        return function ($event) {
            $event->getContext()->set('bar', false);
        };
    }
}
