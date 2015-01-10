
Request lifecycle
=================

Http stream
-----------

PSX uses the proposed HTTP `PSR standard <https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md>`_ 
through the application. At the start of the PSX lifecycle an HTTP request and 
response object is created. Each object is created in its own factory so you 
can change the behavior. This can be useful if you want i.e. use another body 
stream or get values from different environment sources.

.. literalinclude:: ../../library/PSX/Dispatch/RequestFactoryInterface.php
   :language: php
   :lines: 33-42
   :prepend: <?php

.. literalinclude:: ../../library/PSX/Dispatch/ResponseFactoryInterface.php
   :language: php
   :lines: 33-42
   :prepend: <?php

After the request and response objects are created the loader searches the 
fitting controller based on the routing file. The controller must implement the
ApplicationStackInterface

.. literalinclude:: ../../library/PSX/ApplicationStackInterface.php
   :language: php
   :lines: 33-43
   :prepend: <?php

Then the loader executes the application stack which is simply an array 
containing FilterInterface or callables

.. literalinclude:: ../../library/PSX/Dispatch/FilterInterface.php
   :language: php
   :lines: 36-45
   :prepend: <?php

After the stack was executed the response must be send to the client. This is 
done through an sender class which sends the header through the "header" 
function and outputs the response body via "echo".

.. literalinclude:: ../../library/PSX/Dispatch/SenderInterface.php
   :language: php
   :lines: 35-43
   :prepend: <?php

If you want change this behavior i.e. you want to cache the response or make
aditional security checks your class must only implement the SenderInterface.
