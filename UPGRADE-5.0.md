
# Upgrade from 4.x to 5.x

This upgrade contains some breaking changes so that you are required to adjust
your code. The following list contains all important changes:

* It is not longer possible to define a specific method in a route i.e. 
  `MyController::doFoo`. Each resource has one specific controller
* Added a php routing parser which can read all routes from a simple PHP file
  which returns an array. Through this it is now also possible to use a Closure
  as controller. To use this you only have to rename your `routes` file to 
  `routes.php`
* ControllerAbstract
    * `on*` methods have now a `RequestInterface` and `ResponseInterface` as 
      argument
    * The request and response object is now passed as argument and is not 
      available at the constructor
    * Most methods inside the controller which call the request or response 
      property are deprecated instead it is recommended to work directly on the 
      http object
* SchemaApiAbstract
    * `do*` methods have now a `HttpContextInterface` argument
* Framework context object has now explicit getter and setter methods instead 
  of arbitrary key value entries
* The framework has no more a html, text and svg writer. To produce a html 
  response you should extend the `ViewAbstract` controller and use the `render`
  method to produce a html response using a template engine
* The dependency classes are moved to a separate package. The container is now
  available under the `PSX\Dependency` namespace
    * The namespace of `@Inject` annotation has also changed. In case you load 
      your annotations strictly you need to adjust the namespace

The following list contains all development related changes:

* New services:
    * `request_reader`
    * `response_writer`
* Removed services:
    * `template`
    * `application_stack_factory`
    * `loader_callback_resolver`
* Removed classes:
    * `PSX\Framework\ApplicationStackInterface`
    * `PSX\Framework\Controller\ControllerInterface`
    * `PSX\Framework\Controller\Tool\DocumentationController`
    * `PSX\Framework\Data\Writer\TemplateAbstract`
    * `PSX\Framework\Dispatch\ApplicationStackFactory`
    * `PSX\Framework\Filter\ControllerExecutor`
    * `PSX\Framework\Filter\CookieSigner`
    * `PSX\Framework\Filter\StaticCache`
    * `PSX\Framework\Loader\CallbackResolver\DependencyInjector`
* Moved to package psx/dependency
    * `PSX\Framework\Dependency\Annotation\Inject`
    * `PSX\Framework\Dependency\Container`
    * `PSX\Framework\Dependency\NotFoundException`
    * `PSX\Framework\Dependency\ObjectBuilder`
    * `PSX\Framework\Dependency\ObjectBuilderInterface`
* Moved to package psx/http
    * `PSX\Framework\Environment\WebServer\RequestFactoryInterface`
    * `PSX\Framework\Environment\WebServer\ResponseFactoryInterface`
    * `PSX\Framework\Environment\WebServer\SenderInterface`
    * `PSX\Framework\Filter\Backstage`
    * `PSX\Framework\Filter\BrowserCache`
    * `PSX\Framework\Filter\Condition\RequestMethodChoice`
    * `PSX\Framework\Filter\DigestAccessAuthentication`
    * `PSX\Framework\Filter\FilterChain`
    * `PSX\Framework\Filter\IpFirewall`
    * `PSX\Framework\Filter\UserAgentEnforcer`

Because every endpoint has now a dedicated endpoint class we have split up the 
documentation controller into two classes. So you have to adjust your routes 
file in case you are using those controllers:

    PSX\Framework\Controller\Tool\DocumentationController::doIndex
    PSX\Framework\Controller\Tool\DocumentationController::doDetail

becomes:

    PSX\Framework\Controller\Tool\Documentation\IndexController
    PSX\Framework\Controller\Tool\Documentation\DetailController
