
Middleware
==========

Abstract
--------

This chapter gives a short overview how middlewares are implemented in PSX.

Usage
-----

In PSX a controller is basically a class which is a traversable containing 
middleware instances. A middleware is either an instance of ``FilterInterface`` 
or ``Closure``. The default ``ControllerAbstract`` merges the middlewares from
the ``getPreFilter`` and ``getPostFilter`` method with the controller it self
since it is also a middleware:

.. code-block:: php

    <?php
    
    class Controller extends ControllerAbstract
    {
        public function getIterator()
        {
            return new \ArrayIterator(array_merge(
                $this->getPreFilter(),
                [$this],
                $this->getPostFilter()
            ));
        }
    }

Also the global pre and post middlewares are merge to the middleware stack. 
The global middlewares are specified at the ``configuration.php`` file:

.. code-block:: php

    'psx_filter_pre'  => [],
    'psx_filter_post' => [],

This complete stack of middlewares is then executed.
