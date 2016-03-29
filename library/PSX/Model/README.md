PSX ActivityStream
===

## About

This library contains simple POPO which model common data formats. At the moment
we support the following data formats:

* ActivityStream
* Atom
* Rss
* Swagger

These classes can be used in combination with psx/data to consume or produce
data.

## Usage

```php
<?php



```



PSX ActivityStream
===

## About

Model classes to build [ActivityStream](http://activitystrea.ms/) JSON 
responses. More informations about PSX at [phpsx.org](http://phpsx.org/)

## Usage

```php
$object = new ObjectType();
$object->setObjectType('article');
$object->setId('urn:example:blog:abc123/xyz');
$object->setUrl('http://example.org/blog/2011/02/entry');
$object->setDisplayName('Why I love Activity Streams');

$image = new ObjectType();
$image->setUrl('http://example.org/martin/image.jpg');
$image->setMediaType('image/jpeg');
$image->setWidth(250);
$image->setHeight(250);

$actor = new ObjectType();
$actor->setObjectType('person');
$actor->setId('urn:example:person:martin');
$actor->setDisplayName('Martin Smith');
$actor->setUrl('http://example.org/martin');
$actor->setImage($image);

$activity = new Activity();
$activity->setVerb('post');
$activity->setPublished(new DateTime('2011-02-10T15:04:55Z'));
$activity->setLanguage('en');
$activity->setActor($actor);
$activity->setObject($object);
```

The activity model can be serialized to the following JSON

```json
{
  "verb": "post",
  "actor": {
    "id": "urn:example:person:martin",
    "objectType": "person",
    "displayName": "Martin Smith",
    "url": "http://example.org/martin",
    "image": {
      "mediaType": "image/jpeg",
      "url": "http://example.org/martin/image.jpg",
      "height": 250,
      "width": 250
    }
  },
  "object": {
    "id": "urn:example:blog:abc123/xyz",
    "objectType": "article",
    "displayName": "Why I love Activity Streams",
    "url": "http://example.org/blog/2011/02/entry"
  }
  "language": "en",
  "published": "2011-02-10T15:04:55Z"
}
```
