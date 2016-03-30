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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SchemaCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SchemaCommand extends GenerateCommandAbstract
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();

        $this->connection = $connection;
    }

    protected function configure()
    {
        $this
            ->setName('generate:schema')
            ->setDescription('Generates a new schema')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Absolute class name of the schema (i.e. Acme\News\Overview)')
            ->addArgument('table', InputArgument::OPTIONAL, 'Creates the schema according to the given sql table name')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if true');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $definition = $this->getServiceDefinition($input);
        $table      = $input->getArgument('table');

        $output->writeln('Generating schema');

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
            $source = $this->getSchemaSource($definition, $table);

            $output->writeln('Write file ' . $file);

            if (!$definition->isDryRun()) {
                $this->writeFile($file, $source);
            }
        } else {
            throw new \RuntimeException('File ' . $file . ' already exists');
        }
    }

    protected function getSchemaSource(ServiceDefinition $definition, $table)
    {
        $namespace = $definition->getNamespace();
        $className = $definition->getClassName();
        $name      = lcfirst($className);

        if (!empty($table)) {
            $sm         = $this->connection->getSchemaManager();
            $columns    = $sm->listTableColumns($table);
            $properties = array();

            foreach ($columns as $column) {
                $type = $this->convertDoctrinTypeToString($column->getType());

                $properties[] = $this->getSchemaType($type, $column->getName());
            }
        } else {
            $properties = array(
                $this->getSchemaType('integer', 'id'),
                $this->getSchemaType('string', 'title'),
                $this->getSchemaType('dateTime', 'date'),
            );
        }

        $definition = '';

        foreach ($properties as $property) {
            $definition.= $property . "\n";
        }

        $definition = trim($definition);

        return <<<PHP
<?php

namespace {$namespace};

use PSX\Schema\SchemaAbstract;

/**
 * {$className}
 *
 * @see http://phpsx.org/doc/concept/schema.html
 */
class {$className} extends SchemaAbstract
{
	public function getDefinition()
	{
		\$sb = \$this->getSchemaBuilder('{$name}');
		{$definition}

		return \$sb->getProperty();
	}
}

PHP;
    }

    protected function getSchemaType($type, $name)
    {
        switch ($type) {
            case 'integer':
                return <<<PHP
		\$sb->integer('{$name}');
PHP;
                break;

            case 'dateTime':
                return <<<PHP
		\$sb->dateTime('{$name}');
PHP;
                break;

            case 'boolean':
                return <<<PHP
		\$sb->boolean('{$name}');
PHP;
                break;

            case 'float':
                return <<<PHP
		\$sb->float('{$name}');
PHP;
                break;

            case 'string':
            default:
                return <<<PHP
		\$sb->string('{$name}');
PHP;
                break;
        }
    }

    protected function convertDoctrinTypeToString(Types\Type $type)
    {
        switch (true) {
            case $type instanceof Types\BigIntType:
            case $type instanceof Types\IntegerType:
            case $type instanceof Types\SmallIntType:
                return 'integer';

            case $type instanceof Types\DateTimeType:
            case $type instanceof Types\DateTimeTzType:
                return 'dateTime';

            case $type instanceof Types\BooleanType:
                return 'boolean';

            case $type instanceof Types\FloatType:
            case $type instanceof Types\DecimalType:
                return 'float';

            case $type instanceof Types\StringType:
            default:
                return 'string';
        }
    }
}
