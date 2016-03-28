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

