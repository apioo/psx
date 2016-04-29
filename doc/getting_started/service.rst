
Service
=======

In the previous chapter we have defined a controller which receives the request
and produces the response. This chapter describes where we can define the 
business logic of our API endpoint.

Definition
----------

> Put simply, a Service is any PHP object that performs some sort of "global"
> task. It's a purposefully-generic name used in computer science to describe an
> object that's created for a specific purpose (e.g. delivering emails). Each
> service is used throughout your application whenever you need the specific
> functionality it provides. You don't have to do anything special to make a
> service: simply write a PHP class with some code that accomplishes a specific
> task. Congratulations, you've just created a service!
>
> So what's the big deal then? The advantage of thinking about "services" is
> that you begin to think about separating each piece of functionality in your
> application into a series of services. Since each service does just one job,
> you can easily access each service and use its functionality wherever you need
> it. Each service can also be more easily tested and configured since it's
> separated from the other functionality in your application. This idea is
> called service-oriented architecture and is not unique to Symfony or even PHP.
> Structuring your application around a set of independent service classes is a
> well-known and trusted object-oriented best-practice. These skills are key to
> being a good developer in almost any language.

This description was copied from the symfony documentation [#f1]_

Usage
-----

It is recommended to place your API logic inside a service. In the controller
you only need to call service methods which return the response data or handle
the request data. The service should be independent of the HTTP request/response
context. Only the controller needs to read the HTTP data and passes it to the
service methods.

In order to use a service from the DI container you can use the `Inject`
annotation to include a service into your controller. In the following we extend
our previous controller with a service which fetches/inserts data on a table.

.. code-block:: php

    <?php

    namespace PSX\Project;

    use PSX\Framework\Controller\AnnotationApiAbstract;
    use PSX\Record\RecordInterface;

    /**
     * @Title("Endpoint")
     * @PathParam(name="foo_id", type="integer")
     */
    class Endpoint extends AnnotationApiAbstract
    {
    	/**
    	 * @Inject
    	 * @var \Acme\NewsService
    	 */
    	protected $news;

        /**
         * @QueryParam(name="count", description="Count of comments")
         * @Outgoing(code=200, schema="schema/song.json")
         */
        protected function doGet()
        {
            return $this->news->getSongById(
                $this->getUriFragment('foo_id'),
                $this->queryParameters->getProperty('count')
            );
        }

        /**
         * @Incoming(schema="schema/song.json")
         * @Outgoing(code=201, schema="schema/message.json")
         */
        protected function doPost($record)
        {
            $this->news->createSong(
                $record->title,
                $record->artist,
            );

            return [
                'success' => true,
                'message' => 'Successful created',
            ];
        }
    }

Register
^^^^^^^^

In order to add a new service to the DI container you have to add a method to
the container class. In the following an example which creates a new service:

.. code-block:: php

    class Container extends DefaultContainer
    {
        /**
         * @return \Acme\ServiceInterface
         */
        public function getAcmeService()
        {
            return new Acme\Serivce();
        }
    }

This service can then be used in a controller.

.. code-block:: php

    <?php

    class Endpoint extends SchemaApiAbstract
    {
        /**
         * @Inject
         * @var \Acme\ServiceInterface
         */
        protected $acmeService;
    }

Command
-------

By default PSX comes with the following registered services which can be used 
inside a controller:

.. code-block:: text

    annotation_reader            \Doctrine\Common\Annotations\Reader
    annotation_reader_controller \Doctrine\Common\Annotations\Reader
    api_parser                   \PSX\Api\ParserInterface
    application_stack_factory    \PSX\Framework\Dispatch\ControllerFactoryInterface
    cache                        \Psr\Cache\CacheItemPoolInterface
    config                       \PSX\Framework\Config\Config
    connection                   \Doctrine\DBAL\Connection
    console                      \Symfony\Component\Console\Application
    console_reader               \PSX\Framework\Console\ReaderInterface
    controller_factory           \PSX\Framework\Dispatch\ControllerFactoryInterface
    dispatch                     \PSX\Framework\Dispatch\Dispatch
    dispatch_sender              \PSX\Framework\Dispatch\SenderInterface
    event_dispatcher             \Symfony\Component\EventDispatcher\EventDispatcherInterface
    exception_converter          \PSX\Framework\Exception\ConverterInterface
    http_client                  \PSX\Http\ClientInterface
    io                           \PSX\Data\Processor
    loader                       \PSX\Framework\Loader\Loader
    loader_callback_resolver     \PSX\Framework\Loader\CallbackResolverInterface
    loader_location_finder       \PSX\Framework\Loader\LocationFinderInterface
    logger                       \Psr\Log\LoggerInterface
    object_builder               \PSX\Framework\Dependency\ObjectBuilderInterface
    request_factory              \PSX\Framework\Dispatch\RequestFactoryInterface
    resource_listing             \PSX\Api\ListingInterface
    response_factory             \PSX\Framework\Dispatch\ResponseFactoryInterface
    reverse_router               \PSX\Framework\Loader\ReverseRouter
    routing_parser               \PSX\Framework\Loader\RoutingParserInterface
    schema_manager               \PSX\Schema\SchemaManagerInterface
    session                      \PSX\Framework\Session\Session
    table_manager                \PSX\Sql\TableManagerInterface
    template                     \PSX\Framework\Template\TemplateInterface
    validate                     \PSX\Validate\Validate

A current list of services can also be generated with the following command.

.. code::

    vendor\bin\psx container

.. rubric:: Footnotes

.. [#f1] http://symfony.com/doc/current/book/service_container.html

