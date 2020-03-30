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

use Reflector;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionException;
use One\Annotation\Parser;
use One\Annotation\AnnotationBag;
use One\Annotation\Exception\AnnotationParseErrorException;
use One\Annotation\Exception\AnnotationTargetNotFoundException;

/**
 * 注释读取类
 *
 * @since 0.2
 */
class Annotation
{
    /**
     * DocBlock 解析类
     *
     * @var \One\Annotation\Parser
     */
    protected $parser;

    /**
     * 构造
     */
    public function __construct()
    {
        $this->parser = new Parser;
    }

    /**
     * 获得函数的 DocBlock
     *
     * @param mixed $func
     *
     * @return \One\Annotation\AnnotationBag
     * @throws \One\Annotation\Exception\AnnotationTargetNotFoundException
     * @throws \One\Annotation\Exception\AnnotationParseErrorException
     */
    public function getFunction($func): AnnotationBag
    {
        try {
            $reflection = new ReflectionFunction($func);
        } catch (ReflectionException $e) {
            throw AnnotationTargetNotFoundException::functionNotFound($func, $e);
        }

        return $this->getAnnotations($reflection);
    }

    /**
     * 获得类的 DocBlock
     *
     * @param mixed $class
     *
     * @return \One\Annotation\AnnotationBag
     * @throws \One\Annotation\Exception\AnnotationTargetNotFoundException
     * @throws \One\Annotation\Exception\AnnotationParseErrorException
     */
    public function getClass($class): AnnotationBag
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw AnnotationTargetNotFoundException::classNotFound($class, $e);
        }

        return $this->getAnnotations($reflection);
    }

    /**
     * 获得类属性的 DocBlock
     *
     * @param mixed $class
     * @param string $property
     *
     * @return \One\Annotation\AnnotationBag
     * @throws \One\Annotation\Exception\AnnotationTargetNotFoundException
     * @throws \One\Annotation\Exception\AnnotationParseErrorException
     */
    public function getProperty($class, string $property): AnnotationBag
    {
        try {
            $reflection = new ReflectionProperty($class, $property);
        } catch (ReflectionException $e) {
            throw AnnotationTargetNotFoundException::propertyNotFound($class, $property, $e);
        }

        return $this->getAnnotations($reflection);
    }

    /**
     * 获得类方法的 DocBlock
     *
     * @param mixed $class
     * @param string method
     *
     * @return \One\Annotation\AnnotationBag
     * @throws \One\Annotation\Exception\AnnotationTargetNotFoundException
     * @throws \One\Annotation\Exception\AnnotationParseErrorException
     */
    public function getMethod($class, string $method): AnnotationBag
    {
        try {
            $reflection = new ReflectionMethod($class, $method);
        } catch (ReflectionException $e) {
            throw AnnotationTargetNotFoundException::methodNotFound($class, $method, $e);
        }

        return $this->getAnnotations($reflection);
    }

    /**
     * 获得注释
     *
     * @param \Reflector $reflection
     *
     * @return \One\Annotation\AnnotationBag
     * @throws \One\Annotation\Exception\AnnotationParseErrorException
     */
    public function getAnnotations(Reflector $reflection): AnnotationBag
    {
        $docblock = $reflection->getDocComment();
        $annotations = $this->parser->parse($docblock);
        unset($docblock);

        return new AnnotationBag($annotations);
    }
}
