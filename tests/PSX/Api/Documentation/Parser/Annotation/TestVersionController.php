<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Api\Documentation\Parser\Annotation;

use PSX\Api\Version;
use PSX\Controller\SchemaApiAbstract;

/**
 * TestController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 *
 * @Title("Test v1", version="1")
 * @Description("Test description v1", version="1")
 * @PathParam(name="fooId1", type="string", version="1")
 *
 * @Title("Test v2", version="2")
 * @Description("Test description v2", version="2")
 * @PathParam(name="fooId2", type="string", version="2")
 *
 * @Title("Test v3", version="3")
 * @Description("Test description v3", version="3")
 * @PathParam(name="fooId3", type="string", version="3")
 *
 * @Title("Test v4", version="4")
 * @Description("Test description v4", version="4")
 * @PathParam(name="fooId4", type="string", version="4")
 *
 * @Title("Test v5", version="5")
 * @Description("Test description v5", version="5")
 * @PathParam(name="fooId4", type="string", version="5")
 *
 * @Version(version="1", status=PSX\Api\Resource::STATUS_CLOSED)
 * @Version(version="2", status=PSX\Api\Resource::STATUS_DEPRECATED)
 * @Version(version="3", status=PSX\Api\Resource::STATUS_ACTIVE)
 * @Version(version="4", status=PSX\Api\Resource::STATUS_DEVELOPMENT)
 */
class TestVersionController extends SchemaApiAbstract
{
    public function __construct()
    {
    }

    public function getDocumentation()
    {
        return null;
    }

    /**
     * @Description("Test description v1", version="1")
     * @QueryParam(name="string1", type="string", version="1")
     * @Incoming(schema="../schema/schema1.json", version="1")
     * @Outgoing(code=200, schema="../schema/schema1.json", version="1")
     *
     * @Description("Test description v2", version="2")
     * @QueryParam(name="string2", type="string", version="2")
     * @Incoming(schema="../schema/schema2.json", version="2")
     * @Outgoing(code=200, schema="../schema/schema2.json", version="2")
     *
     * @Description("Test description v3", version="3")
     * @QueryParam(name="string3", type="string", version="3")
     * @Incoming(schema="../schema/schema3.json", version="3")
     * @Outgoing(code=200, schema="../schema/schema3.json", version="3")
     *
     * @Description("Test description v4", version="4")
     * @QueryParam(name="string4", type="string", version="4")
     * @Incoming(schema="../schema/schema4.json", version="4")
     * @Outgoing(code=200, schema="../schema/schema4.json", version="4")
     *
     * @Description("Test description v5", version="5")
     * @QueryParam(name="string5", type="string", version="5")
     * @Incoming(schema="../schema/schema5.json", version="5")
     * @Outgoing(code=200, schema="../schema/schema5.json", version="5")
     */
    protected function doGet(Version $version)
    {
    }
}
