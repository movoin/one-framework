<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Annotation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Annotation;

/**
 * DocBlock 解析类
 *
 * @since 0.2
 */
class Parser
{
    const TOKEN_ID = '@';
    const TOKEN_NAME = '[a-zA-Z\_\-\\\][a-zA-Z0-9\_\-\.\\\]*';

    /**
     * DocBlock 正则表达式
     *
     * @var string
     */
    protected $pattern;
    protected $typesPattern;

    /**
     * 强类型解析类
     *
     * @var array
     */
    private $types = [
        '\\One\\Annotation\\Type\\IntegerType' => 'int',
        '\\One\\Annotation\\Type\\StringType' => 'string',
        '\\One\\Annotation\\Type\\FloatType' => 'float',
        '\\One\\Annotation\\Type\\JsonType' => 'json',
    ];

    /**
     * 构造
     */
    public function __construct()
    {
        $this->pattern = '/(?<=\\'. self::TOKEN_ID .')('
            . self::TOKEN_NAME
            .')(((?!\s\\'. self::TOKEN_ID .').)*)/s'
        ;
        $this->typesPattern = '/^(' . implode('|', $this->types) . ')(\s+)/';
    }

    /**
     * 解析
     *
     * @param string $docblock
     *
     * @return array
     * @throws \One\Annotation\Exception\ParseException
     */
    public function parse(string $docblock): array
    {
        $docblock = $this->getDocblockTagsSection($docblock);
        $annotations = $this->parseAnnotations($docblock);
        unset($docblock);

        foreach ($annotations as &$value) {
            if (1 === count($value)) {
                $value = $value[0];
            }
        }

        return $annotations;
    }

    /**
     * 解析注释
     *
     * @param string $str
     *
     * @return array
     * @throws \One\Annotation\Exception\ParseException
     */
    protected function parseAnnotations(string $str): array
    {
        $annotations = [];

        preg_match_all($this->pattern, $str, $found);
        foreach ($found[2] as $key => $value) {
            $annotations[$found[1][$key]][] = $this->parseValue($value);
        }

        unset($found);

        return $annotations;
    }

    /**
     * 解析注释值
     *
     * @param string $value
     *
     * @return mixed
     * @throws \One\Annotation\Exception\ParseException
     */
    protected function parseValue(string $value)
    {
        $value = trim($value);
        $type = '\\One\\Annotation\\Type\\DynamicType';

        if (preg_match($this->typesPattern, $value, $found)) {
            $type = $found[1];
            $value = trim(substr($value, strlen($type)));
            if (in_array($type, $this->types)) {
                $type = array_search($type, $this->types);
            }
        }

        return (new $type)->parse($value);
    }

    /**
     * 获得 DocBlock 标签段落，同时删除描述
     *
     * @param string $docblock
     *
     * @return string
     */
    protected function getDocblockTagsSection(string $docblock): string
    {
        $docblock = $this->sanitizeDocblock($docblock);
        preg_match('/^\s*\\'.self::TOKEN_ID.'/m', $docblock, $matches, PREG_OFFSET_CAPTURE);

        return isset($matches[0]) ? substr($docblock, $matches[0][1]) : '';
    }

    /**
     * 过滤 DocBlock
     *
     * @param string $docblock
     *
     * @return string
     */
    protected function sanitizeDocblock(string $docblock): string
    {
        return preg_replace('/\s*\*\/$|^\s*\*\s{0,1}|^\/\*{1,2}/m', '', $docblock);
    }
}
