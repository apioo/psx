
Data writer
===========

PSX uses a datawriter conecpt to transform the data which was produced by the
controller into an fitting format for the client. What content is served depends
on the Accept header and the GET parameter format. This chapter should give you
on overview which data writer exist and how they work.

Available data writer
---------------------

In PSX we distinct between two types of data writer. First an writer which 
produces an data form or an writer which produces an specification of an data 
format. An "data format" writer can produce content from arbitrary content where
an "specification" data writer needs an specific model to produce the content.
In the following an overview of available writer in PSX: 

+--------------------------+------------------------+------------------+---------------+---------------+
| Class                    | Content-Type           | Format-Parameter | Data-Format   | Specification |
+==========================+========================+==================+===============+===============+
| PSX\\Data\\Writer\\Html  | text/html              | html             | X             |               |
+--------------------------+------------------------+------------------+---------------+---------------+
| PSX\\Data\\Writer\\Json  | application/json       | json             | X             |               |
+--------------------------+------------------------+------------------+---------------+---------------+
| PSX\\Data\\Writer\\Jsonp | application/javascript | jsonp            | X             |               |
+--------------------------+------------------------+------------------+---------------+---------------+
| PSX\\Data\\Writer\\Xml   | application/xml        | xml              | X             |               |
+--------------------------+------------------------+------------------+---------------+---------------+
| PSX\\Data\\Writer\\Atom  | application/atom+xml   | atom             |               | X             |
+--------------------------+------------------------+------------------+---------------+---------------+
| PSX\\Data\\Writer\\Rss   | application/rss+xml    | rss              |               | X             |
+--------------------------+------------------------+------------------+---------------+---------------+

Use case
--------

Lets take a look at the following controller.

.. code-block:: php

    <?php

    class FooController extends ControllerAbstract
    {
    	public function doIndex()
    	{
    		$this->setBody(array(
    			'news' => array(...)
    		));
    	}
    }

If you would request this method with an normal browser PSX would try to display
the data as HTML. Therefor it would use the html writer which assigns the data
to the template. In your template you can then build the html representation of 
the news. If we would make the request containing an Accept header 
application/json or GET parameter "format" containing "json" the data would be
returned in an json format.
