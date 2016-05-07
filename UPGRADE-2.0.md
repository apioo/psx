
# Upgrade from 1.x to 2.x

With the 2.x release we moved from a monolithic library to multiple components. 
That means we have split up the framework into components which can also be used
independent of the framework. The following list contains all important class
changes.

* The `RecordInterface` type hint and `Version` parameter from the 
  `SchemaApiAbstract::do*` methods were removed i.e.:  
  __Before:__  
  `protected function doPost(RecordInterface $record, Version $version)`  
  __After:__  
  `protected function doPost($record)`  

* The following controller classes have changed: 
    * `PSX\Controller\ApiAbstract` => `PSX\Framework\Controller\ApiAbstract`
    * `PSX\Controller\SchemaApiAbstract` => `PSX\Framework\Controller\SchemaApiAbstract`
    * `PSX\Controller\TableApiAbstract` => (removed)
    * `PSX\Controller\ViewAbstract` => `PSX\Framework\Controller\ViewAbstract`
    * `PSX\ControllerAbstract` => `PSX\Framework\Controller\ControllerAbstract`
    * `PSX\Controller\Tool\CommandController` => (removed)
    * `PSX\Controller\Tool\DefaultController` => `PSX\Framework\Controller\Tool\DefaultController`
    * `PSX\Controller\Tool\DiscoveryController` => `PSX\Framework\Controller\Tool\DiscoveryController`
    * `PSX\Controller\Tool\DocumentationController` => `PSX\Framework\Controller\Tool\DocumentationController`
    * `PSX\Controller\Tool\RoutingController` => `PSX\Framework\Controller\Tool\RoutingController`
    * `PSX\Controller\Tool\RamlGeneratorController` => `PSX\Framework\Controller\Generator\RamlController`
    * `PSX\Controller\Tool\SwaggerGeneratorController` => `PSX\Framework\Controller\Generator\SwaggerController`
    * `PSX\Controller\Tool\WsdlGeneratorController` => `PSX\Framework\Controller\Generator\WsdlController`
    * `PSX\Controller\Tool\SoapProxyController` => `PSX\Framework\Controller\Proxy\SoapController`

* Change all controller extends from `AnnotationApiAbstract` to `SchemaApiAbstract`
  The `SchemaApiAbstract` controller contains now by default the annotation 
  parser. The class `AnnotationApiAbstract` still exists but is deprecated.

* The signature of the `getDocumentation` method has changed:  
  __Before:__  
  `public function getDocumentation()`  
  __After:__  
  `public function getDocumentation($version = null)`  
  Also the method returns now directly a `PSX\Api\Resource` object.

* The default record implementation has changed. Previously you could access
  a property through a magic method call. This is not longer possible instead 
  you have to access directly the property.
  __Before:__  
  `$record->getTitle()`  
  __After:__  
  `$record->title`  

* DI container service changes:  
    * `http` => `http_client`
    * `io` => (added)
    * `api_parser` => (added)
    * `annotation_reader_controller` => (added)
    * `command_factory` => (removed)
    * `command_output` => (removed)
    * `executor` => (removed)
    * `reader_factory` => (removed)
    * `writer_factory` => (removed)
    * `transformer_manager` => (removed)
    * `importer_manager` => (removed)
    * `schema_validator` => (removed)
    * `schema_assimilator` => (removed)
    * `record_factory_factory` => (removed)
    * `importer` => (removed)
    * `extractor` => (removed)
    * `serializer` => (removed)

* The following list contains common classes which have changed:  
    * `PSX\Base` => `PSX\Framework\Base`
    * `PSX\Cache` => `PSX\Cache\Pool`
    * `PSX\Config` => `PSX\Framework\Config\Config`
    * `PSX\Data\RecordInterface` => `PSX\Record\RecordInterface`
    * `PSX\Util\CurveArray` => `PSX\Data\Util\CurveArray`
    * `PSX\DateTime` => `PSX\DateTime\DateTime`
    * `PSX\Event` => `PSX\Framework\Event\Event`
    * `PSX\FilterAbstract` => `PSX\Validate\FilterAbstract`
    * `PSX\Http` => `PSX\Http\Client`
    * `PSX\Json` => `PSX\Json\Parser`
    * `PSX\Uri` => `PSX\Uri\Uri`
    * `PSX\Url` => `PSX\Uri\Url`
    * `PSX\Urn` => `PSX\Uri\Urn`
    * `PSX\Sql` => `PSX\Sql\Sql`

* Most applications should work after these changes. The following list contains
  all namespace changes:
    * `PSX\ActivityStream\*` -> `PSX\Model\ActivityStream\*`
    * `PSX\Annotation\*` -> `PSX\Framework\Annotation\*`, `PSX\Api\Annotation\*`
    * `PSX\Atom\*` -> `PSX\Model\Atom\*`
    * `PSX\Command` => (Removed)
    * `PSX\Config` -> `PSX\Framework\Config`
    * `PSX\Console` -> `PSX\Framework\Console`
    * `PSX\Controller` -> `PSX\Framework\Controller`
    * `PSX\Dependency` -> `PSX\Framework\Dependency`
    * `PSX\Dispatch` -> `PSX\Framework\Dispatch`
    * `PSX\Event` -> `PSX\Framework\Event`
    * `PSX\Exception` -> `PSX\Framework\Exception`
    * `PSX\File` -> (Removed)
    * `PSX\Filter` -> `PSX\Validate\Filter`
    * `PSX\Loader` -> `PSX\Framework\Loader`
    * `PSX\Log` -> `PSX\Framework\Log`
    * `PSX\Rss` -> `PSX\Model\Rss`
    * `PSX\Session` -> `PSX\Framework\Session`
    * `PSX\Swagger` -> `PSX\Model\Swagger`
    * `PSX\Template` -> `PSX\Framework\Template`
    * `PSX\Test` -> `PSX\Framework\Test`
    * `PSX\Upload` -> `PSX\Framework\Upload`
    * `PSX\Util` -> `PSX\Framework\Util`

