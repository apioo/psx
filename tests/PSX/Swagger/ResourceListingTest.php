<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Swagger;

use PSX\Data\SerializeTestAbstract;

/**
 * ResourceListingTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceListingTest extends SerializeTestAbstract
{
    public function testSerialize()
    {
        $info = new InfoObject();
        $info->setTitle('Swagger Sample App');
        $info->setDescription('This is a sample server Petstore server');
        $info->setTermsOfServiceUrl('http://helloreverb.com/terms/');
        $info->setContact('apiteam@wordnik.com');
        $info->setLicense('Apache 2.0');
        $info->setLicenseUrl('http://www.apache.org/licenses/LICENSE-2.0.html');

        $resourceListing = new ResourceListing('1.0');
        $resourceListing->setInfo($info);
        $resourceListing->addResource(new ResourceObject('/foo', 'Foobar'));
        $resourceListing->addResource(new ResourceObject('/bar', 'Barfoo'));

        $content = <<<JSON
{
  "swaggerVersion": "1.2",
  "apiVersion": "1.0",
  "info": {
    "title": "Swagger Sample App",
    "description": "This is a sample server Petstore server",
    "termsOfServiceUrl": "http://helloreverb.com/terms/",
    "contact": "apiteam@wordnik.com",
    "license": "Apache 2.0",
    "licenseUrl": "http://www.apache.org/licenses/LICENSE-2.0.html"
  },
  "apis": [{
    "path": "/foo",
    "description": "Foobar"
  },{
    "path": "/bar",
    "description": "Barfoo"
  }]
}
JSON;

        $this->assertRecordEqualsContent($resourceListing, $content);
    }
}
