
### 2.1.0

* Generate swagger 2.0 and raml 1.0 specifications
* Updated psx schema and data library
* Removed wsdl and xsd generation

### 2.0.1 (2016-09-11)

* Use psx_connection in config
* Updated project tests

### 2.0.0 (2016-05-08)

* Split the framework into components
* Decoupled and improved each component
* Reading and writing data works now with POPOs
* Add generator to generate PHP classes from a RAML and JsonSchema specification
* Removed command system
* Improved documentation

### 2.0.0-RC1 (2016-04-30)

* Split the framework into components
* Decoupled and improved each component
* Add generator to generate PHP classes from a RAML and JsonSchema specification
* Removed command system

### 1.2.3 (2016-02-17)

* Added version handling to annotation and raml parser
* The pseudo ANY request method includes now also HEAD
* Fixed graph traverse handling with JsonSerializable objects
* Extended and improved test cases

### 1.2.2 (2016-02-08)

* Fix handle ANY request method
* Fix soap proxy controller content negotiation

### 1.2.1 (2016-02-07)

* Add pseudo ANY request method to the routing parser
* Removed annotation loader
* Dispatcher add option to disable sending the response to the client
* Add SoapProxyController which handles all SOAP communication
* Add option to restrict fields at the table abstract class

### 1.2.0 (2016-01-24)

* Add annotation API controller which can be used to specify the API behaviour
  through annotations
* Improved api generation command
* Add doctrine annotation reader for annotation parsing
* Unify doctrine cache usage and improve entity manager integration
* Oauth2 token endpoint handle also client_id and client_secret parameter

### 1.1.4 (2015-12-29)

* The schema API handles now automatically HEAD requests
* Add support for PATCH request method
* Fix automatic template detection for psr4 namespaces
* Renamed doCreate and doUpdate method to doPost and doPut to follow the http
  request method naming scheme
* Json schema document throw an exception if a json pointer could not be 
  resolved

### 1.1.3 (2015-12-27)

* Fixed a missing import alias which causes a problem in the loader
* Organize uses and fixed coding styles

### 1.1.2 (2015-12-27)

* Add option to specify global middlewares
* Updated composer dependencies
* Graph traverser handle also objects which implement JsonSerializable, 
  ArrayObject or Traversable
* JsonSchema parser set default id if not available
* Improve dispatcher dont send the response for nested calls
* Fixed comperator record comparsion and add test case

### 1.1.1 (2015-11-21)

* Add logger configuration settings and factory
* Add json patch and pointer implementation. The accessor uses now also the json 
  pointer syntax
* Add record serializer and add transformer which builds a record graph from 
  arbitrary data
* Add option to attach a validator to the importer inside a controller
* Make ImporterManager immutable so that it is not possible to globally change
  a importer object
* Improved validator
* Renamed some classes for PHP7 compatibility
* Removed class PSX\Data\Object and added static factory method fromArray to 
  PSX\Data\Record
* Adjust visitor exception messages

### 1.1.0 (2015-10-05)

* Curve array nest method handle indexed arrays
* JSON Schema add title property to complex types
* Documentation controller returns also the API endpoint url
* XML generator validate element names
* Documentation controller returns now the complete json schema which can be 
  used by the API client to generate a documentation
* Improved swagger and json schema generator
* Extended api resource methods

### 1.0.7 (2015-08-22)

* Improved condition class
* Schema html generator fix traversing nested schema properties

### 1.0.6 (2015-08-03)

* Fix schema parse read description of array and scalar types
* Add any schema property type
* Messages of StatusCodeException are now shown to the user when in production 
  mode
* Add title and required parameter to accessor
* Set controllerClass variable for template writers

### 1.0.5 (2015-07-22)

* Fix issue with content negotiation #10
* Make api resource and schema classes serializable

### 1.0.4 (2015-07-05)

* Switched to PSR-2 coding style and using absolute class paths in phpdocs
* Improve controller test cases
* Add RecordInterface access to Accessor
* Add JsonSerializable interface to record abstract
* For incoming requests an exception is now throw if the data structure contains
  keys which are not available in the schema
* Add development status for an API resource

### 1.0.3 (2015-06-13)

* Added new ChoiceType property which allows different kinds of objects as value
* Use same logic for traversing incoming and outgoing data
* Moved schema property methods assimilate and validate into seperate class
* Improved record factory factory class which uses now the object build to get 
  arbitrary dependencies
* Implemented factory to convert requests into PSR-7 objects and vice versa
* Improved many test cases

### 1.0.2 (2015-05-23)

* Fixed bug that the wrong base path was used when an json schema was included 
  in an RAML file

### 1.0.1 (2015-05-22)

* Added raml file parameter for api generation command
* Improved raml parser and tests
* Add format key to swagger parameter record
* Add iterator and countable interface to complex type
* Improved raml and swagger generation
* Add return $this to MethodAbstract::setDescription
* Add debug commands to print an raml and jsonschema resource definition
* Add console commands to generate different schema representations of the api

### 1.0.0 (2015-05-02)

* Removed unused methods in documentation controller
* Added property type factory class
* Gain php7 compatibility
* Json schema parser add schema version check
* Improved schema data, validation and api controller test cases
* Fixed datetime wrong behaviour without constructor args

### 1.0.0-RC2

* Fixed correct handling with date and time formats
* Improved several value objects 
* Improved controller schema api tests
* Pass controller context when forwarding an request to another controller
* The name of the exception record is now "error"
* Add a new context value so that all controllers share the same supported 
  writer value
* Moved body trait methods into the controller abstract class 
* Added controller set stdClass body test
* Improved XSD generator and allow setting min max array length

### 1.0.0-RC1

* Updated documentation
* Improved controller tests
* Container test case preserve config values between unit tests
* Moved test bootstrap code into seperate class

### 0.9.10

* Added getConnection method to table manager to enable easy transaction 
  handling
* Moved cache logic from table manager into seperate reader class
* Added exception converter service which converts an exception into an record
  which gets used to display error messages. This makes it easy to change the
  global error response format
* Send correct content type on exception or send 415 status code
* Handle Accept headers with +json or +xml media types correctly 
* Added jsonx format to default api controller
* Added http StreamInterface and remove psr http dependency
* Updated bootstrap cache generator file list
* Resource listing controller documentation create context in seperate method 
  which can be overloaded

### 0.9.9

* Handle different successful status codes in schema api
* Updated manual
* Removed html presentations from tool controllers
* Fixed xml writer nested array definitions
* Removed rest client controller since it contains no logic
* Improved raml parser
* Improved json schema parse resolve external resources. Supports file:// and
  http:// protocol

### 0.9.8

* Added raml and json schema parser
* Improved html output added psx- prefix for css classes
* Renamed Api\View to Api\Resource and redesigned classes containing the schema. 
  It is now possible to set an response schema for each status code
* Add possibility to cache resource listing results
* Improved tool controllers
* Renamed RestController to RestClientController
* Fixed bug in request factory where sub paths are not handeled correctly

### 0.9.7

* Added record graph traverser class and various visitor implementations which
  are used to produce data from an object graph
* Improved internal data representation for incoming and outgoing data
* Added content type aware fallback template generator and move fallback 
  generation to the html writer
* Improved request factory added automatic protocol detection if "psx_url" is 
  i.e. //foo.com and use buffered reader for request body
* Table abstract returns now record classes instead of associative arrays
* Log request and response body if in debug mode
* Added jsonx support
* Improved streams and added buffered stream implementation
* Added logcaster monolog listener
* Updated PSR http-message to the current version of the spec
* Changed license from GPLv3 to Apache 2.0

### 0.9.6

* Improved documentation
* Added raml generator
* Added table api abstract controller
* Improved and unified api view generators
* Added Swagger generator and improved related data models
* Added resource listing class and improved the api documentation, swagger and 
  wsdl controller
* Move generation of the fallback template into a seperate class
* Improved activitystreams test and implementation
* Removed location and added context class which holds parameters gathered 
  around the controller/command

### 0.9.5

* Added HTTP request/response interfaces and added an factory to convert PSX 
  HTTP request/responses into PSR-7 request/responses
* Added HTTP stream handler which uses the native HTTP stream wrapper
* Added missing HTTP status codes
* Add support for array and object sql table column types
* Added primary key filter which checks whether an id exists in an table
* Used dbal query builder in various places for better db vendor support
* Added plain text writer

### 0.9.4

* Sql table query methods return now an array instead of RecordInterface
* Added media type class and improved content negotiation
* Improved JWT implementation
* Removed unused methods from Base class which now contains only the getVersion 
  method
* Added rfc3986 compatbile URI resolver class 
* Improved URI/URL/URN classes. Changed URL parameter getter/setter methods from 
  getParam to getParameter etc.
* Removed output method from xml writer and improved atom writer/entry
* Added http cookie parser class
* Added http stream util class to simplify working with streams
* Added application stack interface
* Updated dbal to version 2.5
* Added various middleware filter implementations
* Improved tests
* Add CurveArray class to nest or flatten an array
* Updated api doc controller template

### 0.9.3

* Added commands to generate boilerplate source code for controller, commands 
  and schema
* When the default writer gets determined it respects now the list of supported
  writers
* Implemented priority queue to give reader and write a priority in which order 
  they get used
* Changed default html template file extension from .tpl to .html
* Added abstract template writer which can be used to generate an output with
  an template engine
* Added svg writer which uses the abstract template writer
* Add possibility to set an content negotiation rule for an specific writer
* Restricted the TableInterface::TYPE_* constants to the subset of supported 
  types from the doctrine dbal and use the dbal for type conversion
* Added command to generate an bootstrap cache which can be included at startup
  to increase performance

### 0.9.2

* Integrated JMS serializer
* Removed payment and handler classes
* Added symfony console component and added several commands
* Http client removed parameters from the http request object which not directly  
  belong to the request into an http option object which can be passed to the  
  request method
* Moved creation of the DI container into its own file so it can be used in 
  different environments and the user can easily change the container
* Dispatch events at key locations

### 0.9.1

* Added tool controller to generate an Swagger or WSDL definition from an API
* Added SOAP support and WSDL generator
* Improved OpenSSL classes
* Add common http exceptions which can be used to directly deliver an specific 
  status code
* Added version handling for API
* Added getAccessor() method to controller which offers a way to easily get an
  value from the request body

### 0.9

* Remove psx sql library and using doctrine dbal instead
* Improved data system seperating reader functionality into an reader and 
  transformer class
* Added data schema component with that it is possible to describe json or xml
  data formats. The schema can be exported into XSD or JsonSchema
* Make cache system psr-6 compatible
* Added @Inject annotation to use a service from the DI container inside and 
  command or controller
* Extended validate library
* Implemented the first version of the psr http-message interfaces
* Improve template interface and add missing methods in smarty and twig handler
* Split up di container services into different traits
* Add command system
* Add support for chunked transfer encoding
* Error handling was shifted in an controller which can be overwritten to 
  provide custom error page or handling 
* Doctrine handler resolves TO_ONE entity associations
* Improved request factory
* Possibility to set an wildcard in an routing file

### 0.8.6

* Added missing http event method onHead
* Possibiliy to set an alias path in an routing file
* Improved hhvm/hack compatibility (all tests pass on hhvm 3.0.1)
* Added data html writer
* Improved routing mechanism method which should be called must now be declared
  in the routing file
* Added an callback handler to each handler type
* Updated swagger library to current version 1.2
* Added record xml definition schema
* Default manager was removed and concrete Dom/Map manager was introduced

### 0.8.5

* Improved hhvm compatibility
* Added redirect exception
* Importer add possibility to import anonymous record classes and improved tests
* Renamed controller method getRequestFilter and getResponseFilter to 
  getPreFilter and getPostFilter
* Improved tests

### 0.8.4

* Update http library to current psr-7
* Improved tests

### 0.8.3

* Changed getRecord of HandlerQueryInterface its only possible to get a new 
  record containing all supported fields
* Implemented http request and response through the complete framework lifecycle
* Added callback resolver which builds an php callback from an location
* Removed output buffering for more transparent debugging
* Make http library compatible to the upcomming psr-7
* Updated activity streams according to RFC draft
* Removed docbook documentation infavor of reStructuredText files

### 0.8.2

* Improve parsing of routing file
* Fix bug that the sended headers are not correctly parsed

### 0.8.1

* Add http callback handler and add response parser class
* Fix html parser single and double quotes in attributes
* Allow urls with protocol definition i.e. //phpsx.org
* Improved filter tests and added date range filter
* Added entity annotation importer
* Improved tests
* Removed apc cache handler
* Added sql table manager wich can read table definitions from different sources
* Add annotation test and improve parsing

### 0.8

* Added domain package. The package helps you to create your application in a 
  domain driven design
* Fully support symfony di container
* Introduced importer concept. An importer is a class wich can fill a record 
  with data
* Every record has now a single method getRecordInfo() wich returns all
  available fields. Removed all other methods from the record interface
* Added mapper class to map an record to another class by calling the fitting
  setter methods
* It is possible to add reader and writer implementations to the reader or 
  writer factory
* Improved html parser. Add an option to allow html comments wich is false by
  default
* Added symfony event dispatcher for event handling in an domain
* A new handler package was added wich contains all handler related classes
* Added filter definition class to validate a record based on a set of rules

### 0.7.3

* Added openid connect basic client
* Fixed routing mechanism
* Improved sql select and tests
* Update webfinger library to rfc7033
* Added hostmeta library
* Improved XRD library
* Created XRI package wich contains now the XRD and XRDS libraries
* Added jsonp data writer
* Added http fixture for skrill test
* Improved loader and dispatch test

### 0.7.2

* Fix use correct request method
* Improved oauth test

### 0.7.1

* Improve template detection
* Curl handler option to set a proxy
* Use real request method in oauth authentication filter

### 0.7

* Added get method to TemplateInterface
* Load template from Resource folder in ViewAbstract
* Added custom template error config
* Removed template and module folder
* Added routing location finder and remove filesystem router
* Removed constant PSX_PATH_MODULE and PSX_PATH_TEMPLATE
* Improved paypal library
* Added http mock and mock capture handler
* Improved unit tests

### 0.6.4

* Add proper clone behavior for select class
* Set openid request record attributes protected
* Fix oauth request filter content type detection

### 0.6.3

* Added static method setupEnvironment to Bootstrap
* Improve sql library
* Fixed getErrorTemplate() env vars
* DateTime added convertIntervalToSeconds method
* Response and request filter can now return also callables
* Added getStage() method to the controller to control wich methods should be 
  called
* Improve void session to save values in memory
* Added clearError() method to validate

### 0.6.2

* Added JWT and JWS library
* Json decode throws now an exception if decoding fails
* Add void session
* Add support to call controller through cli to simplify testing
* Improve error response handling

### 0.6.1

* Improved cache class

### 0.6

* Added dispatch class wich routes the request to the controller. Also applies
  the request and response filter wich are defined by the controller
* The controller proxies unknown get* methods to the container
* Improved template and added template wrapper classes for twig and smarty
* Improved DI container interoperability it is now possible to use the symfony 
  DI container instead of the psx DI container
* Use composer autoloader
* Added json activity stream writer
* Add simple xrds writer
* Extract xml, atom and rss writer into class for usage outside of the data 
  writer context
* Remove logging library
* Added Sitemap an Odata libraries
* Improved activity stream classes
* Add method get, getBy and getOneBy to handler interface

### 0.5.4

* Fixed record serialize
* Fix select remove getDefaultRecordClass call from underlying table and remove
  getResultSet method
* Improved select and record test

### 0.5.3

* Fix Oauth2 error exception types
* Improve handler abstract and interface
* Use json encode/decode to un/serialize records
* ResultSet add getter methods and add(), clear() method
* Remove static parse methods from Condition
* Remove getDefaultRecordClass from TableAbstract since the table has not to 
  decide wich object should be used this is part of the handler
* Improve tests and remove create table statments in tests

### 0.5.2

* Use port in base self url
* Config option to set include path

### 0.5.1

* Added autoload parameter in config to disable autoloading in case an external
  autoloader like composer is used
* Added payment paypal and skrill library
* Improve record added import annotation parsing and added Serializable 
  interface
* Fixed wrong name in OpenId provider namespace

### 0.5

* Added namespace support
* Improved db tests
* Improved html parser library
* Added pingback library
* Validate, parameter and body objects are now loaded in an DI container
* Load dependencies in _ini() method instead of __construct()
* Fixed markdown parser and sql select

### 0.4.7

* Added classes to generate an swagger api declaration and util annotation 
  parser
* Improved autoloader using stream_resolve_include_path before require the class
  so the autoload will not fail if the file doesnt exists
* Added new html5 filter collections
* Renamed PSX_Time to PSX_DateTime 
* Fix http redirection header of relative urls
* Added user agent string to most http calls
* Add cache to PSX_Base getRequest method so calling the method multiple times
  will always return the same object
* Improved PSX_Loader class. The name of the index method must be now __index
  wich is called if no other method is specified in the request. This was done 
  because of constructor conflict. Added annotation parser to loader for easier 
  routing 
* Added method getValidator(), getMethod(), getUrl(), getHeaders(), getHeader(), 
  getParameter() and getBody() to PSX_ModuleAbstract
* Using PSX_PATH_CACHE path in file cache handler
* Fix markdown class encode emphasis
* Add method setSavePath to PSX_Session class

### 0.4.6

* Added Atom and RSS static request method
* Improved docblock comments
* Removed unused DOM package
* Changed constructor of PSX_Sql class
* Added cookie store to http package
* Improved cache classes and tests
* Moved class PSX_Get, PSX_Post, PSX_Cookie, PSX_Request, PSX_Files in input
* package and improved tests
* Updated paging class and tests
* Added calendar class
* Added improved psx html package add html5 lexer and filter library and updated
* html parser
* Added getParameters to dependency abstract
* Added view dependency and renamed module DefaultAbstract to ViewAbstract

### 0.4.5

* Renamed constant in_psx to PSX because of namespace issue
* Added data multipart/form-data reader
* Added PSX_Base class containing informations about the framework and current
  http request
* Moved several methods from PSX_Config into PSX_Base class
* Added data reader and writer factory
* Added Oauth2 client library
* Added payment paypal library
* Improved log library
* Removed ca-bundle.pem from library and add method to set ca path for curl
  handler
* Renamed static method "isFile" to "exists" in file class because the class
  extends now SplFileObject wich already defines an isFile method
* Fixed openid stateless mode and correct exception name

### 0.4.4

* Updated webfinger library and tests
* Removed idna (punnycode) filter
* Added uri and urn libraries
* Added opensocial and activitystreams records
* Improved tests
* Added oembed library
* Fix openid consumer and relying party library
* Update documentation

### 0.4.3

* Fixed openid provider association implementation
* Added openssl wrapper class
* Added webfinger util library
* Added util markdown parser class
* Improve error and exception handling
* Added sql selection class
* Removed data servlet interface in favor of the sql table class
* Added sql table abstract class
* Add http upload libary
* Added optional require parameter to input method
* Added and improved several tests

### 0.4.2

* Updated atom and rss item parsing
* Updated HTTP socks handler proper handling of chunked transfer encoding
* Removed NestedSet library because they where not stable enough
* Add OpenID provider implementation
* Fix HTTP classes headers are stored now interal as key => value
* Fix OpenId so that the claimed id is added on redirect if the local id doesent
  exist and add getIdentifiert method
* Update HTTP request class and fix curl handler follow location. Remove
  "Expect: 100-Continue" header and update ca-bundle.pem
* Added unit tests
* Fixed several bugs

### 0.4.1

* Rewrote manual
* Fix follow location in http curl handler
* Improve tests

### 0.4

* Using now standard "camelcase" coding style for interoperability
* Update data writer library for handling Atom and Rss feeds correctly
* Add http library option to automatically follow redirects
* Remove unused classes
* Fixing many bugs

### 0.3.4

* Rewrote html form / paging / grid using now the DomDocument object
* Remove task lib
* Add psx/api and psx/data package for building RESTful API services
* Moved the classes from psx/core in the package psx
* Rewroter filter system and add class psx_validate

### 0.3.3

* Updated psx_srv_[atom|rss] class wich uses now the DateTime class
* Updated dom wich extends now the DomDocument class
* Updated time class wich extends now the DateTime class and some uneccasary
  methods were removed
* Remove util/uri class
* Update sql library send SET NAMES UTF-8 command on creation

### 0.3.2

* Unknown

### 0.3.1

* Added form generation class
* Added and fixed tests
* Added request library
* Implemented psx_input interface
* Rewrote filter library
* Rewrote manual to version 0.3
* Fixed openid oauth and yadis library

### 0.3

* Complete redesing of the framework the library is now more loosly coupled
  because most classes using dependency injection.
* The component section was removed and all classes are now in the psx library
  folder
* The interface psx_module_private was created if you implement that in your
  module nobody can access the public methods via the browser
* The standard PHP loader was implemented wich is also used by Zend etc.
* adding sql pdo driver
* Fixing several bugs

### 0.2.3

* Add pubsubhubbub library
* Add atom library to parse atom feeds
* Add html parse library to parse malformed html
* We use XMLWriter now in the class rss/atom/opensearch to produce XML
* Fixed opensocial component bugs

### 0.2.2

* Add openid, yadis, atom/rss2 and opensearch component
* Renamed folder lib to library and ext to extension
* Perfomance improvments
* Fixing bugs
* Delete include folder
* Add phpunit tests

### 0.2.1

* Add component net/oauth
* Rewrote http lib
* Add phpunit tests
* Fixing bugs
* Redesign sample template
* Orm lib removed
* Parse lib removed
