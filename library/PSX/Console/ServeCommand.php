<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Console;

use PSX\Config;
use PSX\Command\Executor;
use PSX\Command\ParameterParser;
use PSX\Dispatch;
use PSX\Http\RequestParser;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ServeCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
		$scheme = parse_url($this->config['psx_url'], PHP_URL_SCHEME);
		$host   = parse_url($this->config['psx_url'], PHP_URL_HOST);

		// request
		$parser   = new RequestParser($scheme . '://' . $host, RequestParser::MODE_LOOSE);
		$request  = $parser->parse($this->reader->read());

		// response
		$response = new Response('HTTP/1.1');
		$response->addHeader('X-Powered-By', 'psx');
		$response->setBody(new TempStream(fopen('php://output', 'r+')));

		// dispatch request
		$this->dispatch->route($request, $response);

		// determine return code
		return $response->getStatusCode() >= 400 && $response->getStatusCode() < 600 ? 1 : 0;
	}
}
