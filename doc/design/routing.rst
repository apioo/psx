
Routing
=======

Routing is the process to delegate the HTTP request to the fitting controller. 
By default PSX uses a simple routing file wich contains all available routes. 
The routing file was inspired by the java play framework.

Routing file
------------

PSX parses the routing file and the route which matches first is taken. Lets see 
the following example routing file

.. code-block:: none

    # example routing file

    GET / Foo\News\Application\Index::doFoo
    GET /foo ~/
    GET|POST /api Foo\News\Application\Api::doIndex
    GET /news/:news_id Foo\News\Application\News::doDetail
    GET /news/archive/$year<[0-9]+> Foo\News\Application\News::doArchive
    GET /file/*path Foo\News\Application\File::downloadFile

in case we make the following HTTP request the method doIndex of the class 
`Foo\\News\\Application\\Api` gets called

.. code-block:: none

    GET /api HTTP/1.1

The path /foo is an alias for the path /. That means in both cases we call the 
method doFoo of the class `Foo\\News\\Application\\Index`.

If we make a HTTP request to /news/2 the controller 
`Foo\\News\\Application\\News::doDetail` gets called. In our controller we can
access the dynamic part of the path with:

.. code-block:: php

    <?php

    $newsId = $this->getUriFragment('news_id')

The dynamic part contains all content to the next slash. You can also specifiy
a regular expression to define which chars are allowed in the dynamic part. In
our example the path /news/archive/$year<[0-9]+> is only matched if the last 
part contains numeric signs i.e. /news/archive/2014.

Also you can specifiy a wildcard * which matches all content i.e. 
/file/foo/bar/test.txt would invoke `Foo\\News\\Application\\File::downloadFile`

Reverse routing
---------------

PSX contains a reverse router class with that it is possible to get the path or 
url to an existing controller method. If you want i.e. to link or redirect to a 
specific controller method

.. code-block:: php

    <?php

    $path = $this->reverseRouter->getPath('Foo\News\Application\Index::doFoo');
    $url  = $this->reverseRouter->getUrl('Foo\News\Application\Index::doFoo');
    $path = $this->reverseRouter->getPath('Foo\News\Application\News::doArchive', array('year' => 2014));

or in you template you can use

.. code-block:: php

    <a href="<?php echo $router->getPath('Foo\News\Application\Index::doFoo'); ?>">Home</a>

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
