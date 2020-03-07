<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Annotation\Fixtures
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Annotation\Fixtures;

/**
 * Test Annotation
 *
 * @boolean-type-a
 * @boolean-type-b true
 * @boolean-type-c false
 *
 * @integer-type-a 15
 * @integer-type-b int 15
 *
 * @float-type-a 0.15
 * @float-type-b float 0.15
 *
 * @string-type-a  hello world!
 * @string-type-b "hello world!"
 * @string-type-c string 123456
 *
 * @json-type-a { "foo" : "bar" }
 * @json-type-b json ["foo", "bar"]
 * @json-type-c {
 *   "foo" : [
 *      "bar", "baz"
 *    ]
 * }
 *
 * @dot-style.annotation hello!
 *
 * @multiline
 *   ------
 *   < moo >
 *   ------
 *         \   ^__^
 *          \  (oo)\_______
 *             (__)\       )\/\
 *                 ||----w |
 *                 ||     ||
 */
class Test
{
    /**
     * foobar
     *
     * @test lalalala
     *
     * @var string
     */
    public $foobar;

    /**
     * method
     *
     * @get @post
     *
     * @return void
     */
    public function methodName()
    {
    }

    /**
     * bad case
     *
     * @name int "foobar"
     *
     * @return void
     */
    public function badCase()
    {
    }
}
