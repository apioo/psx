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

namespace PSX\Controller\Tool;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PSX\Command\ParameterParser;
use PSX\Controller\ApiAbstract;
use PSX\Data\Record;
use PSX\Loader\Context;

/**
 * CommandController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CommandController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Command\Executor
     */
    protected $executor;

    /**
     * @Inject
     * @var \PSX\Dispatch\CommandFactoryInterface
     */
    protected $commandFactory;

    /**
     * @Inject
     * @var \Monolog\Logger
     */
    protected $logger;

    public function onGet()
    {
        parent::onGet();

        $commandClass = $this->getParameter('command');

        if (!empty($commandClass)) {
            $command    = $this->commandFactory->getCommand($commandClass, new Context());
            $parameters = $command->getParameters();
            $data       = array();

            foreach ($parameters as $parameter) {
                $data[] = new Record('parameter', [
                    'name'        => $parameter->getName(),
                    'description' => $parameter->getDescription(),
                    'type'        => $parameter->getType(),
                ]);
            }

            $this->setBody(array(
                'command'     => $commandClass,
                'description' => $parameters->getDescription(),
                'parameters'  => $data,
            ));
        } else {
            $this->setBody(array(
                'commands' => (object) $this->executor->getAliases(),
            ));
        }
    }

    public function onPost()
    {
        parent::onPost();

        $commandClass = $this->getParameter('command');
        $parameters   = $this->getBody();
        $parameters   = !empty($parameters) ? $parameters : array();

        if (!empty($commandClass)) {
            $stream = fopen('php://memory', 'r+');

            $this->logger->pushHandler(new StreamHandler($stream, Logger::DEBUG));

            $this->executor->run(new ParameterParser\Map($commandClass, $parameters));

            $output = stream_get_contents($stream, -1, 0);

            $this->logger->popHandler();

            $this->setBody(array(
                'output' => $output,
            ));
        } else {
            throw new \Exception('Command not available');
        }
    }
}
