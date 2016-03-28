PSX Data
===

## About

[PSR-6](http://www.php-fig.org/psr/psr-6/) implementation using the doctrine 
cache system. This implementation is used by the PSX framework. More 
informations about PSX at http://phpsx.org

## Usage

```php
<?php

$pool = new PSX\Cache\Pool(new Doctrine\Common\Cache\FilesystemCache());
$item = $pool->getItem('foo');

if (!$item->isHit()) {
    $value = doComplexTask();

    $item->set($value);
    $item->expiresAfter(3600);

    $pool->save($item);
} else {
    $value = $item->get();
}

// @TODO work with the value
    
```


$config = Configuration::createDefault();
$dm     = new DataManager($config);



$data   = $dm->getBodyAs(Foo::class, 'application/json', '{"foo": "bar"}');




$schema = JsonSchema::fromFile('incoming.json');
$data   = $dm->getBodyAs($schema, 'application/json', '{"foo": "bar"}');





$dm->writeBody($data);







class Foo
{
    protected $foo;

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
}



$schema = JsonSchema::fromFile('outgoing.json');
$data = $dm->getAssimilator()->assimilate($schema, $data);


$data = $dm->getWriter('application/xml')->write($data);


$foo = $dm->getAccessor($data)->get('/foo');


{
	"title": "Example Schema",
	"type": "object",
	"properties": {
		"foo": {
			"type": "string"
		},
	}
}





$data = $dm->getReader('application/json')->read('{"foo": "bar"}');

object(stdClass)#2 (1) {
  ["foo"]=>
  string(3) "bar"
}

$data = $dm->getWriter('application/xml')->write($data);

<?xml version="1.0" encoding="UTF-8"?>
<foo>bar</foo>

$data = $dm->getReader('application/xml')->read($data);

object(DOMDocument)#2 (34) {
    ...
}



$data = $dm->getExtractor()->extract(new Message(['Content-Type' => 'application/json'], '{"foo": "bar"}'));

object(stdClass)#2 (1) {
  ["foo"]=>
  string(3) "bar"
}



$data = $dm->getExtractor()->extract(new Message(['Content-Type' => 'application/xml'], '<foo>bar</foo>'));

object(stdClass)#2 (1) {
  ["foo"]=>
  string(3) "bar"
}



$data = $dm->getExtractor()->extract(new Message(['Content-Type' => 'application/x-www-form-urlencoded'], 'foo=bar'));

object(stdClass)#2 (1) {
  ["foo"]=>
  string(3) "bar"
}





$dm->getAssimilator()->assimilate();




BarFactory implements FactoryInterface
{
	public function __construct(Connection $connection)
	{
	
	}
}

$dm->setFactoryBuilder(new NewBuilder());
$dm->setFactoryBuilder(new ClosureBuilder(function($className) use ($connection){

	if ($className == 'BarFactory') {
		return new BarFactory($connection);
	}

}));





