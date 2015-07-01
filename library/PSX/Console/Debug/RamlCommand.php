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

namespace PSX\Console\Debug;

use PSX\Api\Documentation\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RamlCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('debug:raml')
            ->setDescription('Parses an RAML file and prints the resource definition')
            ->addArgument('file', InputArgument::REQUIRED, 'The RAML file')
            ->addArgument('path', InputArgument::REQUIRED, 'The path which you want parse')
            ->addArgument('format', InputArgument::OPTIONAL, 'Optional the output format either php or serialize');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $documentation = Parser\Raml::fromFile($input->getArgument('file'), $input->getArgument('path'));

        switch ($input->getArgument('format')) {
            case 'serialize':
                $output->write(serialize($documentation));
                break;

            case 'php':
            default:
                $output->write(var_export($documentation, true));
                break;
        }
    }
}
