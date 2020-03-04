<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Encode
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Encode;

use One\Encode\Base62;

class Base62Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider base62Provider
     */
    public function testBase62($data, $type)
    {
        $encode = Base62::encode($data);
        $this->assertEquals($data, Base62::decode($encode, $type));
    }

    public function base62Provider()
    {
        return [
            ['', Base62::TYPE_STRING],
            ['string', Base62::TYPE_STRING],
            ["\x00\x00\x00\x01\x02", Base62::TYPE_STRING],
            [123456, Base62::TYPE_INT],
        ];
    }

    /**
     * @expectedException One\Encode\Exception\EncodeException
     */
    public function testEncodeBase62Exception()
    {
        Base62::encode([]);
    }

    /**
     * @expectedException One\Encode\Exception\EncodeException
     */
    public function testDecodeBase62Exception()
    {
        Base62::decode('ababab', 3);
    }

    /**
     * @expectedException One\Encode\Exception\EncodeException
     */
    public function testValidateBase62Exception()
    {
        Base62::decode('-07d8e31da269bf28');
    }
}
