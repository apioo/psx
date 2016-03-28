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

namespace PSX\Framework\Console;

use PSX\Framework\Command\ParameterParser;
use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Http\RequestParser;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Uri\Url;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ServeCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ServeCommand extends Command
{
    protected $config;
    protected $dispatch;
    protected $reader;

    public function __construct(Config $config, Dispatch $dispatch, ReaderInterface $reader)
    {
        parent::__construct();

        $this->config   = $config;
        $this->dispatch = $dispatch;
        $this->reader   = $reader;
    }

    public function setReader(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    protected function configure()
    {
        $this
            ->setName('serve')
            ->setDescription('Accepts an HTTP request via stdin and returns the HTTP response');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // request
        $baseUrl = new Url($this->config['psx_url']);
        $baseUrl = $baseUrl->withPath(null);

        $parser   = new RequestParser($baseUrl, RequestParser::MODE_LOOSE);
        $request  = $parser->parse($this->reader->read());

        // response
        $response = new Response();
        $response->setHeader('X-Powered-By', 'psx');
        $response->setBody(new TempStream(fopen('php://memory', 'r+')));

        // dispatch request
        $this->dispatch->route($request, $response);

        // determine return code
        return $response->getStatusCode() >= 400 && $response->getStatusCode() < 600 ? 1 : 0;
    }
}
