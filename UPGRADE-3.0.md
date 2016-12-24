
# Upgrade from 2.x to 3.x

The document explains the major changes between the 2.x and 3.x release and what
steps must be taken to successfully upgrade an 2.x installation.

In the previous 2.x release the response data of a controller was adjusted to 
the schema. That means that fields which were not declared in the schema were 
removed from the response. In the 3.x release the response schema is only used 
for documentation purpose and does not change the response in any way. This 
increases the performance and simplifies the development.

With the 3.x release the json schema implementation was extended to cover all
aspects of the specification (the library passes now also the official json 
schema test suite). Because of this we have removed the XSD implementation since 
it is not possible to fully transform a json schema to XSD. The WSDL generator 
was also removed since it is based on the XSD generator.

The framework generates now RAML 1.0 and Swagger 2.0 specifications.

The following list contains changes which you should consider when upgrading to 
3.x.

* `PSX\Controller\ControllerAbstract`
    * If you are using a custom validator with `getBodyAs` the interface has changed
      from `\PSX\Validate\ValidatorInterface` to `\PSX\Schema\Validation\ValidatorInterface`
    * The `setBodyAs` method was removed (use the `setBody` method instead)
    * The `getAccessor` method was removed (use the static method `Accessor::get` instead)

* `PSX\Controller\SchemaApiAbstract`
    * The property `$apiParser` was removed and a new property `$apiManager` was
      added which contains an instance of `PSX\Api\ApiManager`
    * The `getValidator` method must now return an instance of `PSX\Schema\ValidatorInterface`

* `PSX\Controller\Generator\SwaggerController`
    * The method `doIndex` and `doDetail` were removed. Because of that the route 
      file must must be changed:
     `PSX\Framework\Controller\Generator\SwaggerController::doIndex` => (removed)
     `PSX\Framework\Controller\Generator\SwaggerController::doDetail` => `PSX\Framework\Controller\Generator\SwaggerController`

* `PSX\Controller\Generator\WsdlController`
    * The WsdlController was removed and should be deleted from the `routes` file

* `PSX\Data\Payload`
    * The `validator` and `revealer` property were removed

* `PSX\Data\Processor`
    * The `read` method accepts now (optional) an schema visitor instance
    * The schema argument was removed from the `write` method
    * The method `assimilate` was removed

* `PSX\Data\Validator\Property`
    * This class was deprecated in favor of `PSX\Schema\Validation\Field`

* `PSX\Data\Validator\Validator`
    * This class was deprecated in favor of `PSX\Schema\Validation\Validator`

* `PSX\Schema\Builder`
    * The required properties must be now set (as array of property names) on the 
      complex object instead of specifying the required flag on each property. This
      is in sync with the json schema specification
    * The method `choiceType` was removed
    * The method `complexType` was renamed to `objectType`
    * The method `float` was renamed to `number`

* `PSX\Schema\Property`
    * The method `getAny` was removed (instead you should use `additionalProperties`)
    * The method `getChoice` was removed (instead you should use `oneOf`)

* `PSX\Schema\Property\*`
    * All specific properties like i.e. `PSX\Schema\Property\BooleanType` were 
      removed. You can get the correct type from the `PSX\Schema\Property` factory

* `PSX\Schema\VisitorInterface`
    * The type hints of each method have changed from a specific property to 
      `PropertyInterface`
    * The method `visitComplex` was renamed to `visitObject`
    * The method `visitFloat` was renamed to `visitNumber`

* `PSX\Schema\Visitor\IncomingVisitor`
    * The `IncomingVisitor` was removed instead you should use the `TypeVisitor`

* The following classes were removed
    * `PSX\Data\Validator\ValidatorAbstract` => (removed)
    * `PSX\Schema\ChoiceResolver` => (removed)
    * `PSX\Schema\Parser\Popo\TypeParser` => (removed)
    * `PSX\Schema\Visitor\OutgoingVisitor` => (removed)
    * `PSX\Schema\Visitor\IncomingVisitor` => (removed)
