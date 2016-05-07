
Tools
=====

PSX comes by default with a set of controllers which help you to generate 
documentation or other data based on the defined APIs. The following chapter
explains all available controllers. To setup such a controller you have to add 
a route entry.

Documentation
-------------

PSX provides a controller which can generate automatically a documentation for
the defined APIs. The controller provides only an API which can be used by any
client to display a documentation for end-users. You can use i.e. evid 
(https://github.com/k42b3/evid) which is a html/javascript app which uses the 
API to provide a clean API documentation. With evid it is also very easy to 
customize the documentation for your own needs by i.e. providing custom pages or 
change the style according to your needs.

.. code::

    GET /doc                PSX\Framework\Controller\Tool\DocumentationController::doIndex
    GET /doc/:version/*path PSX\Framework\Controller\Tool\DocumentationController::doDetail

WSDL
----

Generates a WSDL representation for the given API.

.. code::

    GET /wsdl/:version/*path PSX\Framework\Controller\Generator\WsdlController
    ANY /soap                PSX\Framework\Controller\Proxy\SoapController

Swagger
-------

Generates a Swagger resource listing and definition.

.. code::

    GET /swagger                PSX\Framework\Controller\Generator\SwaggerController::doIndex
    GET /swagger/:version/*path PSX\Framework\Controller\Generator\SwaggerController::doDetail

RAML
----

Generates a RAML representation for the given API.

.. code::

    GET /raml/:version/*path PSX\Framework\Controller\Generator\RamlController

Routing
-------

Provides an API to publish all available API paths.

.. code::

    GET /routing PSX\Framework\Controller\Tool\RoutingController
