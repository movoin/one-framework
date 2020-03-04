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

use One\Utility\Reflection;

class ReflectionTest extends \PHPUnit\Framework\TestCase
{
    public function testNewInstance()
    {
        $this->assertInstanceOf(
            '\\stdClass',
            Reflection::newInstance(\stdClass::class)
        );
    }
}
