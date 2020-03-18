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

use One\Filesystem\Finder;
use One\Utility\Helper\RegexHelper;

class RegexHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testGlobToRegexDelimiters()
    {
        $this->assertEquals('#^(?=[^\.])\#$#', RegexHelper::globToRegex('#'));
        $this->assertEquals('#^\.[^/]*$#', RegexHelper::globToRegex('.*'));
        $this->assertEquals('^\.[^/]*$', RegexHelper::globToRegex('.*', true, true, ''));
        $this->assertEquals('/^\.[^/]*$/', RegexHelper::globToRegex('.*', true, true, '/'));
    }

    public function testGlobToRegexDoubleStarStrictDots()
    {
        $finder = new Finder();
        $regex = RegexHelper::globToRegex('/**/*.neon');

        foreach ($finder->in(__DIR__ . '/Fixtures') as $k => $v) {
            $k = str_replace(DIRECTORY_SEPARATOR, '/', $k);
            if (preg_match($regex, substr($k, strlen(__DIR__)))) {
                $match[] = substr($k, 10 + strlen(__DIR__));
            }
        }
        sort($match);

        $this->assertSame(['regex/one/b/c.neon', 'regex/one/b/d.neon'], $match);
    }

    public function testGlobToRegexDoubleStarNonStrictDots()
    {
        $finder = new Finder();
        $regex = RegexHelper::globToRegex('/**/*.neon', false);

        foreach ($finder->in(__DIR__ . '/Fixtures') as $k => $v) {
            $k = str_replace(DIRECTORY_SEPARATOR, '/', $k);
            if (preg_match($regex, substr($k, strlen(__DIR__)))) {
                $match[] = substr($k, 10 + strlen(__DIR__));
            }
        }
        sort($match);

        $this->assertSame(['regex/.dot/b/c.neon', 'regex/.dot/b/d.neon', 'regex/one/b/c.neon', 'regex/one/b/d.neon'], $match);
    }

    public function testGlobToRegexDoubleStarWithoutLeadingSlash()
    {
        $finder = new Finder();
        $regex = RegexHelper::globToRegex('/Fixtures/regex/one/**');

        foreach ($finder->in(__DIR__) as $k => $v) {
            $k = str_replace(DIRECTORY_SEPARATOR, '/', $k);
            if (preg_match($regex, substr($k, strlen(__DIR__)))) {
                $match[] = substr($k, 10 + strlen(__DIR__));
            }
        }
        sort($match);

        $this->assertSame(['regex/one/a', 'regex/one/b', 'regex/one/b/c.neon', 'regex/one/b/d.neon'], $match);
    }

    public function testGlobToRegexDoubleStarWithoutLeadingSlashNotStrictLeadingDot()
    {
        $finder = new Finder();
        $regex = RegexHelper::globToRegex('/Fixtures/regex/one/**', false);

        foreach ($finder->in(__DIR__) as $k => $v) {
            $k = str_replace(DIRECTORY_SEPARATOR, '/', $k);
            if (preg_match($regex, substr($k, strlen(__DIR__)))) {
                $match[] = substr($k, 10 + strlen(__DIR__));
            }
        }
        sort($match);

        $this->assertSame(['regex/one/.dot', 'regex/one/a', 'regex/one/b', 'regex/one/b/c.neon', 'regex/one/b/d.neon'], $match);
    }
}
