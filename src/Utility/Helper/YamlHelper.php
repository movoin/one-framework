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

use One\Exception\RuntimeException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Yaml 助手类
 *
 * @static
 * @since 0.2
 */
class YamlHelper
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * 解析 YAML 文件内容
     *
     * @param string $filename
     * @param mixed $default
     *
     * @return mixed
     * @throws \One\Exception\RuntimeException
     */
    public static function parseFile(string $filename, $default = null)
    {
        if (! file_exists($filename)) {
            return $default;
        }

        try {
            $yaml = new Parser();
            $data = $yaml->parseFile($filename);
        } catch (ParseException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        unset($yaml);

        return $data === null ? $default : $data;
    }

    /**
     * 解析 YAML 字符串
     *
     * @param string $input
     * @param mixed $default
     *
     * @return mixed
     * @throws \One\Exception\RuntimeException
     */
    public static function parse(string $input, $default = null)
    {
        if (empty($input)) {
            return $default;
        }

        try {
            $yaml = new Parser();
            $data = $yaml->parse($input);
        } catch (ParseException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        unset($yaml);

        return $data === null ? $default : $data;
    }

    /**
     * 将输入内容转换为 Yaml
     *
     * @param mixed $input
     *
     * @return string
     */
    public static function dump($input): string
    {
        return (new Dumper(2))->dump($input, 2, 0);
    }
}
