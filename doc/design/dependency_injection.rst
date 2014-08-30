
Depndency injection
===================

Abstract
--------

If you develop a new application you should try to not bind any business logic 
to the framework which you are using. Instead you should create services which 
get their dependencies via constructor or setter injection. So you can use your 
code also in other environments. PSX helps you in building applications which 
are not tied to the framework.

Definition
----------

The DI container of PSX is a simple class where each method represents an 
service definition. Because of that you have to specifiy in the index.php which 
container you are using.

.. literalinclude:: ../../public/index.php
   :language: php
   :lines: 23-28
   :prepend: <?php

Normally you would create your own container which extends the PSX default 
container. To add a service to the container you have to simply specifiy a 
method in the container which returns an object instance.

.. code-block:: php

    <?php

    class CustomContainer extends DefaultContainer
    {
        /**
         * @return Foo\Bar
         */
        public function getSomeBar()
        {
            return new Bar($this->get('entity_manager'));
        }

        /**
         * @return Foo\EntityManager
         */
        public function getEntityManager()
        {
            return new EntityManager();
        }
    }

The id of the service is "some_bar" which can be used in an inject annotation.
Note when your object has an dependency to another service use the 
$this->get('[service_id]') method to get only one instance of the service. In 
PSX it is best practice to group services into traits. I.e. the default 
container uses several traits which contains service methods

.. literalinclude:: ../../library/PSX/Dependency/DefaultContainer.php
   :language: php
   :lines: 44-50
   :prepend: <?php

The container is also compatible with the symfony DI container in case you want
use a service definition file you could also use the following code in your 
index.php

.. code-block:: php

    <?php

    require_once('../vendor/autoload.php');

    $container = new Symfony\Component\DependencyInjection\ContainerBuilder();
    $loader = new Symfony\Component\DependencyInjection\Loader\XmlFileLoader($container, new Symfony\Component\Config\FileLocator(__DIR__));
    $loader->load('services.xml');

    PSX\Bootstrap::setupEnvironment($container->get('config'));

    $request  = $container->get('request_factory')->createRequest();
    $response = $container->get('response_factory')->createResponse();

    $container->get('dispatch')->route($request, $response);

Usage
-----

The command and controller are the points which connect the framework to your
application. The DI container is not directly accessible instead you have to
specify properties in your command and controller with an specific inject 
annotation to use a service. 

.. code-block:: php

    <?php

    class FooController extends ViewAbstract
    {
        /**
         * @Inject
         * @var PSX\Http
         */
        protected $http
    }

When the controller or command gets created PSX searches for properties with
the "@Inject" annotation. If the inject annotation is available it takes the 
name from the property and searches in the DI container for a fitting service.
You can also specify the id of the service which should be used i.e. 
"@Inject entity_manager". We need to have the following method in our DI 
container in order to access the "http" service from the code above. 

.. code-block:: php

    <?php

    class DefaultContainer extends Container
    {
        /**
         * @return PSX\Http
         */
        public function getHttp()
        {
            return new Http();
        }
    }

This has the advantage that the DI container is completely invisible for our 
application. We only need to explicit specify the services which we need in our 
controller or command. So it is by design not possible to pass the DI container 
to any service in our application which decouples the code from the framework. 
Also it has the nice advantage that you can use code completion in your IDE.
