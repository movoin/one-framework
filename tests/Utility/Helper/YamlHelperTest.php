<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Utility\Helper
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Utility\Helper;

use One\Utility\Helper\YamlHelper;

class YamlHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testParseFile()
    {
        $this->assertTrue(is_array(YamlHelper::parseFile(__DIR__ . '/Fixtures/test.yml')));
        $this->assertTrue(is_null(YamlHelper::parseFile(__DIR__ . '/Fixtures/empty.yml')));
        $this->assertTrue(is_null(YamlHelper::parseFile('bad.yml')));
    }

    public function testParseString()
    {
        $this->assertSame(YamlHelper::parse('foo: bar'), ['foo' => 'bar']);
        $this->assertTrue(is_null(YamlHelper::parse('')));
    }

    public function testDump()
    {
        $this->assertSame(YamlHelper::dump(['foo' => 'bar']), "foo: bar\n");
        $this->assertSame(YamlHelper::dump(['zar' => 'tar']), "zar: tar\n");
    }

    /**
     * @expectedException One\Exception\RuntimeException
     */
    public function testParseTabException()
    {
        YamlHelper::parseFile(__DIR__ . '/Fixtures/tab.yml');
    }

    /**
     * @expectedException One\Exception\RuntimeException
     */
    public function testParseException()
    {
        YamlHelper::parse("\t");
    }
}
