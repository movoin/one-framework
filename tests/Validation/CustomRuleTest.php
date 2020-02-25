<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Validation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Validation;

use One\Validation\Validator;

class CustomRuleTest extends \PHPUnit\Framework\TestCase
{
private $validator;

    public function setUp()
    {
        $this->validator = new Validator;
    }

    public function tearDown()
    {
        $this->validator = null;
    }

    /**
     * @dataProvider provideCustomValidators
     */
    public function testCustomValidator($validator, $result)
    {
        $this->validator->reset();
        $this->validator->addRule('test', $validator);

        $this->validator->configure([
            [ 'name', 'test' ]
        ]);

        $data = [
            'name' => 'foobar'
        ];

        $this->assertEquals($result, $this->validator->validate($data));
    }

    public function provideCustomValidators()
    {
        return [
            [
                [$this, 'returnTrue'],
                true
            ],
            [
                [$this, 'returnFalse'],
                false
            ],
            [
                \One\Tests\Validation\Fixtures\CustomRule::class,
                true
            ],
            [
                function ($attributes, $name, $parameters) {
                    return true;
                },
                true
            ]
        ];
    }

    public function returnTrue($attributes, $name, $parameters)
    {
        return true;
    }

    public function returnFalse($attributes, $name, $parameters)
    {
        return false;
    }
}
