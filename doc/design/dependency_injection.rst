
Dependency injection
====================

Definition
----------

The file :file:`container.php` returns the main DI container of your 
application. 

.. literalinclude:: ../../container.php
   :language: php

The DI container of PSX is a simple class where each method represents an 
service definition. Normally you would create your own container which extends 
the PSX default container. To add a service to the container you have to simply 
specifiy a method in the container which returns an object instance.

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
   :lines: 52-59
   :prepend: <?php

The returned container must be compatible with the symfony DI container. You 
could also use i.e. the symfony DI container

.. code-block:: php

    <?php

    $container = new Symfony\Component\DependencyInjection\ContainerBuilder();
    $loader = new Symfony\Component\DependencyInjection\Loader\XmlFileLoader($container, new Symfony\Component\Config\FileLocator(__DIR__));
    $loader->load('services.xml');

    return $container;

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
