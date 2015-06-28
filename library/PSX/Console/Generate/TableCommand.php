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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types;
use PSX\Sql\SerializeTrait;
use PSX\Sql\TableInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TableCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TableCommand extends GenerateCommandAbstract
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
			->setName('generate:table')
			->setDescription('Generates a new api controller based on an SQL table')
			->addArgument('namespace', InputArgument::REQUIRED, 'Absolute class name of the class (i.e. Acme\Table\News)')
			->addArgument('table', InputArgument::REQUIRED, 'Creates the table according to the given sql table name')
			->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if true');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$definition = $this->getServiceDefinition($input);
		$table      = $input->getArgument('table');

		$output->writeln('Generating table');

		// create dir
		$path = $definition->getPath();

		if(!$this->isDir($path))
		{
			$output->writeln('Create dir ' . $path);

			if(!$definition->isDryRun())
			{
				$this->makeDir($path);
			}
		}

		// generate controller
		$file = $path . DIRECTORY_SEPARATOR . $definition->getClassName() . '.php';

		if(!$this->isFile($file))
		{
			$source = $this->getTableSource($definition, $table);

			$output->writeln('Write file ' . $file);

			if(!$definition->isDryRun())
			{
				$this->writeFile($file, $source);
			}
		}
		else
		{
			throw new \RuntimeException('File ' . $file . ' already exists');
		}
	}

	protected function getTableSource(ServiceDefinition $definition, $table)
	{
		$namespace = $definition->getNamespace();
		$className = $definition->getClassName();
		$name      = lcfirst($className);

		$sm         = $this->connection->getSchemaManager();
		$columns    = $sm->listTableColumns($table);
		$indexes    = $sm->listTableIndexes($table);
		$properties = array();

		foreach($columns as $column)
		{
			$isPrimary = false;
			foreach($indexes as $index)
			{
				if($index->isPrimary() && in_array($column->getName(), $index->getColumns()))
				{
					$isPrimary = true;
					break;
				}
			}

			$properties[] = $this->convertDoctrineTypeToString($column, $isPrimary);
		}

		$columns = '';

		foreach($properties as $property)
		{
			$columns.= $property . "\n";
		}

		$columns = trim($columns);

		return <<<PHP
<?php

namespace {$namespace};

use PSX\Sql\TableAbstract;

/**
 * {$className}
 *
 * @see http://phpsx.org/doc/concept/table.html
 */
class {$className} extends TableAbstract
{
	public function getName()
	{
		return '{$table}';
	}

	public function getColumns()
	{
		return array(
			{$columns}
		);
	}
}

PHP;
	}

	protected function convertDoctrineTypeToString(Column $column, $isPrimary)
	{
		$type = SerializeTrait::getTypeByDoctrineType($column->getType());
		$name = $column->getName();

		switch($type)
		{
			case TableInterface::TYPE_BIGINT:
				$result = <<<PHP
			'{$name}' => self::TYPE_BIGINT
PHP;
				break;

			case TableInterface::TYPE_BLOB:
				$result = <<<PHP
			'{$name}' => self::TYPE_BLOB
PHP;
				break;

			case TableInterface::TYPE_BOOLEAN:
				$result = <<<PHP
			'{$name}' => self::TYPE_BOOLEAN
PHP;
				break;

			case TableInterface::TYPE_DATETIME:
				$result = <<<PHP
			'{$name}' => self::TYPE_DATETIME
PHP;
				break;

			case TableInterface::TYPE_DATE:
				$result = <<<PHP
			'{$name}' => self::TYPE_DATE
PHP;
				break;

			case TableInterface::TYPE_DECIMAL:
				$result = <<<PHP
			'{$name}' => self::TYPE_DECIMAL
PHP;
				break;

			case TableInterface::TYPE_FLOAT:
				$result = <<<PHP
			'{$name}' => self::TYPE_FLOAT
PHP;
				break;

			case TableInterface::TYPE_INT:
				$result = <<<PHP
			'{$name}' => self::TYPE_INT
PHP;
				break;

			case TableInterface::TYPE_SMALLINT:
				$result = <<<PHP
			'{$name}' => self::TYPE_SMALLINT
PHP;
				break;

			case TableInterface::TYPE_TEXT:
				$result = <<<PHP
			'{$name}' => self::TYPE_TEXT
PHP;
				break;

			case TableInterface::TYPE_ARRAY:
				$result = <<<PHP
			'{$name}' => self::TYPE_ARRAY
PHP;
				break;

			case TableInterface::TYPE_OBJECT:
				$result = <<<PHP
			'{$name}' => self::TYPE_OBJECT
PHP;
				break;

			case TableInterface::TYPE_TIME:
				$result = <<<PHP
			'{$name}' => self::TYPE_TIME
PHP;
				break;

			case TableInterface::TYPE_VARCHAR:
			default:
				$result = <<<PHP
			'{$name}' => self::TYPE_VARCHAR
PHP;
				break;
		}

		if($column->getAutoincrement())
		{
			$result.= ' | self::AUTO_INCREMENT';
		}

		if($isPrimary)
		{
			$result.= ' | self::PRIMARY_KEY';
		}

		return $result . ',';
	}
}
