<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Console\Generate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates a single file containing all files which are needed for every PSX
 * request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BootstrapCacheCommand extends GenerateCommandAbstract
{
	protected function configure()
	{
		$this
			->setName('generate:bootstrap_cache')
			->setDescription('Generates a bootstrap cache file containing common PSX classes');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$files   = $this->getFiles();
		$content = '<?php' . "\n";

		foreach($files as $file)
		{
			$source    = trim(substr(php_strip_whitespace($file), 5));
			$pos       = strpos($source, ';');
			$namespace = substr($source, 0, $pos);
			$source    = $namespace . ' { ' . trim(substr($source, $pos + 1)) . ' } ' . "\n";

			$content.= $source;
		}

		$this->writeFile(PSX_PATH_CACHE . '/bootstrap.cache.php', $content);

		$output->writeln('Generation successful');
	}

	/**
	 * All files which get included into the bootstrap cache. Note dont include
	 * controller/command classes which contain annotations since all comments  
	 * get removed
	 *
	 * @codeCoverageIgnore
	 */
	protected function getFiles()
	{
		return array(
			PSX_PATH_LIBRARY . '/PSX/Dependency/Container.php',
			PSX_PATH_LIBRARY . '/PSX/Dependency/Command.php',
			PSX_PATH_LIBRARY . '/PSX/Dependency/Console.php',
			PSX_PATH_LIBRARY . '/PSX/Dependency/Controller.php',
			PSX_PATH_LIBRARY . '/PSX/Dependency/Data.php',
			PSX_PATH_LIBRARY . '/PSX/Dependency/Event.php',
			PSX_PATH_LIBRARY . '/PSX/Dependency/DefaultContainer.php',
			PSX_PATH_LIBRARY . '/PSX/Bootstrap.php',
			PSX_PATH_LIBRARY . '/PSX/Config.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch/RequestFactoryInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch/RequestFactory.php',
			PSX_PATH_LIBRARY . '/PSX/Url.php',
			PSX_PATH_LIBRARY . '/PSX/Uri.php',
			PSX_PATH_LIBRARY . '/PSX/Http/Message.php',
			PSX_PATH_LIBRARY . '/PSX/Http/Request.php',
			PSX_PATH_LIBRARY . '/PSX/Http/HeaderFieldValues.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch/ResponseFactoryInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch/ResponseFactory.php',
			PSX_PATH_LIBRARY . '/PSX/Http/Response.php',
			PSX_PATH_LIBRARY . '/PSX/Http/Stream/TempStream.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch.php',
			PSX_PATH_LIBRARY . '/PSX/LoaderInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Loader.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/LocationFinderInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/LocationFinder/RoutingParser.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/RoutingParserInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/RoutingParser/RoutingFile.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/CallbackResolverInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/CallbackResolver/DependencyInjector.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch/ControllerFactoryInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch/ControllerFactory.php',
			PSX_PATH_LIBRARY . '/PSX/Dependency/ObjectBuilderInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Dependency/ObjectBuilder.php',
			PSX_PATH_LIBRARY . '/PSX/Event.php',
			PSX_PATH_LIBRARY . '/PSX/Event/RouteMatchedEvent.php',
			PSX_PATH_LIBRARY . '/PSX/Event/ControllerExecuteEvent.php',
			PSX_PATH_LIBRARY . '/PSX/Event/ControllerProcessedEvent.php',
			PSX_PATH_LIBRARY . '/PSX/Event/ResponseSendEvent.php',
			PSX_PATH_LIBRARY . '/PSX/Event/RequestIncomingEvent.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch/SenderInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Dispatch/Sender/Basic.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/RoutingCollection.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/PathMatcher.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/Location.php',
			PSX_PATH_LIBRARY . '/PSX/ControllerInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Util/Annotation.php',
			PSX_PATH_LIBRARY . '/PSX/Util/Annotation/DocBlock.php',
			PSX_PATH_LIBRARY . '/PSX/Validate.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/ReverseRouter.php',
			PSX_PATH_LIBRARY . '/PSX/Data/ReaderFactory.php',
			PSX_PATH_LIBRARY . '/PSX/Data/ReaderInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Data/ReaderAbstract.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Reader/Json.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Reader/Form.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Reader/Xml.php',
			PSX_PATH_LIBRARY . '/PSX/Data/WriterFactory.php',
			PSX_PATH_LIBRARY . '/PSX/Data/WriterInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Writer/ArrayAbstract.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Writer/Json.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Writer/Html.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Writer/Xml.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Writer/Atom.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Writer/Form.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Writer/Jsonp.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Writer/Soap.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Importer.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Extractor.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Transformer/TransformerManager.php',
			PSX_PATH_LIBRARY . '/PSX/Data/TransformerInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Transformer/XmlArray.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Transformer/Atom.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Transformer/Soap.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Schema/ValidatorInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Schema/Validator.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Record/FactoryFactory.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Record/ImporterManager.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Record/ImporterInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Record/Importer/Record.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Record/Importer/Schema.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Record/Importer/Table.php',
			PSX_PATH_LIBRARY . '/PSX/Loader/Callback.php',
			PSX_PATH_LIBRARY . '/PSX/Data/RecordInterface.php',
			PSX_PATH_LIBRARY . '/PSX/Data/RecordAbstract.php',
			PSX_PATH_LIBRARY . '/PSX/Data/Record.php',
			PSX_PATH_LIBRARY . '/PSX/Data/RecordInfo.php',
			PSX_PATH_LIBRARY . '/PSX/Http.php',
			PSX_PATH_LIBRARY . '/PSX/Http/ParserAbstract.php',
			PSX_PATH_LIBRARY . '/PSX/Http/ResponseParser.php',
		);
	}
}
