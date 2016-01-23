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

namespace PSX\Console;

use PSX\Command\Executor;
use PSX\Command\ParameterParser;
use Symfony\Component\Console\Command\Command;
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
class CommandCommand extends Command
{
    protected $executor;
    protected $reader;

    public function __construct(Executor $executor, ReaderInterface $reader)
    {
        parent::__construct();

        $this->executor = $executor;
        $this->reader   = $reader;
    }

    public function setReader(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    protected function configure()
    {
        $this
            ->setName('command')
            ->setDescription('Executes an command through the console. The parameters must be either provided as JSON via stdin or per parameter')
            ->addArgument('cmd', InputArgument::REQUIRED, 'Name of the command')
            ->addArgument('parameters', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Needed parameters for the command')
            ->addOption('stdin', 's', InputOption::VALUE_NONE, 'Whether to read from stdin');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command    = $input->getArgument('cmd');
        $parameters = $input->getArgument('parameters');

        if ($input->getOption('stdin')) {
            $body = $this->reader->read();
            if (empty($body)) {
                $body = '{}';
            }

            $parser = new ParameterParser\Json($command, $body);
        } else {
            $map = array();
            foreach ($parameters as $parameter) {
                $parts = explode(':', $parameter, 2);
                $key   = $parts[0];
                $value = isset($parts[1]) ? $parts[1] : null;

                $map[$key] = $value;
            }

            $parser = new ParameterParser\Map($command, $map);
        }

        $this->executor->run($parser);
    }
}
