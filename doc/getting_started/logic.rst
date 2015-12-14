
Logic
=====

In the previous chapter we have defined a schema for our API. This chapter 
covers how we can create the logic for our API which consumes the request and 
creates a response.

Using services
--------------

Services are the building blocks of our application. In order to use a service 
from the DI container you can use the "Inject" annotation to include a service 
into your controller. In the following an example controller which uses a 
doctrine DBAL connection to fetch data from a table.

.. code-block:: php

    <?php

    namespace Foo;

    use PSX\Api\Documentation\Parser\Raml;
    use PSX\Api\Version;
    use PSX\Controller\SchemaApiAbstract;
    use PSX\Data\RecordInterface;
    use PSX\Loader\Context;

    class Endpoint extends SchemaApiAbstract
    {
    	/**
    	 * @Inject
    	 * @var Doctrine\DBAL\Connection
    	 */
    	protected $connection;

        public function getDocumentation()
        {
            return Raml::fromFile(__DIR__ . '/endpoint.raml', $this->context->get(Context::KEY_PATH));
        }

        protected function doGet(Version $version)
        {
            $count  = $this->queryParameters->getProperty('count') ?: 8;
            $result = $this->connection->fetchAll('SELECT id, title, content FROM acme_table LIMIT :count', array(
            	'count' => $count
            ));

            return [
                'entry' => $result,
            ];
        }
    }

By default PSX comes with the following registered services which can be used 
inside a controller or command.

.. code-block:: text

    config                    PSX\Config
    http                      PSX\Http
    session                   PSX\Session
    connection                Doctrine\DBAL\Connection
    template                  PSX\TemplateInterface
    validate                  PSX\Validate
    object_builder            PSX\Dependency\ObjectBuilderInterface
    cache                     PSX\Cache\CacheItemPoolInterface
    logger                    Psr\Log\LoggerInterface
    table_manager             PSX\Sql\TableManagerInterface
    entity_manager            Doctrine\ORM\EntityManager
    command_factory           PSX\Dispatch\CommandFactoryInterface
    command_output            PSX\Command\OutputInterface
    executor                  PSX\Command\Executor
    console                   Symfony\Component\Console\Application
    console_reader            PSX\Console\ReaderInterface
    application_stack_factory PSX\Dispatch\ControllerFactoryInterface
    controller_factory        PSX\Dispatch\ControllerFactoryInterface
    dispatch_sender           PSX\Dispatch\SenderInterface
    loader_location_finder    PSX\Loader\LocationFinderInterface
    loader_callback_resolver  PSX\Loader\CallbackResolverInterface
    loader                    PSX\Loader
    request_factory           PSX\Dispatch\RequestFactoryInterface
    response_factory          PSX\Dispatch\ResponseFactoryInterface
    dispatch                  PSX\Dispatch
    routing_parser            PSX\Loader\RoutingParserInterface
    reverse_router            PSX\Loader\ReverseRouter
    resource_listing          PSX\Api\Resource\ListingInterface
    reader_factory            PSX\Data\ReaderFactory
    writer_factory            PSX\Data\WriterFactory
    transformer_manager       PSX\Data\Transformer\TransformerManager
    importer_manager          PSX\Data\Record\ImporterManager
    schema_manager            PSX\Data\Schema\SchemaManagerInterface
    schema_validator          PSX\Data\Schema\ValidatorInterface
    schema_assimilator        PSX\Data\Schema\Assimilator
    record_factory_factory    PSX\Data\Record\FactoryFactory
    importer                  PSX\Data\Importer
    extractor                 PSX\Data\Extractor
    serializer                PSX\Data\SerializerInterface
    event_dispatcher          Symfony\Component\EventDispatcher\EventDispatcherInterface

A current list of services can also be generated with the following command.

.. code::

    vendor\bin\psx container

Adding a new service
--------------------

In order to add a new service to the DI container you have to add a method to 
the container class. In the following an example which creates a new service:

.. code-block:: php

    class Container extends DefaultContainer
    {
        /**
         * @return Acme\ServiceInterface
         */
        public function getAcmeService()
        {
            return new Acme\Serivce();
        }
    }

This service can then be used in a controller. Since PSX uses composer you can 
require other packages to use them as a service.

