<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Annotation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Annotation;

use One\Annotation\Annotation;

class AnnotationTest extends \PHPUnit\Framework\TestCase
{
    private $annotation;

    public function setUp()
    {
        $this->annotation = new Annotation;
    }

    public function tearDown()
    {
        $this->annotation = null;
    }

    public function testFunction()
    {
        require_once __DIR__ . '/Fixtures/Functions.php';

        $bag = $this->annotation->getFunction('One\Tests\Annotation\Fixtures\foobar');

        $this->assertEquals($bag->get('name'), 'foobar');
    }

    /**
     * @dataProvider annotationProvider
     */
    public function testClass($name, $value)
    {
        $bag = (new Annotation)->getClass('One\Tests\Annotation\Fixtures\Test');

        $this->assertEquals($bag->get($name), $value);
    }

    public function annotationProvider()
    {
        return [
            ['boolean-type-a', true],
            ['boolean-type-b', true],
            ['boolean-type-c', false],
            ['string-type-a', 'hello world!'],
            ['string-type-b', 'hello world!'],
            ['string-type-c', '123456'],
            ['integer-type-a', 15],
            ['integer-type-b', 15],
            ['float-type-a', 0.15],
            ['float-type-b', 0.15],
            ['json-type-a', ['foo' => 'bar']],
            ['json-type-b', ['foo', 'bar']],
            ['json-type-c', ['foo' => ['bar', 'baz']]],
            ['dot-style.annotation', 'hello!'],
            ['multiline', '------
  < moo >
  ------
        \   ^__^
         \  (oo)\_______
            (__)\       )\/\
                ||----w |
                ||     ||'],
        ];
    }

    public function testMethod()
    {
        $bag = $this->annotation->getMethod('One\Tests\Annotation\Fixtures\Test', 'methodName');

        $this->assertTrue($bag->get('get'));
        $this->assertTrue($bag->get('post'));
    }

    public function testProperty()
    {
        $bag = $this->annotation->getProperty('One\Tests\Annotation\Fixtures\Test', 'foobar');

        $this->assertEquals($bag->get('test'), 'lalalala');
    }

    /**
     * @dataProvider exceptionsProvider
     * @expectedException One\Exception\RuntimeException
     */
    public function testRuntimeExceptions($method, $param)
    {
        call_user_func_array([$this->annotation, $method], $param);
    }

    public function exceptionsProvider()
    {
        return [
            ['getFunction', ['test']],
            ['getClass', ['test']],
            ['getMethod', ['test', 'method']],
            ['getMethod', ['One\Tests\Annotation\Fixtures\Test', 'badCase']],
            ['getProperty', ['test', 'property']],
        ];
    }
}
