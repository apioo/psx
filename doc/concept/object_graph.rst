
Object graph
============

Abstract
--------

With object graph we mean the response which is set in the controller as body.
This can be i.e. a simple associative array or a deep nested object structure. 
PSX walks through the object graph and determines whether a value represents an 
object, array or scalar value. If the value is a PSX\\Record\\RecordInterface,
stdClass or associative array the value is treated as object. If it is an
array the value gets treated as array. All other values have no structural 
meaning and are treated as scalar values. If a scalar value is an object PSX 
tries to cast it to a string.

Examples
--------

In the following some examples to show how PSX handles the object graph 

.. code-block:: php

    <?php

    $this->setBody([
        'foo' => 'bar'
    ]);


.. code-block:: json

    {
        "foo": "bar"
    }


.. code-block:: php

    <?php

    $this->setBody([
        'foo' => ['foo', 'bar']
    ]);


.. code-block:: json

    {
        "foo": [
            "foo",
            "bar"
        ]
    }


.. code-block:: php

    <?php

    $this->setBody([
        'entry' => [
            [
                'title' => 'foo'
            ],
            [
                'title' => 'foo'
            ]
        ]
    ]);


.. code-block:: json

    {
        "entry": [
            {
                "title": "foo"
            },
            {
                "title": "foo"
            }
        ]
    }

.. code-block:: php

    <?php

    $this->setBody([
        'foo' => new Object([
            'title' => 'bar'
        ])
    ]);


.. code-block:: json

    {
        "foo": {
            "title": "bar"
        }
    }


.. code-block:: php

    <?php

    $body = new \stdClass();
    $body->title = 'foo';

    $this->setBody([
        'foo' => $body
    ]);


.. code-block:: json

    {
        "foo": {
            "title": "foo"
        }
    }


.. code-block:: php

    <?php

    $this->setBody([
        'foo' => new \ArrayIterator(['foo', 'bar'])
    ]);


.. code-block:: json

    {
        "foo": [
            "foo",
            "bar"
        ]
    }


.. code-block:: php

    <?php

    $generator = function(){
        foreach (['foo', 'bar'] as $value) {
            yield $value;
        }
    };

    $this->setBody(array(
        'foo' => $generator()
    ));


.. code-block:: json

    {
        "foo": [
            "foo",
            "bar"
        ]
    }
