<?php

namespace Sport\LobsterBundle\Tests\Entity;

class GenericEntityTest extends \PHPUnit_Framework_TestCase
{
    private $namespace;

    private $entityClasses = array();

    public function __construct()
    {
        parent::__construct();
        $nsClass         = new \ReflectionClass($this);
        $this->namespace = str_replace('\Tests', '', $nsClass->getNamespaceName());
    }

    public function setUp()
    {
        $path = realpath(__DIR__ . '/../../Entity');
        if (empty($path)) {
            echo("Entity directory not correctly configured");
            exit;
        }

        $dir = new \DirectoryIterator($path);
        /** @var \DirectoryIterator $file */
        foreach ($dir as $file) {
            if (!$file->isDot()) {
                $class = $file->getFilename();
                $parts = explode('.', $class);
                if (!empty($parts[0]) && !preg_match('/Repository/', $class)) {
                    $class                       = "{$this->namespace}\\{$parts[0]}";
                    $this->entityClasses[$class] = $this->reflectClass($class);
                }
            }
        }
    }

    private function reflectClass($class)
    {
        $reflector = new \ReflectionClass($class);
        $list      = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods   = $this->filterMethods($class, $list);

        return $this->collectProperties($methods);
    }

    private function filterMethods($class, $list)
    {
        $methods = array();
        foreach ($list as $method) {
            $matches = array();
            $text    = $method->getDocComment();
            $name    = $method->getName();
            if ($method->getDeclaringClass()->name === $class) {
                if (preg_match('/^set(.*)/', $name, $matches)) {
                    $methods[$matches[1]]['set']  = true;
                    $methods[$matches[1]]['type'] = $this->getType($text);
                } elseif (preg_match('/^add(.*)/', $name, $matches)) {
                    $methods[$matches[1]]['add']  = true;
                    $methods[$matches[1]]['type'] = $this->getType($text);
                } elseif (preg_match('/^is(.*)/', $name, $matches)) {
                    $methods[$matches[1]]['is'] = true;
                }
            }
        }

        return $methods;
    }

    private function getType($text)
    {
        $matches = array();
        preg_match('/@param (\S+) /m', $text, $matches);
        $type = null;
        if (!empty($matches)) {
            $type = $matches[1];
        }

        return $type;
    }

    private function collectProperties($methods)
    {
        $ret = array();
        foreach ($methods as $name => $method) {
            if (@$method['is'] && @$method['set']) {
                $ret['boolean'][$name] = $method['type'];
            } elseif (@$method['add']) {
                $ret['collections'][$name] = $method['type'];
            } elseif (@$method['set']) {
                $ret['basic'][$name] = $method['type'];
            }
        }

        return $ret;
    }

    public function testBasicGettersAndSetters()
    {
        foreach ($this->entityClasses as $class => $properties) {
            $testObject = new $class();
            $this->assertNull($testObject->getId(), 'Object has ID');
            if (@$properties['basic']) {
                foreach ($properties['basic'] as $name => $type) {
                    $value = null;
                    switch ($type) {
                        case 'integer':
                            $value = 1;
                            break;
                        case 'float':
                            $value = 1.23;
                            break;
                        case 'string':
                            $value = 'string';
                            break;
                        case 'boolean':
                            $value = true;
                            break;
                        default:
                            $class = ($type[0] !== '\\') ? $this->namespace . '\\' . $type : $type;
                            if (class_exists($class)) {
                                $value = new $class();
                            }
                            break;
                    }
                    if (!is_null($value)) {
                        $this->getAndSetValue($testObject, $name, $type, $value);
                    } else {
                        //$this->fail('Unknown type');
                    }
                }
            }

            if (@$properties['boolean']) {
                foreach ($properties['boolean'] as $name => $type) {
                    $setter = "set{$name}";
                    $getter = "is{$name}";
                    $ret    = $testObject->$setter(true);
                    $this->assertInstanceOf($class, $ret, "Setter {$setter} for {$class} does not return itself");
                    $ret = $testObject->$getter();
                    $this->assertTrue($ret, "Getter {$getter} for {$class} doesn't return a boolean value");
                }
            }
        }
    }

    private function getAndSetValue($testObject, $name, $type, $value)
    {
        $setter    = "set{$name}";
        $getter    = "get{$name}";
        $ret       = $testObject->$setter($value);
        $testClass = get_class($testObject);
        $this->assertInstanceOf($testClass, $ret, "Setter {$setter} for {$testClass} does not return itself");
        $ret = $testObject->$getter();
        $this->assertEquals($value, $ret, "Getter {$getter} for {$testClass} does not return an {$type} value");
    }

    public function testCollections()
    {
        foreach ($this->entityClasses as $class => $properties) {
            if (isset($properties['collections'])) {
                $testClass = new $class();
                foreach ($properties['collections'] as $name => $type) {
                    $object  = $this->namespace . '\\' . $type;
                    $subject = new $object();
                    $getter  = "get{$name}";
                    if (!method_exists($testClass, $getter)) {
                        $getter .= 's';
                    }
                    if (!method_exists($testClass, $getter)) {
                        $getter = preg_replace('/trys$/', 'tries', $getter);
                    }
                    if (method_exists($testClass, $getter)) {
                        $adder   = "add{$name}";
                        $remover = "remove{$name}";
                        $this->assertEquals(0, count($testClass->$getter()));
                        $ret = $testClass->$adder($subject);
                        $this->assertInstanceOf($class, $ret);
                        $ret = $testClass->$getter();
                        $this->assertEquals(1, count($ret));
                        $this->assertInstanceOf($object, $ret[0]);
                        $testClass->$remover($subject);
                        $this->assertEquals(0, count($testClass->$getter()));
                        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $testClass->$getter());
                    }
                }
            }
        }
    }
}
