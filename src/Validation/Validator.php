<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Validation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Validation;

use One\Utility\Assert;
use One\Utility\Reflection;
use One\Validation\Contract\Rule;
use One\Validation\Exception\ValidationException;
use One\Validation\Rule\CustomRule;

/**
 * 数据规则校验器
 *
 * @since 0.2
 */
class Validator
{
    /**
     * 内建校验规则
     *
     * @var array
     */
    protected $builtinRules = [
        'AlphaNumericRule', 'ArrayRule', 'BetweenRule', 'BooleanRule', 'DatetimeRule',
        'EmailRule', 'EqualsRule', 'FloatRule', 'GreaterRule', 'InRule',
        'InstanceOfRule', 'IntegerRule', 'IpAddressRule', 'JsonRule', 'LengthRule',
        'LessRule', 'MobileRule', 'NotEqualsRule', 'NotInRule', 'NotNullRule',
        'NullRule', 'NumberRule', 'PhoneRule', 'RegexRule', 'RequiredRule',
        'StringRule', 'UrlRule',
    ];
    /**
     * 自定义校验规则
     *
     * @var array
     */
    protected $customRules = [];
    /**
     * 校验规则实例
     *
     * @var array
     */
    protected $ruleInstances = [];
    /**
     * 校验错误消息
     *
     * @var array
     */
    protected $errors = [];
    /**
     * 校验规则配置
     *
     * ['names', 'rule', 'on' => 'scenario', 'except' => 'scenario', ...$attributes]
     *
     * @var array
     */
    protected $config = [];
    /**
     * 校验场景
     *
     * @var string
     */
    protected $scenario;

    /**
     * 构造
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    /**
     * 配置
     *
     * @param array $config
     *
     * @return void
     */
    public function configure(array $config): void
    {
        $this->config = $config;

        // 初始化规则
        $builtin = $this->builtinRules;
        $this->builtinRules = [];
        array_walk($builtin, function (string $class) {
            $names = forward_static_call(["\\One\\Validation\\Rule\\{$class}", 'getNames']);
            array_walk($names, function (string $name) use ($class) {
                $this->builtinRules[$name] = $class;
            });
            unset($names);
        });

        unset($builtin);
    }

    /**
     * 重置校验器
     *
     * @return void
     */
    public function reset(): void
    {
        $this->config = [];
        $this->errors = [];
        $this->scenario = null;
    }

    /**
     * 校验数据
     *
     * @param array $attributes
     *
     * @return bool
     * @throws \One\Validation\Exception\ValidationException
     */
    public function validate(array $attributes): bool
    {
        $configs = $this->getScenarioConfig();
        foreach ($configs as $parameters) {
            $names = array_map('trim', explode(',', $parameters[0]));
            unset($parameters[0]);
            $this->validateValue($attributes, $names, $parameters);
            unset($names);
        }
        unset($configs);

        return count($this->errors) === 0;
    }

    /**
     * 校验指定规则的数据
     *
     * @param array $attributes
     * @param array $names
     * @param array $parameters
     *
     * @return bool
     * @throws \One\Validation\Exception\ValidationException
     */
    public function validateValue(array $attributes, array $names, array $parameters): bool
    {
        $instance = $this->getRuleInstance(array_shift($parameters));

        foreach ($names as $name) {
            if (! $instance($attributes, $name, $parameters)) {
                return false;
            }
        }

        unset($instance);

        return true;
    }

    /**
     * 判断是否存在指定规则
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasRule(string $name): bool
    {
        return isset($this->builtinRules[$name]) ||
            isset($this->customRules[$name]) ||
            isset($this->ruleInstances[$name]);
    }

    /**
     * 添加自定义校验规则
     *
     * **示例：**
     *
     * - 类方法
     * ```
     * $validator->addRule('class_method', [$this, 'ruleMethod']);
     * ```
     *
     * - 类命名空间，必须实现 `One\Validation\Contract\Rule` 接口
     * ```
     * $validator->addRule('namespace', '\\App\\Validation\\Rule\\Namespace');
     * ```
     *
     * - 类名，必须实现 `One\Validation\Contract\Rule` 接口
     * ```
     * $validator->addRule('className', RuleClass::name);
     * ```
     *
     * - 函数
     * ```
     * $validator->addRule('func', 'func_name');
     * ```
     *
     * - 闭包
     * ```
     * $validator->addRule('func', function ($attributes, $name, $parameters) {
     *     return true;
     * });
     * ```
     *
     * @param array $rule
     *
     * @return void
     * @throws \One\Validation\Exception\ValidationException
     */
    public function addRule(string $name, $rule): void
    {
        if ($this->hasRule($name)) {
            throw new ValidationException($name, "规则已被定义");
        }
        if (! $this->isCustomRule($rule)) {
            throw new ValidationException($name, "规则必须可回调");
        }

        $this->customRules[$name] = $rule;
    }

    /**
     * 获得规则实例
     *
     * @param string $name
     *
     * @return \One\Validation\Contract\Rule
     * @throws \One\Validation\Exception\ValidationException
     */
    public function getRuleInstance(string $name): Rule
    {
        if (isset($this->ruleInstances[$name])) {
            return $this->ruleInstances[$name];
        }

        if (($rule = $this->getRuleDefine($name)) !== false) {
            if (Assert::string($rule)) {
                $namespace = Assert::namespace($rule) ?
                    $rule :
                    '\\One\\Validation\\Rule\\' . $rule;

                return $this->ruleInstances[$name] = Reflection::newInstance($namespace, [$this]);
            }
            if (Assert::array($rule)) {
                return $this->ruleInstances[$name] = CustomRule::createFromArray($this, $rule);
            }
            if (Assert::callable($rule)) {
                return $this->ruleInstances[$name] = CustomRule::createFromClosure($this, $rule);
            }
        }

        throw new ValidationException($name, "校验规则未定义");
    }

    /**
     * 获得全部错误信息
     *
     * @return array
     */
    public function getErrors(): array
    {
        $errors = $this->errors;
        $this->errors = [];

        return $errors;
    }

    /**
     * 获得最新一条错误信息
     *
     * @return string
     */
    public function getLastError(): string
    {
        if (empty($this->errors)) {
            return '';
        }

        return array_shift($this->errors);
    }

    /**
     * 添加校验错误信息
     *
     * @param string $error
     *
     * @return void
     */
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * 设置当前校验场景
     *
     * @param string $scenario
     *
     * @return void
     */
    public function setScenario(string $scenario): void
    {
        $this->scenario = strtolower($scenario);
    }

    /**
     * 获得当前场景的校验规则配置
     *
     * @return array
     */
    protected function getScenarioConfig(): array
    {
        if ($this->scenario === null) {
            return $this->config;
        }

        $config = [];
        foreach ($this->config as $conf) {
            if (! isset($conf['on']) && ! isset($conf['except'])) {
                $config[] = $conf;
            }
            if (isset($conf['on']) && $this->isMatchScenario($conf['on'])) {
                unset($conf['on']);
                $config[] = $conf;
            }
            if (isset($conf['except']) && ! $this->isMatchScenario($conf['except'])) {
                unset($conf['except']);
                $config[] = $conf;
            }
        }

        return $config;
    }

    /**
     * 获得指定规则定义
     *
     * @param string $rule
     *
     * @return array|string|callable|false
     */
    protected function getRuleDefine(string $rule)
    {
        if (isset($this->builtinRules[$rule])) {
            return $this->builtinRules[$rule];
        }
        if (isset($this->customRules[$rule])) {
            return $this->customRules[$rule];
        }

        return false;
    }

    /**
     * 判断是否自定义规则
     *
     * @param  callable|string|array $rule
     *
     * @return bool
     */
    protected function isCustomRule($rule): bool
    {
        return Assert::callable($rule) ||
               Assert::namespace($rule);
    }

    /**
     * 判断是否场景是否匹配
     *
     * @param string|array $scenarios
     *
     * @return bool
     */
    protected function isMatchScenario($scenarios): bool
    {
        if (Assert::stringNotEmpty($scenarios)) {
            $scenarios = explode(',', $scenarios);
        }

        return Assert::oneOf(
            $this->scenario,
            array_map('trim', $scenarios)
        );
    }
}
