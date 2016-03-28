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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ViewCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ViewCommand extends ContainerGenerateCommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('generate:view')
            ->setDescription('Generates a new view controller and an template sample')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Absolute class name of the controller (i.e. Acme\News\Overview)')
            ->addArgument('services', InputArgument::OPTIONAL, 'Comma seperated list of service ids (i.e. connection,template)', 'connection,template')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if true');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $definition = $this->getServiceDefinition($input);

        $output->writeln('Generating view controller');

        // create dir
        $path            = $definition->getPath();
        $applicationPath = $path . DIRECTORY_SEPARATOR . 'Application';
        $resourcePath    = $path . DIRECTORY_SEPARATOR . 'Resource';

        if (!$this->isDir($applicationPath)) {
            $output->writeln('Create dir ' . $applicationPath);

            if (!$definition->isDryRun()) {
                $this->makeDir($applicationPath);
            }
        }

        if (!$this->isDir($resourcePath)) {
            $output->writeln('Create dir ' . $resourcePath);

            if (!$definition->isDryRun()) {
                $this->makeDir($resourcePath);
            }
        }

        // generate controller
        $controllerFile = $applicationPath . DIRECTORY_SEPARATOR . $definition->getClassName() . '.php';
        $templateFile   = $resourcePath . DIRECTORY_SEPARATOR . $this->underscore($definition->getClassName()) . '.html';

        if (!$this->isFile($controllerFile) && !$this->isFile($templateFile)) {
            $definition->setNamespace($definition->getNamespace() . '\Application');

            $source = $this->getControllerSource($definition);

            $output->writeln('Write file ' . $controllerFile);

            if (!$definition->isDryRun()) {
                $this->writeFile($controllerFile, $source);
            }

            // template file
            $source = $this->getTemplateSource();

            $output->writeln('Write file ' . $templateFile);

            if (!$definition->isDryRun()) {
                $this->writeFile($templateFile, $source);
            }
        } else {
            if ($this->isFile($controllerFile)) {
                throw new \RuntimeException('File ' . $controllerFile . ' already exists');
            } elseif ($this->isFile($templateFile)) {
                throw new \RuntimeException('File ' . $templateFile . ' already exists');
            }
        }
    }

    protected function getControllerSource(ServiceDefinition $definition)
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

use PSX\Framework\Controller\ViewAbstract;

/**
 * {$className}
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class {$className} extends ViewAbstract
{
	{$services}

	public function doIndex()
	{
		// @TODO controller action

		\$this->setBody(array(
			'message' => 'This is the default controller of PSX',
		));
	}
}

PHP;
    }

    protected function getTemplateSource()
    {
        return <<<'HTML'
<!DOCTYPE>
<html>
<head>
	<title>Controller</title>
</head>
<body>

<p><?php echo $message; ?></p>

</body>
</html>
HTML;
    }
}
