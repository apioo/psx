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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates a single file containing all files which are needed for every PSX
 * request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

        foreach ($files as $file) {
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
    public function getFiles()
    {
        return array(
            PSX_PATH_LIBRARY . '/../vendor/symfony/dependency-injection/Symfony/Component/DependencyInjection/ContainerInterface.php',
            PSX_PATH_LIBRARY . '/../vendor/symfony/event-dispatcher/Symfony/Component/EventDispatcher/EventSubscriberInterface.php',
            PSX_PATH_LIBRARY . '/../vendor/symfony/event-dispatcher/Symfony/Component/EventDispatcher/EventDispatcherInterface.php',
            PSX_PATH_LIBRARY . '/../vendor/symfony/event-dispatcher/Symfony/Component/EventDispatcher/Event.php',
            PSX_PATH_LIBRARY . '/../vendor/symfony/event-dispatcher/Symfony/Component/EventDispatcher/EventDispatcher.php',
            PSX_PATH_LIBRARY . '/../vendor/psr/log/Psr/Log/LoggerInterface.php',
            PSX_PATH_LIBRARY . '/../vendor/psr/log/Psr/Log/LoggerAwareInterface.php',
            PSX_PATH_LIBRARY . '/../vendor/doctrine/annotations/lib/Doctrine/Common/Annotations/AnnotationRegistry.php',
            PSX_PATH_LIBRARY . '/../vendor/monolog/monolog/src/Monolog/Formatter/FormatterInterface.php',
            PSX_PATH_LIBRARY . '/../vendor/monolog/monolog/src/Monolog/Formatter/NormalizerFormatter.php',
            PSX_PATH_LIBRARY . '/../vendor/monolog/monolog/src/Monolog/Handler/HandlerInterface.php',
            PSX_PATH_LIBRARY . '/../vendor/monolog/monolog/src/Monolog/Handler/AbstractHandler.php',
            PSX_PATH_LIBRARY . '/../vendor/monolog/monolog/src/Monolog/Handler/AbstractProcessingHandler.php',
            PSX_PATH_LIBRARY . '/../vendor/monolog/monolog/src/Monolog/Handler/ErrorLogHandler.php',
            PSX_PATH_LIBRARY . '/../vendor/monolog/monolog/src/Monolog/Logger.php',
            PSX_PATH_LIBRARY . '/PSX/Api/DocumentedInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource/ListingInterface.php',
            PSX_PATH_LIBRARY . '/PSX/ApplicationStackInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Bootstrap.php',
            PSX_PATH_LIBRARY . '/PSX/Cache/HandlerInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Cache/CacheItemInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Cache/CacheItemPoolInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Cache/Item.php',
            PSX_PATH_LIBRARY . '/PSX/Cache/Handler/File.php',
            PSX_PATH_LIBRARY . '/PSX/Cache.php',
            PSX_PATH_LIBRARY . '/PSX/Config.php',
            PSX_PATH_LIBRARY . '/PSX/ControllerInterface.php',
            PSX_PATH_LIBRARY . '/PSX/ControllerAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Extractor.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Importer.php',
            PSX_PATH_LIBRARY . '/PSX/Data/ReaderInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/ReaderAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Reader/Form.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Reader/Json.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Reader/Xml.php',
            PSX_PATH_LIBRARY . '/PSX/Data/ReaderFactory.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/FactoryFactory.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/ImporterInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/ImporterManager.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/Importer/Record.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/Importer/Schema.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/Importer/Table.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/VisitorInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/VisitorAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record/GraphTraverser.php',
            PSX_PATH_LIBRARY . '/PSX/Data/RecordInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/RecordAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/RecordInfo.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Record.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/ValidatorInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/SchemaManagerInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Assimilator.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/SchemaManager.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Validator.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/PropertyInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/PropertyAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/PropertySimpleAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/DecimalType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/StringType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/ArrayType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/ComplexType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/BooleanType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/DateType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/DateTimeType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/DurationType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/FloatType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/IntegerType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Property/TimeType.php',
            PSX_PATH_LIBRARY . '/PSX/Data/SchemaInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/SchemaAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Schema/Builder.php',
            PSX_PATH_LIBRARY . '/PSX/Data/TransformerInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Transformer/XmlArray.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Transformer/Atom.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Transformer/Soap.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Transformer/Jsonx.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Transformer/TransformerManager.php',
            PSX_PATH_LIBRARY . '/PSX/Data/WriterInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/TemplateAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/XmlWriterAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/Xml.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/Atom.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/Form.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/Html.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/Json.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/Jsonp.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/Soap.php',
            PSX_PATH_LIBRARY . '/PSX/Data/Writer/Jsonx.php',
            PSX_PATH_LIBRARY . '/PSX/Data/WriterFactory.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/Command.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/Console.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/Container.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/Controller.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/Data.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/DefaultContainer.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/Event.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/ObjectBuilder.php',
            PSX_PATH_LIBRARY . '/PSX/Dependency/ObjectBuilderInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/ApplicationStackFactory.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/ControllerFactoryInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/ControllerFactory.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/Filter/ControllerExecutor.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/FilterChain.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/FilterChainInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/FilterInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/RequestFactory.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/RequestFactoryInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/ResponseFactory.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/ResponseFactoryInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/Sender/Basic.php',
            PSX_PATH_LIBRARY . '/PSX/Dispatch/SenderInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Event.php',
            PSX_PATH_LIBRARY . '/PSX/Event/ControllerExecuteEvent.php',
            PSX_PATH_LIBRARY . '/PSX/Event/ControllerProcessedEvent.php',
            PSX_PATH_LIBRARY . '/PSX/Event/RequestIncomingEvent.php',
            PSX_PATH_LIBRARY . '/PSX/Event/ResponseSendEvent.php',
            PSX_PATH_LIBRARY . '/PSX/Event/RouteMatchedEvent.php',
            PSX_PATH_LIBRARY . '/PSX/Http.php',
            PSX_PATH_LIBRARY . '/PSX/Http/MediaType.php',
            PSX_PATH_LIBRARY . '/PSX/Http/StreamInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Http/MessageInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Http/RequestInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Http/ResponseInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Http/Message.php',
            PSX_PATH_LIBRARY . '/PSX/Http/Request.php',
            PSX_PATH_LIBRARY . '/PSX/Http/Response.php',
            PSX_PATH_LIBRARY . '/PSX/Http/ParserAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Http/ResponseParser.php',
            PSX_PATH_LIBRARY . '/PSX/Http/Stream/StringStream.php',
            PSX_PATH_LIBRARY . '/PSX/Http/Stream/TempStream.php',
            PSX_PATH_LIBRARY . '/PSX/Http/Stream/Util.php',
            PSX_PATH_LIBRARY . '/PSX/Loader.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/CallbackResolver/DependencyInjector.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/CallbackResolverInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/Context.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/LocationFinder/RoutingParser.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/LocationFinderInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/PathMatcher.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/ReverseRouter.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/RoutingCollection.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/RoutingParser/RoutingFile.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/RoutingParser/CachedParser.php',
            PSX_PATH_LIBRARY . '/PSX/Loader/RoutingParserInterface.php',
            PSX_PATH_LIBRARY . '/PSX/LoaderInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Log/ErrorFormatter.php',
            PSX_PATH_LIBRARY . '/PSX/Log/LogListener.php',
            PSX_PATH_LIBRARY . '/PSX/TemplateInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Template.php',
            PSX_PATH_LIBRARY . '/PSX/Template/GeneratorFactoryInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Template/GeneratorFactory.php',
            PSX_PATH_LIBRARY . '/PSX/Uri.php',
            PSX_PATH_LIBRARY . '/PSX/Url.php',
            PSX_PATH_LIBRARY . '/PSX/Util/Annotation.php',
            PSX_PATH_LIBRARY . '/PSX/Util/Annotation/DocBlock.php',
            PSX_PATH_LIBRARY . '/PSX/Util/PriorityQueue.php',
            PSX_PATH_LIBRARY . '/PSX/Validate.php',
            PSX_PATH_LIBRARY . '/PSX/Api/DocumentationInterface.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Documentation/Simple.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource/MethodAbstract.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource/Get.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource/Post.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource/Put.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource/Delete.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource/Factory.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource/Listing/ControllerDocumentation.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Resource.php',
            PSX_PATH_LIBRARY . '/PSX/Api/Version.php',
        );
    }
}
