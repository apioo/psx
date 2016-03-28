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

namespace PSX\Framework\Console\Generate;

use PSX\Framework\Dependency\Container as PSXContainer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ContainerGenerateCommandAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ContainerGenerateCommandAbstract extends GenerateCommandAbstract
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    protected function getServiceDefinition(InputInterface $input)
    {
        $definition = parent::getServiceDefinition($input);
        $services   = $input->getArgument('services');

        if (!empty($services)) {
            $services = $this->getAvailableServices(explode(',', $services));
        } else {
            $services = array();
        }

        $definition->setServices($services);

        return $definition;
    }

    protected function getServiceSource($serviceId)
    {
        if ($this->container instanceof PSXContainer) {
            $returnType = $this->container->getReturnType($serviceId);
        } else {
            $service = $this->container->get($serviceId);

            if (is_object($service)) {
                $returnType = get_class($service);
            } else {
                $returnType = gettype($service);
            }
        }

        return <<<PHP
	/**
	 * @Inject
	 * @var {$returnType}
	 */
	protected \${$serviceId};
PHP;
    }

    protected function getAvailableServices(array $services)
    {
        $result = array();

        foreach ($services as $service) {
            $service = trim($service);

            if ($this->container->has($service)) {
                $result[] = $service;
            } else {
                throw new \InvalidArgumentException('Given service ' . $service . ' not found');
            }
        }

        return $result;
    }

    protected function underscore($word)
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
    }
}
