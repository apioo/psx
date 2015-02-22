
Tools
=====

PSX comes by default with a set of controllers which help you to generate 
documentation or other data based on your defined APIs. The following chapter
will help you to setup these tools. The paths in the routes are only a 
suggestion you can easily change them how you like.

Documentation
-------------

Generates an enduser API documentation based on your defined API controllers. 

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

Provides a simple user interface to browse all existing routes

.. code::

    GET /routing PSX\Controller\Tool\RoutingController

Rest console
------------

A javascript based rest console to test your API endpoints

.. code::

    GET /rest PSX\Controller\Tool\RestController

Command
-------

A web interface to execute commands

.. code::

    GET|POST /command PSX\Controller\Tool\CommandController

