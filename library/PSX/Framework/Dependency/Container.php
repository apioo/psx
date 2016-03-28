<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Framework\Dependency;

use InvalidArgumentException;
use PSX\Framework\Util\Annotation;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ScopeInterface;

/**
 * A simple and fast implementation of an dependency container. Note this
 * implementation does not support nested scopes. You can enter a scope and when
 * you leave the scope you are at the root scope
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Container implements ContainerInterface
{
    protected $services   = array();
    protected $parameters = array();
    protected $scope      = 'container';
    protected $scopes     = array('container');

    public function __construct()
    {
        $this->services[$this->scope]   = array();
        $this->parameters[$this->scope] = array();
    }

    public function set($name, $obj, $scope = 'container')
    {
        if (!in_array($scope, $this->scopes)) {
            throw new InvalidArgumentException('Invalid scope');
        }

        $name = self::normalizeName($name);

        return $this->services[$scope][$name] = $obj;
    }

    public function get($name, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        $name = self::normalizeName($name);

        if (!isset($this->services[$this->scope][$name])) {
            if (method_exists($this, $method = 'get' . $name)) {
                $this->services[$this->scope][$name] = $this->$method();
            } else {
                if ($invalidBehavior == self::EXCEPTION_ON_INVALID_REFERENCE) {
                    throw new ServiceNotFoundException('Service ' . $name . ' not defined');
                } elseif ($invalidBehavior == self::NULL_ON_INVALID_REFERENCE) {
                    return null;
                }
            }
        }

        return $this->services[$this->scope][$name];
    }

    public function has($name)
    {
        $name = self::normalizeName($name);

        return isset($this->services[$this->scope][$name]) || method_exists($this, 'get' . $name);
    }

    public function setParameter($name, $value)
    {
        $name = strtolower($name);

        $this->parameters[$this->scope][$name] = $value;
    }

    public function getParameter($name)
    {
        $name = strtolower($name);

        if ($this->hasParameter($name)) {
            return $this->parameters[$this->scope][$name];
        } else {
            throw new InvalidArgumentException('Parameter ' . $name . ' not set');
        }
    }

    public function hasParameter($name)
    {
        $name = strtolower($name);

        return isset($this->parameters[$this->scope][$name]);
    }

    public function enterScope($name)
    {
        if (!$this->hasScope($name)) {
            throw new InvalidArgumentException('Scope ' . $name . ' does not exist');
        }

        $this->scope = $name;
    }

    public function leaveScope($name)
    {
        $this->scope = 'container';
    }

    public function addScope(ScopeInterface $scope)
    {
        $this->scopes[] = $scope->getName();

        $this->services[$scope->getName()]   = array();
        $this->parameters[$scope->getName()] = array();
    }

    public function hasScope($name)
    {
        return in_array($name, $this->scopes);
    }

    public function isScopeActive($name)
    {
        return $this->scope == $name;
    }

    /**
     * Returns all available service ids of this container
     *
     * @return array
     */
    public function getServiceIds()
    {
        $services  = array();
        $container = new \ReflectionClass($this);

        foreach ($container->getMethods() as $method) {
            if (!in_array($method->name, array('get', 'getParameter', 'getServiceIds', 'getReturnType')) && preg_match('/^get(.+)$/', $method->name, $match)) {
                $services[] = self::underscore($match[1]);
            }
        }

        return $services;
    }

    /**
     * Tries to determine the return type of an service. At first we try to
     * determine the type from the return annotation which is in most cases
     * more useful because it could specify an interface instead of an concrete
     * implementation. As fallback we get an instance of the service and return
     * the type
     *
     * @param string $name
     * @return string
     */
    public function getReturnType($name)
    {
        $container = new ReflectionClass($this);

        try {
            $method = $container->getMethod('get' . self::normalizeName($name));
            $doc    = Annotation::parse($method->getDocComment());
            $return = $doc->getFirstAnnotation('return');

            if (!empty($return)) {
                return $return;
            }
        } catch (ReflectionException $e) {
            // method does not exist
        }

        // as fallback we get the service and return the used type
        $service = $this->get($name);

        if (is_object($service)) {
            return get_class($service);
        } else {
            return gettype($service);
        }
    }

    public static function normalizeName($name)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    public static function underscore($id)
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), strtr($id, '_', '.')));
    }
}
