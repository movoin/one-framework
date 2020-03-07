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
use One\Annotation\Exception\ParseException;
use One\Exception\RuntimeException;

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
     * @param mixed $function
     *
     * @return \One\Annotation\AnnotationBag
     * @throws \One\Exception\RuntimeException
     */
    public function getFunction($function): AnnotationBag
    {
        try {
            $reflection = new ReflectionFunction($function);
        } catch (ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->getAnnotations($reflection);
    }

    /**
     * 获得类的 DocBlock
     *
     * @param mixed $class
     *
     * @return \One\Annotation\AnnotationBag
     * @throws \One\Exception\RuntimeException
     */
    public function getClass($class): AnnotationBag
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
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
     * @throws \One\Exception\RuntimeException
     */
    public function getProperty($class, string $property): AnnotationBag
    {
        try {
            $reflection = new ReflectionProperty($class, $property);
        } catch (ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
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
     * @throws \One\Exception\RuntimeException
     */
    public function getMethod($class, string $method): AnnotationBag
    {
        try {
            $reflection = new ReflectionMethod($class, $method);
        } catch (ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->getAnnotations($reflection);
    }

    /**
     * 获得注释
     *
     * @param \Reflector $reflection
     *
     * @return \One\Annotation\AnnotationBag
     * @throws \One\Exception\RuntimeException
     */
    public function getAnnotations(Reflector $reflection): AnnotationBag
    {
        $docblock = $reflection->getDocComment();

        try {
            $annotations = $this->parser->parse($docblock);
            unset($docblock);
        } catch (ParseException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return new AnnotationBag($annotations);
    }
}
