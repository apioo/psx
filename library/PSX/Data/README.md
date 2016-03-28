PSX Data
===

## About

Data processing library which helps to read and write data to and from POPOs
in different formats.

## Usage

The following example showcases how you could read/write a complex model.

```php
// create processor
$processor = new Processor(Configuration::createDefault());

// example json data which we want to parse in our model
$in = <<<JSON
{
    "id": 1,
    "title": "Lorem ipsum",
    "author": {
        "id": 1,
        "name": "Foo",
        "email": "foo@bar.com",
    },
    "comments": [{
        "id": 1,
        "author": {
            "id": 1,
            "name": "Foo",
            "email": "foo@bar.com",
        },
        "text": "Lorem ipsum"
    },{
        "id": 2,
        "author": {
            "id": 1,
            "name": "Foo",
            "email": "foo@bar.com",
        },
        "text": "Lorem ipsum"
    }],
    "date": "2016-03-28T22:40:00Z"
}
JSON;

// reads the json data into a custom model class
$model = $processor->read(News::class, Payload::json($in));

// the model can be used to get or set data
$model->getAuthor()->getName();
$model->getComments()[0]->getText();

// writes the model back to json
$out = $processor->write(Payload::json($model));

// model classes
class News
{
    /**
     * @Type("integer")
     */
    protected $id;

    /**
     * @Type("string")
     */
    protected $title;

    /**
     * @Type("Acme\Author")
     */
    protected $author;

    /**
     * @Type("array<Acme\Comment>")
     */
    protected $comments;

    /**
     * @Type("datetime")
     */
    protected $date;

    // getter/setter implementations removed for readability
}

class Author
{
    /**
     * @Type("integer")
     */
    protected $id;

    /**
     * @Type("string")
     */
    protected $name;

    /**
     * @Type("string")
     */
    protected $email;

    // getter/setter implementations removed for readability
}

class Comment
{
    /**
     * @Type("integer")
     */
    protected $id;

    /**
     * @Type("Acme\Author")
     */
    protected $author;

    /**
     * @Type("string")
     */
    protected $text;

    // getter/setter implementations removed for readability
}


```

## Formats

The library supports different reader and writer classes to produce different
data formats. If you want to read a specific format you can provide the content
type of the data. I.e. if you want read XML you could use the following
payload:

```php
$payload = Payload::create($in, 'application/xml');
```

The processor uses a reader factory to obtain the fitting reader for a specific
content type. In this case it would use the XML reader. The reader factory can
be easily extended with different reader classes to support other data formats.

```php
$configuration->getReaderFactory()->addReader(new Acme\Reader(), 32);
```

In order to produce a payload from an incoming HTTP request you simply have to
set the body as data and the content type from the header. How you access this
data depends on the HTTP interface. For an PSR-7 request you could use:

```php
$payload = Payload::create(
    (string) $request->getBody(),
    $request->getHeaderLine('Content-Type')
);
```

On the other hand if you want to write data as response you would use:

```php
$payload = Payload::create(
    $model,
    $request->getHeaderLine('Accept')
);
```

When writing data the same mechanism is used with a writer factory. The writer
factory can also be extended with custom writer implementations.

```php
$configuration->getWriterFactory()->addWriter(new Acme\Writer(), 64);
```

