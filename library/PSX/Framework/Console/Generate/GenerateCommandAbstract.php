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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * GenerateCommandAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class GenerateCommandAbstract extends Command
{
    protected function getServiceDefinition(InputInterface $input)
    {
        $namespace = $input->getArgument('namespace');
        $dryRun    = $input->getOption('dry-run');

        $parts     = explode('\\', $namespace);
        $namespace = implode('\\', array_map('ucfirst', array_slice($parts, 0, count($parts) - 1)));
        $class     = end($parts);

        if (empty($namespace) || empty($class)) {
            throw new \InvalidArgumentException('Namespace must have at least an vendor and class name i.e. Acme\News');
        }

        return new ServiceDefinition($namespace, $class, $dryRun);
    }

    protected function makeDir($path)
    {
        return mkdir($path, 0744, true);
    }

    protected function writeFile($file, $content)
    {
        return file_put_contents($file, $content);
    }

    protected function isFile($path)
    {
        return is_file($path);
    }

    protected function isDir($path)
    {
        return is_dir($path);
    }
}
