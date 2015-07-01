<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Console\Generate;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CommandCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CommandCommand extends ContainerGenerateCommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('generate:command')
            ->setDescription('Generates a new command')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Absolute class name of the command (i.e. Acme\News\Overview)')
            ->addArgument('services', InputArgument::OPTIONAL, 'Comma seperated list of service ids (i.e. connection,http)')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if true');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $definition = $this->getServiceDefinition($input);

        $output->writeln('Generating command');

        // create dir
        $path = $definition->getPath();

        if (!$this->isDir($path)) {
            $output->writeln('Create dir ' . $path);

            if (!$definition->isDryRun()) {
                $this->makeDir($path);
            }
        }

        // generate controller
        $file = $path . DIRECTORY_SEPARATOR . $definition->getClassName() . '.php';

        if (!$this->isFile($file)) {
            $source = $this->getCommandSource($definition);

            $output->writeln('Write file ' . $file);

            if (!$definition->isDryRun()) {
                $this->writeFile($file, $source);
            }
        } else {
            throw new \RuntimeException('File ' . $file . ' already exists');
        }
    }

    protected function getCommandSource(ServiceDefinition $definition)
    {
        $namespace = $definition->getNamespace();
        $className = $definition->getClassName();
        $services  = '';

        foreach ($definition->getServices() as $serviceName) {
            $services.= $this->getServiceSource($serviceName) . "\n\n";
        }

        $services = trim($services);

        return <<<PHP
<?php

namespace {$namespace};

use PSX\CommandAbstract;
use PSX\Command\Parameter;
use PSX\Command\Parameters;
use PSX\Command\OutputInterface;

/**
 * {$className}
 *
 * @see http://phpsx.org/doc/design/command.html
 */
class {$className} extends CommandAbstract
{
	{$services}

	public function onExecute(Parameters \$parameters, OutputInterface \$output)
	{
		\$foo = \$parameters->get('foo');
		\$bar = \$parameters->get('bar');

		// @TODO do a task

		\$output->writeln('This is an PSX sample command');
	}

	public function getParameters()
	{
		return \$this->getParameterBuilder()
			->setDescription('Displays informations about an foo command')
			->addOption('foo', Parameter::TYPE_REQUIRED, 'The foo parameter')
			->addOption('bar', Parameter::TYPE_OPTIONAL, 'The bar parameter')
			->getParameters();
	}
}

PHP;
    }
}
