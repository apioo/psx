
Object graph
============

Abstract
--------

With object graph we mean the response which is set in the controller as body.
This can be i.e. a simple associative array or a deep nested object structure. 
PSX walks through the object graph and determines whether a value represents an 
object, array or scalar value. If the value is a PSX\\Data\\RecordInterface, 
stdClass or associative array the value gets treated as object. If it is an 
array the value gets treated as array. All other values have no structural 
meaning and are treated as scalar values. If a scalar value is an object PSX 
tries to case it to a string.

Examples
--------

In the following some examples to show how PSX handles the object graph 

.. code-block:: php

    <?php

    $this->setBody(array(
        'foo' => 'bar'
    ));


.. code-block:: json

    {
        "foo": "bar"
    }


.. code-block:: php

    <?php

    $this->setBody(array(
        'foo' => array('foo', 'bar')
    ));


.. code-block:: json

    {
        "foo": [
            "foo",
            "bar"
        ]
    }


.. code-block:: php

    <?php

    $this->setBody(array(
        'entry' => array(
            array(
                'title' => 'foo'
            ),
            array(
                'title' => 'foo'
            )
        )
    ));


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

    $this->setBody(array(
        'foo' => new Object(array(
            'title' => 'bar'
        ))
    ));


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

    $this->setBody(array(
        'foo' => $body
    ));


.. code-block:: json

    {
        "foo": {
            "title": "foo"
        }
    }
