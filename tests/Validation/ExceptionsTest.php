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

class ExceptionsTest extends \PHPUnit\Framework\TestCase
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
     * @dataProvider provideFailsRules
     * @expectedException One\Validation\Exception\ValidationInvalidArgumentException
     */
    public function testValidateException(array $attributes, array $rule)
    {
        $this->validator->reset();
        $this->validator->configure([$rule]);
        $this->validator->validate($attributes);
    }

    public function provideFailsRules()
    {
        return [
            [
                [ 'name' => 129 ],
                [ 'name', 'between', 'min' => 0 ]
            ],
            [
                [ 'name' => 129 ],
                [ 'name', 'between', 'max' => 100 ]
            ],
            [
                [ 'name' => 'zar' ],
                [ 'name', 'in' ]
            ],
            [
                [ 'name' => 'foo' ],
                [ 'name', 'notIn' ]
            ],
            [
                [ 'name' => 'foobar' ],
                [ 'name', 'len' ]
            ],
            [
                [ 'name' => 1 ],
                [ 'name', 'greater' ]
            ],
            [
                [ 'name' => 11 ],
                [ 'name', 'less' ]
            ],
            [
                [ 'name' => 11 ],
                [ 'name', 'equals' ]
            ],
            [
                [ 'name' => 100 ],
                [ 'name', 'notEquals' ]
            ],
            [
                [ 'name' => new \stdClass ],
                [ 'name', 'instance' ]
            ],
            [
                [ 'name' => '1024' ],
                [ 'name', 'regex' ]
            ],
        ];
    }

    /**
     * @expectedException One\Validation\Exception\ValidationRuleExistsException
     */
    public function testValidationRuleExistsException()
    {
        $this->validator->addRule('int', function () {});
    }

    /**
     * @expectedException One\Validation\Exception\ValidationRuleMustBeCallableException
     */
    public function testValidationRuleMustBeCallableException()
    {
        $this->validator->addRule('new', false);
    }

    /**
     * @expectedException One\Validation\Exception\ValidationRuleNotExistsException
     */
    public function testValidationRuleNotExistsException()
    {
        $this->validator->reset();
        $this->validator->getRuleInstance('bad');
    }
}
