<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Utility\Helper
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Utility\Helper;

/**
 * 正则助手类
 *
 * @since 0.2
 */
class RegexHelper
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * 判断字符串是否为正则表达式
     *
     * @param string $str
     *
     * @return bool
     */
    public static function isRegex(string $str): bool
    {
        if (preg_match('/^(.{3,}?)[imsxuADU]*$/', $str, $m)) {
            $start = substr($m[1], 0, 1);
            $end = substr($m[1], -1);

            if ($start === $end) {
                return !preg_match('/[*?[:alnum:] \\\\]/', $start);
            }

            foreach ([['{', '}'], ['(', ')'], ['[', ']'], ['<', '>']] as $delimiters) {
                if ($start === $delimiters[0] && $end === $delimiters[1]) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 文件路径转换为正则表达式
     *
     * 支持：filename.*, *.php, file.ext, file
     *
     * @param string $glob
     * @param bool $strictLeadingDot
     * @param bool $strictWildcardSlash
     * @param string $delimiter
     *
     * @return string
     */
    public static function globToRegex(
        string $glob,
        bool $strictLeadingDot = true,
        bool $strictWildcardSlash = true,
        string $delimiter = '#'
    ): string {
        $firstByte = true;
        $escaping = false;
        $inCurlies = 0;
        $regex = '';
        $sizeGlob = strlen($glob);
        for ($i = 0; $i < $sizeGlob; ++$i) {
            $car = $glob[$i];
            if ($firstByte && $strictLeadingDot && '.' !== $car) {
                $regex .= '(?=[^\.])';
            }

            $firstByte = '/' === $car;

            if (
                $firstByte &&
                $strictWildcardSlash &&
                isset($glob[$i + 2]) &&
                '**' === $glob[$i + 1] . $glob[$i + 2] &&
                (! isset($glob[$i + 3]) || '/' === $glob[$i + 3])
            ) {
                $car = '[^/]++/';
                if (! isset($glob[$i + 3])) {
                    $car .= '?';
                }

                if ($strictLeadingDot) {
                    $car = '(?=[^\.])' . $car;
                }

                $car = '/(?:' . $car . ')*';
                $i += 2 + isset($glob[$i + 3]);

                if ('/' === $delimiter) {
                    $car = str_replace('/', '\\/', $car);
                }
            }

            if (
                $delimiter === $car ||
                '.' === $car ||
                '(' === $car ||
                ')' === $car ||
                '|' === $car ||
                '+' === $car ||
                '^' === $car ||
                '$' === $car
            ) {
                $regex .= "\\$car";
            } elseif ('*' === $car) {
                $regex .= $escaping ? '\\*' : ($strictWildcardSlash ? '[^/]*' : '.*');
            } elseif ('?' === $car) {
                $regex .= $escaping ? '\\?' : ($strictWildcardSlash ? '[^/]' : '.');
            } elseif ('{' === $car) {
                $regex .= $escaping ? '\\{' : '(';
                if (! $escaping) {
                    ++$inCurlies;
                }
            } elseif ('}' === $car && $inCurlies) {
                $regex .= $escaping ? '}' : ')';
                if (! $escaping) {
                    --$inCurlies;
                }
            } elseif (',' === $car && $inCurlies) {
                $regex .= $escaping ? ',' : '|';
            } elseif ('\\' === $car) {
                if ($escaping) {
                    $regex .= '\\\\';
                    $escaping = false;
                } else {
                    $escaping = true;
                }

                continue;
            } else {
                $regex .= $car;
            }
            $escaping = false;
        }

        return $delimiter . '^' . $regex . '$' . $delimiter;
    }
}
