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

use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ApiCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiCommand extends ContainerGenerateCommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('generate:api')
            ->setDescription('Generates a new api controller')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Absolute class name of the controller (i.e. Acme\News\Overview)')
            ->addArgument('services', InputArgument::OPTIONAL, 'Comma seperated list of service ids', 'connection,schemaManager')
            ->addOption('raml', null, InputOption::VALUE_OPTIONAL, 'Absolute path to an RAML specification (i.e. ./schema.raml)')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if true');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $definition = $this->getServiceDefinition($input);
        $ramlFile   = $input->getOption('raml');

        $output->writeln('Generating api controller');

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
            $source = $this->getControllerSource($definition, $path, $ramlFile);

            $output->writeln('Write file ' . $file);

            if (!$definition->isDryRun()) {
                $this->writeFile($file, $source);

                $output->writeln('');
                $output->writeln('Add an entry to your routes file (' . $this->container->get('config')->get('psx_routing') . ')');
                $output->writeln('to make the endpoint accessible i.e.:');
                $output->writeln('');
                $output->writeln('GET|POST|PUT|DELETE /acme/api ' . $definition->getClass());
                $output->writeln('');
            }
        } else {
            throw new RuntimeException('File ' . $file . ' already exists');
        }
    }

    protected function getControllerSource(ServiceDefinition $definition, $basePath, $ramlFile)
    {
        $namespace = $definition->getNamespace();
        $className = $definition->getClassName();
        $services  = '';

        foreach ($definition->getServices() as $serviceName) {
            $services.= $this->getServiceSource($serviceName) . "\n\n";
        }

        $services = trim($services);

        // documentation
        $documentation = '';
        if (!empty($ramlFile)) {
            if (!$this->isFile($basePath . '/' . $ramlFile)) {
                throw new RuntimeException('RAML file "' . $basePath . '/' . $ramlFile . '" does not exist');
            }

            $documentation = <<<PHP
return Documentation\Parser\Raml::fromFile(__DIR__ . '/{$ramlFile}', \$this->context->get(Context::KEY_PATH));
PHP;
        } else {
            $documentation = <<<PHP
\$resource = new Resource(Resource::STATUS_ACTIVE, \$this->context->get(Context::KEY_PATH));

		\$resource->addMethod(Resource\Factory::getMethod('GET')
			->addResponse(200, \$this->schemaManager->getSchema('{$namespace}\Schema\Collection')));

		return \$resource;
PHP;
        }

        // controller
        return <<<PHP
<?php

namespace {$namespace};

use PSX\Framework\Api\Documentation;
use PSX\Framework\Api\Resource;
use PSX\Framework\Api\Version;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Data\RecordInterface;
use PSX\Framework\Loader\Context;

/**
 * {$className}
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class {$className} extends SchemaApiAbstract
{
	{$services}

	/**
	 * @return \PSX\Api\Resource
	 */
	public function getDocumentation()
	{
		{$documentation}
	}

	/**
	 * Returns the GET response
	 *
	 * @param \PSX\Api\Version \$version
	 * @return array|\PSX\Data\RecordInterface
	 */
	protected function doGet(Version \$version)
	{
		return array(
			'message' => 'This is the default controller of PSX'
		);
	}

	/**
	 * Returns the POST response
	 *
	 * @param \PSX\Data\RecordInterface \$record
	 * @param \PSX\Api\Version \$version
	 * @return array|\PSX\Data\RecordInterface
	 */
	protected function doPost(RecordInterface \$record, Version \$version)
	{
	}

	/**
	 * Returns the PUT response
	 *
	 * @param \PSX\Data\RecordInterface \$record
	 * @param \PSX\Api\Version \$version
	 * @return array|\PSX\Data\RecordInterface
	 */
	protected function doPut(RecordInterface \$record, Version \$version)
	{
	}

	/**
	 * Returns the DELETE response
	 *
	 * @param \PSX\Data\RecordInterface \$record
	 * @param \PSX\Api\Version \$version
	 * @return array|\PSX\Data\RecordInterface
	 */
	protected function doDelete(RecordInterface \$record, Version \$version)
	{
	}
}

PHP;
    }
}
