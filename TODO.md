_Ever growing list of ideas and thoughts howto improve and extend PSX. Topics_ 
_which are likely to be implemented are moved to a seperate github issue_

### 2.x

- [ ] Handle JSONRpc calls
- [x] SchemaApiAbstract for outgoing schemas we need an option to set an 
      passthru schema which simply outputs all provided data
- [x] Use https://github.com/nikic/PHP-Parser/blob/master/doc/4_Code_generation.markdown
      to generate code
- [x] Remove set/getRestrictedFields in TableAbstract
- [x] Redesign framework generations commands
- [x] Fix problem with annotation collision @Title in api and schema
- [x] Schema manager erturns same instance probably a problem when a schema gets
      modified
- [x] Add annotation to define the class of the request body, thus we must
      remove the RecordInterface type hint of the doPost etc methods
- [x] Create schema generator which dumps a schema as PHP code
- [x] Remove doCreate and doUpdate call in SchemaApiAbstract
- [x] Think about moving from a monolith repo into multiple packages. There are
      some components which might be useful withput psx i.e. data, schema
- [x] We need a command which reads a RAML/Swagger file and generates a 
      controller with annotations based on the definition

### 1.x

- [ ] Add GraphQL parser to generate a schema based on a GraphQL string #12
- [ ] Add Swagger2 support #11
- [ ] Improve OAuth2 support #7
- [ ] Add host-meta controller to expose specific API services #5
- [ ] Create controller which generates a text/uri-list #4
- [ ] Add WADL generator #2
- [ ] Probably adapt a common API error format:
      https://github.com/blongden/vnd.error
      https://tools.ietf.org/html/draft-nottingham-http-problem-07
- [ ] Add controller to server static content
- [ ] Better support for conditional requests E-Tag, If-Match etc.
- [ ] Think about support for OData
- [ ] Think about support for JSON-RPC
- [ ] Add http interceptor handler and method to add global default headers i.e.
      a user agent header (these can be overriden by a specific set header)
- [ ] Use DateTimeImmutable if we are dropping support for 5.4 and require at 
      least 5.5
- [ ] Schema html writer array constraints are not listed, only the constraints 
      of the prototype element
- [ ] Evaluate again whether to use psr7. Probably wait to see adoption, 
      immutability is still a problem
- [ ] Extend schema representation
  - [ ] Add schema property default value option
  - [ ] Possibility to describe in an API that authentication is required. 
        Probably also what kind basic, oauth, etc
    - [ ] Check how this is done in various specs like Swagger, RAML, etc.
  - [x] PSX schema add support for object choice for a key (json schema: oneOf, 
        xsd:choice)
  - [x] Add possibility to add a wildcard type in a schema which allows 
        arbitrary subtypes where we dont know the structure i.e. config (Is this 
        a good idea?)
- [ ] Add event listener to table manager so that we can fire an event everytime 
      a record gets created, updated or deleted
- [ ] Consider use Stash as caching library if PSR is ready
- [ ] Evaluate whether we could build a RDF writer
- [ ] Add verify method to psx sql condition to verify conditions against an 
      array
- [ ] Option to set a custom record class in table abstract
- [ ] Implement filters
  - [ ] Add hawk authentication filter (https://github.com/hueniverse/hawk)
  - [ ] Add aws authentication filter (http://docs.aws.amazon.com/AmazonS3/latest/dev/RESTAuthentication.html)
  - [ ] Add CORS header filter
  - [ ] Add request rate limit filter
- [ ] Better symfony integration
  - [ ] Add PSX symfony bundle
  - [ ] Add SymfonyControllerExecutor to execute a symfony controller from PSX
- [ ] Probably implement http://www.w3.org/TR/xmldsig-core/
- [ ] Probably replace all generic exceptions (RuntimeException, 
      InvalidArgumentException, etc..) with PSX specific exceptions 
- [ ] Follow the json-ld / hydra spec (http://www.hydra-cg.com/) and see whether 
      we should add support for it
- [ ] Test and handle file uploads

### Meta

- [ ] Update github readme, more getting started instructions etc.
- [ ] Add hyperlink vs specification API to the documentation
- [ ] Write in doc about data vs document oriented aka data writer vs template 
      writer
- [ ] Improve global exception messages so that we have the same kind of wording
- [ ] For version 1.0 implement http://www.techempower.com/benchmarks
- [ ] Add win ci (http://www.appveyor.com/)

### Archive

- [x] Add commands which generate php code
  - [x] Add command to generate an API endpoint based on a RAML file
  - [x] Add command to generate a PHP schema based on a json schema
  - [x] Add command to generate a json schema from a PHP schema
- [x] Add command to generate client code based on the API definition. Probably 
      this should be done in another project and based on a RAML/jsonschema spec
- [x] Schema generation
  - [x] Probably add json schema importer command to create a PHP schema based
        on the given json schema
  - [x] Add generator to generate a php schema class from a xsd or json schema 
        definition 
  - [/] Create tool controller to create and export a schema. Should produce
        a json schema which can be imported by a command
  - [x] Create command to create PHP source code based on a RAML spec. This 
        could probably use the json schema generator to generate the schema 
        class. So we could create a complete schema endpoint from the RAML spec
- [x] Consider use of JAX-RS annotations to provide meta informations for a 
      controller #3
- [x] Add ANY request method in router
- [ ] We need two routes for the table schema controller for a better RESTful 
      urls i.e. /foo to get a collection /foo/1 for a specific entry (we have
      deprecated the table controller)
  - [ ] This requires that we need some way to describe the needed routes for
        a controller. Or we add multiple routes which point to the controller
- [x] WSDL generation problem for routes with dynamic parts
- [ ] Add template helper which simplifies working with a HATEOAS array. You can
      get a title or href by rel. Probably not needed since in almost every case 
      we render HATEOAS arrays through a data and not a template writer #6
- [x] Add proxy controller which can be used to fix the WSDL dynmaic part 
      problem. The proxy controller redirects calls to other controllers. 
      Useful for all service which have one entry point OData, GraphQL
- [x] Add more http methods to controller abstract / schema api
  - [x] Support http method PATCH. Json patch format (rfc6902) is already 
        implemented in #13
- [x] Option to setup global middlewares inside the configuration.php which are
      loaded for all controllers. Maybe we can specifiy pre and post controller
      middlewares #15
- [x] Rename object to something else since object is a keyword in php7
- [x] Accessor work with / insteadof . as seperator
- [x] Build on validator and remove Record/Array validator which can handle all
      nested types stdClass/array/RecordInterface and which also allows to 
      specify paths i.e. /foo/bar for a property
  - [x] Allow validator to specifiy widl card in path i.e. /entry/*/title. So we
        can match i.e. entries inside an array
- [x] Schema html renderer, schema property which was referenced was not 
      rendered
- [x] Add schema "any" element which behaves like an array but as object with 
      arbitrary keys
- [x] Add class to apply a JSON patch format on an object structure
- [x] Add API test case for choice property type
- [ ] Probably add XML-RPC protocol reader/writer visitor. (XML-RPC is really
      outdated since we already have SOAP its maybe not needed)
- [ ] Probably rename Context::KEY_* to PSX_*
- [x] Add writer test with empty records
- [x] Probably use readthedocs.org for documentation hosting
- [x] Add security disclosure link to website (PSR-9)
- [x] Add commands which generate different formats
  - [x] Add command to generate a raml definition from a documented API
- [x] Additional parsing commands
  - [x] Add command which parses a json schema file and display what PSX could
        understand. Useful for debugging
  - [x] Add command which parses a raml file and display what PSX could
        understand. Useful for debugging
- [x] If 1.0 is released create 1.x branch
- [x] Register PSX at http://www.programmableweb.com/
- [ ] Possibility to throw an exception which has a response record attached 
      which gets displayed to the user. We dont want do that since this could be 
      abused as normal output channel
- [x] Add PSX value objects probably in seperate folder/package there we can 
      have Uri, Url, Urn, DateTime, Cookie, MediaType and maybe add new types 
      Date and Time. Also the Property\* objects are kind of value objects
      Value objects were unified
- [x] The exception controller must have the same supported writers as the  
      origin controller
- [x] Add begin/cancel/rollback transaction methods to TableManager. Probably 
      add method which takes a Closure where the code is executed in a 
      transaction
- [x] check "Invalid url syntax" exception whenn calling urls i.e. 
      http://127.0.0.1/projects/comparabl/public/?project=symfony%2Fsymfony&source=1&destination=2
- [x] Check whether we produce the right format for html/svg etc. on an 
      exception. Probably force same format from controller for error response
- [x] When passing a custom Accept header containing a version i.e. 
      application/vnd.foobar.v2+json check whether the +[format] is considered
- [ ] Fix correct handling with Accept: text/foo header (If the accept header is 
      not known we serve as fallback json)
- [x] Move todo and bugs to github
- [x] When using raml/json schema for resource description their are some 
      limitations
  - [x] We can not use all property types i.e. date time
  - [x] We can not reference other schemas within a schema
- [x] Add query parameters to documentation
- [x] Add support for jsonx 
      http://www-01.ibm.com/support/knowledgecenter/SS9H2Y_7.1.0/com.ibm.dp.doc/json_jsonxconversionrules.html
- [x] Solve fallback template html vs svg problem
- [x] Make protocol optional in "psx_url" and use $_SERVER['HTTPS'] to determine
      protocol
- [x] Add buffered stream to read multiple times from read-only stream
- [x] Change license to Apache License 2
- [x] Check whether query parameters are transmitted when sending a http request
      probably we need a method which returns us [path] + [query] ~ [fragment]
- [x] Problem schema type XSD writer property can not have same names. Either 
      throw an exception or make it work
- [x] Rename getChild in Schema to getProperty etc. (renamed to get)
- [x] Add RAML export to documentation controller
- [x] Use DBAL query builder in MysqlDescribe and Session\Handler\Sql
- [x] Think about return a plain array for sql table so that the result is more
      light and can be used in CurveArray
- [x] Write errors to log when in production mode
- [x] Fix RecordStore references
- [x] probably add cache controller which does caching through http around a 
      haevy task
- [ ] Pubsubhubbub provider implementation (moved to psx-ws)
- [ ] Update pshb subscriber to 0.4 spec (moved to psx-ws)
- [ ] Implement webfinger provider serving XML (XRD) and JSON (JRD)
      http://tools.ietf.org/html/draft-jones-appsawg-webfinger-03 (moved to psx-ws)
- [ ] Implement foaf, activity streams, microformat parser (moved to psx-ws)
- [ ] Implement OpenId connect based on Oauth 2.0 (moved to psx-ws)
- [ ] Test webfinger and XRDS with OpenId and update HostMeta to extend XRD (moved to psx-ws)
- [ ] Implement openid connect basic (http://openid.net/specs/openid-connect-basic-1_0-28.html) (moved to psx-ws)
  - [x] Add JWS (http://tools.ietf.org/html/draft-ietf-jose-json-web-signature-11)
  - [x] Add JWT (http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-08)
- [ ] Update XRD classes using DomDocument according to
      http://docs.oasis-open.org/xri/xrd/v1.0/os/xrd-1.0-os.html (moved to psx-ws)
- [ ] Add XRDS class using old XRD class see
      http://yadis.org/wiki/Yadis_1.0_%28HTML%29 (moved to psx-ws)
- [x] Use entity manager in entity importer
- [x] Add help command which displays all needed settings for a command
- [x] Remove calls to deprecated methods from the location object
- [x] Update JWT to current spec
- [x] Oauth filter only for specific request methods
- [x] Add cache filter or/and response cache which simply writes the response 
      into a file using the psr cache
- [x] Create generate command which generate i.e. controller, commands, schema
      or other classes
- [ ] Probably use willdurand/negotiation for content negotiation index,php and 
      console.php- We have implemented a custom contnent negotiation rule since
- [x] Move html in its own repo
- [x] Move payment into own repo
- [x] Check if an exception gets thrown in a template that it gets correctly 
      bubbeled to the user
- [x] Dependency tool controller
- [x] Probably add DI container in its own file which we can then include in the
- [x] Add event dispatcher to important places i.e. when a request arrives, 
      when a route was found, etc. (event listener should not be able to modify
      the request or response only read)
- [x] Build commands using the symfony command system
- [x] Swagger generator controller
- [x] Update docblock using namespaces and update copyright
- [x] Test API status controller
- [x] Probably create xmlrpc or SOAP gateway
- [x] Howto handle API version managment
- [x] throw exception "response already writen" if setBody is called multiple 
      times
- [x] setBody add serializer which extracts the fields from the properties of a 
      normal object
- [x] Routes use ~/foo to set an alias for a specific path
- [x] Try to hackify PSX code using the hackificator.log
- [x] Type-Hint Psr\Http\StreamInterface
- [x] Implement Oauth 2.0 (consumer done so far)
- [x] Add paypal payment builder
- [ ] Implement magicsig and salmon (replaced by JWT)
- [x] Add dynamic table wich uses describe to build a table class based on the
      table name
- [x] Add url resolver to resolve a href to a specific url
- [ ] Add sitemap reader (add record importer wich can import sitemap data into 
      a record collection)
- [x] Handler can define wich record class should be used (i.e. getRecordClass)
- [x] Allow _ underscore in resource path to prettify folder structure
- [x] Move setResponse from api to controller
- [x] Remove annotation parsing from the controller
- [x] Improved router
- [x] Add routing mechanism to template
- [x] Create video tutorial showcasing features of psx (update website)
- [x] Build rss and atom writer class wich is used in data writer methods
- [x] Try to use doctrine repository interface for handler
- [x] Update docblock of each class and remove revision tag
- [x] Implement namespaces
- [x] Add getter methods for acticity stream
- [x] Rewrite data system so that writers can be registered instead of being 
      hardcoded in the factory
- [x] Add jsonp writer
- [x] Update webfinger to current spec http://www.rfc-editor.org/rfc/rfc7033.txt
- [x] Fix html filter iframe must closed with </iframe> and not <iframe />
- [x] Improve dependency managment we have an dependency bag where dependencies 
      can be added this bag is injected into each module
- [x] Add swagger geenrator
- [x] Fix build scripts using git
- [x] Add continious integration (http://travis-ci.org)
- [x] Move from svn to git repo
- [x] Add session save path to session class. Probably set this default to 
      PSX_PATH_CACHE
- [x] build psx package (http://packagist.org)
- [x] test paypal IPN endpoint and implement express checkout
- [ ] Add setter methods for opensocial records
- [x] Add HTTP_HeadRequest class
- [x] write better and more complex tests
- [ ] build XRDS component srv class wich is used by opensocial and maybe oauth  
      to declare their services
- [x] Add OpenSocial records
- [x] Fix Atum/Entry source attribute. source element contains complete feed
      without feed tag
- [x] SQL condition implement functions in parser
- [x] Oembed provider implementation
- [x] add webfinger library (host-meta, lrdd discovery)
- [x] update documentation to new coding standard
- [x] rewrite manual to version 0.3
- [x] replace the stream wrapper by a import function because this is not so
      error-prone
- [x] rewrite tests
- [x] implement a mechanism so that you cant call files directly i.e. check 
      whether the const in_psx is set.
- [x] over write the X-Powered-By so that you are more anonymous and an attacker 
      cant get your php version
- [x] replace the foreach loops in common.php by hardcode the values is faster
- [x] remove the ini.xml and hardcode the settings is faster
- [x] add component util/grid (data in table + sorting + grid) all without
      javascript
- [x] add component util/paging

