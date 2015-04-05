
Tools
=====

PSX comes by default with a set of controllers which help you to generate 
documentation or other data based on your defined APIs. The following chapter
explains all available controllers. To setup such an controller you have to add 
a route entry. The paths in the routes are only a suggestion you can easily 
change them how you like.

Documentation
-------------

PSX provides an controller which can generate automatically a documentation for
your defined APIs. The controller provides only an API which can be used by any
client. You can use i.e. evid (https://github.com/k42b3/evid) which is a
html/javascript client which uses the API to provide a clean API documentation.
Also it is very easy to customize the documentation for your own needs by 
i.e. providing custom pages or change the style according to your needs.

.. code::

    GET /doc                PSX\Controller\Tool\DocumentationController::doIndex
    GET /doc/:version/*path PSX\Controller\Tool\DocumentationController::doDetail

WSDL
----

Generates an WSDL representation for the given API.

.. code::

    GET /wsdl/:version/*path PSX\Controller\Tool\WsdlGeneratorController

Swagger
-------

Generates an Swagger resource listing and definition.

.. code::

    GET /swagger                PSX\Controller\Tool\SwaggerGeneratorController::doIndex
    GET /swagger/:version/*path PSX\Controller\Tool\SwaggerGeneratorController::doDetail

RAML
----

Generates an RAML representation for the given API.

.. code::

    GET /raml/:version/*path PSX\Controller\Tool\RamlGeneratorController

Routing
-------

Provides an API to publish all available API paths

.. code::

    GET /routing PSX\Controller\Tool\RoutingController

Command
-------

Provides an API to execute commands

.. code::

    GET|POST /command PSX\Controller\Tool\CommandController

