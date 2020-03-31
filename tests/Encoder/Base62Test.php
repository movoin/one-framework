<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Encoder
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Encoder;

use One\Encoder\Base62;

class Base62Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider base62Provider
     */
    public function testBase62($data)
    {
        $encode = Base62::encode($data);
        $this->assertEquals($data, Base62::decode($encode));
    }

    public function base62Provider()
    {
        return [
            [''],
            ['string'],
            ["\x00\x00\x00\x01\x02"],
            ['123456'],
        ];
    }

    /**
     * @expectedException One\Encoder\Exception\DecodeException
     */
    public function testValidateBase62Exception()
    {
        Base62::decode('-07d8e31da269bf28');
    }
}
