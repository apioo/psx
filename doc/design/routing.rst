
Routing
=======

Routing is the process to delegate the HTTP request to the fitting controller. 
By default PSX uses a simple routing PHP file which contains all available 
routes.

Routing file
------------

PSX parses the routing file and the route which matches first is taken. Lets 
take a look at the following example routing file

.. code-block:: php

    <?php
    
    # example routing file
    
    return [
        [["GET"], "/", "Foo\News\Application\Index"],
        [["GET"], "/foo", "~/"],
        [["GET", "POST"], "/api", "Foo\News\Application\Api"],
        [["GET"], "/news/:news_id", "Foo\News\Application\News\Detail"],
        [["GET"], "/news/archive/$year<[0-9]+>", "Foo\News\Application\News\Archive"],
        [["GET"], "/file/*path", "Foo\News\Application\File"],
    ];

in case we make the following HTTP request the `Foo\\News\\Application\\Api` 
controller gets called

.. code-block:: none

    GET /api HTTP/1.1

The path `/foo` is an alias for the path `/`. That means in both cases we call 
controller `Foo\\News\\Application\\Index`.

If we make a HTTP request to `/news/2` the controller 
`Foo\\News\\Application\\News\\Detail` gets called. In our controller we can
access the dynamic part of the path with:

.. code-block:: php

    <?php

    $newsId = $this->context->getUriFragment('news_id')

The dynamic part contains all content to the next slash. You can also specifiy
a regular expression to define which chars are allowed in the dynamic part. In
our example the path /news/archive/$year<[0-9]+> is only matched if the last 
part contains numeric signs i.e. /news/archive/2014.

Also you can specifiy a wildcard * which matches all content i.e. 
/file/foo/bar/test.txt would invoke `Foo\\News\\Application\\File`

Reverse routing
---------------

PSX contains a reverse router class with that it is possible to get the path or 
url to an existing controller method. If you want i.e. to link or redirect to a 
specific controller method

.. code-block:: php

    <?php

    $path = $this->reverseRouter->getPath('Foo\News\Application\Index');
    $url  = $this->reverseRouter->getUrl('Foo\News\Application\Index');
    $path = $this->reverseRouter->getPath('Foo\News\Application\News\Archive', ['year' => 2014]);

or in you template you can use

.. code-block:: php

    <a href="<?php echo $router->getPath('Foo\News\Application\Index'); ?>">Home</a>

this has the advantage that you can easily change your urls in your routing file
and all links point automatically to the correct path.

Custom routing
--------------

If you want store your routes in another format or in a database you can 
write your own RoutingParser. The routing parser returns a RoutingCollection 
which contains all available routes.

.. code-block:: php

    interface RoutingParserInterface
    {
        /**
         * @return \PSX\Framework\Loader\RoutingCollection
         */
        public function getCollection();
    }

Your routing class has to implement this interface. Then you can overwrite the 
method :code:`getRoutingParser` in your DI container. Note in case you have a
really huge amount of routes you should probably consider to write your own 
location finder since the routing collection contains all available routes.
A location finder has to implement the following interface.

.. code-block:: php

    interface LocationFinderInterface
    {
        /**
         * Resolves the incoming request to an source. An source is an string which
         * can be resolved to an callback. The source must be added to the context.
         * If the request can not be resolved the method must return null else the
         * given request
         *
         * @param \PSX\Http\RequestInterface $request
         * @param \PSX\Framework\Loader\Context $context
         * @return \PSX\Http\RequestInterface|null
         */
        public function resolve(RequestInterface $request, Context $context);
    }
