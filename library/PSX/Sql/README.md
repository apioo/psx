Structor
========

## About

Structor is a simple library which helps you to build complex data structures.
In example if you want to provide the following JSON based on a SQL database:

```json
{
    "totalEntries": 2,
    "entries": [
        {
            "id": 1,
            "title": "Foo",
            "isNew": true,
            "author": {
                "displayName": "Foo Bar",
                "uri": "http:\/\/phpsx.org"
            },
            "date": "2016-03-01T00:00:00+01:00",
            "links": {
                "self": "http:\/\/foobar.com\/news\/1"
            }
        },
        {
            "id": 2,
            "title": "Bar",
            "isNew": true,
            "author": {
                "displayName": "Foo Bar",
                "uri": "http:\/\/phpsx.org"
            },
            "date": "2016-03-01T00:00:00+01:00",
            "links": {
                "self": "http:\/\/foobar.com\/news\/2"
            }
        }
    ]
}
```

To produce such a result we first need to get the data from the database. Today
most database libraries or ORMs are not designed to produce such nested results.
They either return a simple array or model objects in case of an ORM. Turning
these results into the desired format is mostly no easy task.

Structor simplifies this process by providing a simple builder to create fully
customizable responses. To produce the response above we could write the
following code:

```php
use PSX\Structor\Field;
use PSX\Structor\Provider\PDO;
use PSX\Structor\Reference;

$provider = new PDO\Factory($pdo);

$definition = [
    'totalEntries' => 2,
    'entries' => $provider->newCollection('SELECT id, authorId, title, createDate FROM news ORDER BY createDate DESC', [], [
        'id' => 'id',
        'title' => new Field\Callback('title', function($title){
            return ucfirst($title);
        }),
        'isNew' => new Field\Value(true),
        'author' => $provider->newEntity('SELECT name, uri FROM author WHERE id = :id', ['id' => new Reference('authorId')], [
            'displayName' => 'name',
            'uri' => 'uri',
        ]),
        'date' => new Field\DateTime('createDate'),
        'links' => [
            'self' => new Field\Replace('http://foobar.com/news/{id}'),
        ]
    ])
];

$builder = new Builder();
$result  = $builder->build($definition));

// echo json_encode($result);
```

In this example it would be much more performant to simply join the author table
but the example should show how to handle nested queries.

Structor aims not to be a replacement for existing database or ORM libraries
instead it should be used in conjunction to produce complex data views.

## Concept

Structor uses a concept called providers. A provider is a simple class which
either returns a collection or entity. Collection means it returns an array
which contains array entities. A entity means it returns a simple associative
array containing the fields of the entity.

At the moment we have a PDO and Doctrine DBAL provider but it should be easy
possible to writer providers for other systems. Feel free to send a pull
request.
