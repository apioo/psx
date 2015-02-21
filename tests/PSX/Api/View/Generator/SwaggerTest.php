<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Api\View\Generator;

use DOMDocument;
use PSX\Api\View;

/**
 * SwaggerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SwaggerTest extends GeneratorTestCase
{
	public function testGenerate()
	{
		$generator = new Swagger(1, 'http://api.phpsx.org', 'http://foo.phpsx.org');
		$json      = $generator->generate($this->getView());

		$expect = <<<'JSON'
{
    "swaggerVersion": "1.2",
    "apiVersion": 1,
    "basePath": "http:\/\/api.phpsx.org",
    "resourcePath": "\/foo\/bar",
    "apis": [
        {
            "path": "\/foo\/bar",
            "operations": [
                {
                    "method": "GET",
                    "nickname": "getCollection",
                    "parameters": [],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Response",
                            "responseModel": "getResponse"
                        }
                    ]
                },
                {
                    "method": "POST",
                    "nickname": "postItem",
                    "parameters": [
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "postRequest"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Response",
                            "responseModel": "postResponse"
                        }
                    ]
                },
                {
                    "method": "PUT",
                    "nickname": "putItem",
                    "parameters": [
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "putRequest"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Response",
                            "responseModel": "putResponse"
                        }
                    ]
                },
                {
                    "method": "DELETE",
                    "nickname": "deleteItem",
                    "parameters": [
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "deleteRequest"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Response",
                            "responseModel": "deleteResponse"
                        }
                    ]
                }
            ]
        }
    ],
    "models": {
        "getResponse": {
            "id": "getResponse",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "ref7738db4616810154ab42db61b65f74aa"
                    }
                }
            }
        },
        "postRequest": {
            "id": "postRequest",
            "required": [
                "title",
                "date"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string"
                }
            }
        },
        "postResponse": {
            "id": "postResponse",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "putRequest": {
            "id": "putRequest",
            "required": [
                "id"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string"
                }
            }
        },
        "putResponse": {
            "id": "putResponse",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "deleteRequest": {
            "id": "deleteRequest",
            "required": [
                "id"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string"
                }
            }
        },
        "deleteResponse": {
            "id": "deleteResponse",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "ref7738db4616810154ab42db61b65f74aa": {
            "id": "ref7738db4616810154ab42db61b65f74aa",
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string"
                }
            }
        }
    }
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, $json);
	}
}
