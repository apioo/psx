PSX Sql
===

## About

The SQL library helps to manage data from relational databases. It contains a
system to build complex data structures from flat SQL results and a simple
table interface which can be used as an alternative to an ORM. Each table class
represents a table on the database and offers standard methods to CRUD data to
the table. It is also easy possible to add custom methods to a table.

## Complex data structures

At first lets take a look how we can generate the following data structure:

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

The SQL library simplifies this process by providing a simple builder to create
fully customizable responses. To produce the response above we could write the
following code:

```php
use PSX\Sql\Field;
use PSX\Sql\Provider\PDO;
use PSX\Sql\Reference;

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

## Table class

The table class represents a table and provides methods to query and manipulate
data on a table. It is similar to a repository in doctrine except that it works
with plain SQL and does not return entities.

It is recommended to put custom queries in a seperate method into the table
class so that you can reuse such queries across your app. The following methods
are available by default:

```php
// query
$table->getAll();
$table->getBy();
$table->getOneBy();
$table->get();
$table->getCount();

// manipulation
$table->create();
$table->update();
$table->delete();

```

There is also a table manager which can be used to obtain table instances. It
simply passes the database connection to the table instance. You most likely
want to register the table manager at your DI container so that you can easily
use it in your app to obtain a table instance.
