
Value objects
=============

Abstract
--------

PSX uses in several places value objects. To unify the look and feel of those 
value objects we have a simple specification which describes the behaviour.

Specification
-------------

* The class consists of multiple components. A component must be either a scalar 
  value or another value object

* The class must be immutable so it must be not possible to modify the state
  of a value object once it was created

* The class must have a constructor with two behaviours. If only one argument was 
  passed to the constructor this value represents the string representation of 
  the value object and must be parsed into its components. If more arguments are 
  provided they must be assigned to the fitting components. It must be possible 
  to pass every component to the constructor. I.e. the following two objects are 
  equal:

  .. code-block:: php

      $mediaType = new MediaType('text/plain; charset=UTF-8');

      $mediaType = new MediaType('text', 'plain', ['charset' => 'UTF-8']);

* The class must have a method ``toString()`` which returns a string which 
  contains the complete state of the object. The following operation should be 
  always true: 
  
  .. code-block:: php

      $uri = new Uri('/foo'); 

      $uri->toString() === (new Uri($uri->toString()))->toString();

  We use a custom ``toString()`` method since the exception handling inside the
  ``__toString()`` method is really bad. Nevertheless each object can have the
  magic ``__toString()`` method which calls the ``toString()`` method

* The class must have a protected property and getter method for each component

  .. code-block:: php

      $mediaType = new MediaType('text/plain; charset=UTF-8');

      $mediaType->getType();
      $mediaType->getSubType();

* The class may have static factory methods ``fromXXX`` to create the object from 
  other values

  .. code-block:: php

      $time = Time::fromDateTime(new \DateTime())

* The class may have ``withXXX`` methods to create a new value object replacing 
  only the given component

  .. code-block:: php

      $uri = new Uri('/test?foo=bar');

      $uri = $uri->withPath('/foo');

* The value object should provide a protected method ``parse()`` which parses 
  the string representation. When parsing the value must be cast to a string so 
  that in case it is another value object it returns its string representation. 
  If there are components which need extra processing provide protected methods 
  ``parseXXX()`` which are called inside the parse method. This has the 
  advantage that value objects which extend this class can override these 
  methods so they can choose whether they want to parse theses values or not

Implementations
---------------

The following value object implementations exist:

 * PSX\\Uri\\Uri
 * PSX\\Uri\\Url
 * PSX\\Uri\\Urn
 * PSX\\Http\\MediaType
 * PSX\\Http\\Cookie
 * PSX\\DateTime\\Date
 * PSX\\DateTime\\Time
 * PSX\\DateTime\\DateTime
