
Import data
===========

Abstract
--------

This chapter will explain more detailed how you can import request data.

Controller
----------

Through the `@Incoming` annotation it is possible to specify a schema for the
request data. Besides a path to a json schema file it is also possible to
provide a class name of a simple PHP object. PSX builds a schema based on the
properties of the class and tries to import the data into an instance of that
object. At the end you will receive an object of that type containing the data
from the request.

.. code-block:: php

    <?php

    namespace Acme\Foo;

    use PSX\Controller\ControllerAbstract;

    class Controller extends SchemaApiAbstract
    {
        /**
         * @Incoming("Acme\Foo\Bar")
         */
        public function doPost($model)
        {
        }
    }
