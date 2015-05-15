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

namespace PSX\Console\Debug;

use PSX\Test\CommandTestCase;
use PSX\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * RamlCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlCommandTest extends CommandTestCase
{
	public function testCommand()
	{
		$command = new RamlCommand();

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'file'   => __DIR__ . '/../../Api/Documentation/Parser/test.raml',
			'path'   => '/foo',
			'format' => 'serialize',
		));

		$expect = <<<'PHP'
PSX\Api\Documentation\Version::__set_state(array(
   'resources' => 
  array (
    2 => 
    PSX\Api\Resource::__set_state(array(
       'status' => 1,
       'path' => '/foo',
       'title' => 'Bar',
       'description' => 'Some description',
       'pathParameters' => 
      PSX\Data\Schema\Property\ComplexType::__set_state(array(
         'properties' => 
        array (
        ),
         'name' => 'path',
         'description' => NULL,
         'required' => NULL,
         'reference' => NULL,
      )),
       'methods' => 
      array (
        'GET' => 
        PSX\Api\Resource\Get::__set_state(array(
           'description' => 'Informations about the method',
           'queryParameters' => 
          PSX\Data\Schema\Property\ComplexType::__set_state(array(
             'properties' => 
            array (
              'param_integer' => 
              PSX\Data\Schema\Property\IntegerType::__set_state(array(
                 'max' => 16,
                 'min' => 8,
                 'pattern' => NULL,
                 'enumeration' => NULL,
                 'name' => 'param_integer',
                 'description' => NULL,
                 'required' => true,
                 'reference' => NULL,
              )),
              'param_number' => 
              PSX\Data\Schema\Property\FloatType::__set_state(array(
                 'max' => NULL,
                 'min' => NULL,
                 'pattern' => NULL,
                 'enumeration' => NULL,
                 'name' => 'param_number',
                 'description' => 'The number',
                 'required' => NULL,
                 'reference' => NULL,
              )),
              'param_date' => 
              PSX\Data\Schema\Property\DateTimeType::__set_state(array(
                 'minLength' => NULL,
                 'maxLength' => NULL,
                 'pattern' => NULL,
                 'enumeration' => NULL,
                 'name' => 'param_date',
                 'description' => NULL,
                 'required' => NULL,
                 'reference' => NULL,
              )),
              'param_boolean' => 
              PSX\Data\Schema\Property\BooleanType::__set_state(array(
                 'pattern' => NULL,
                 'enumeration' => NULL,
                 'name' => 'param_boolean',
                 'description' => NULL,
                 'required' => true,
                 'reference' => NULL,
              )),
              'param_string' => 
              PSX\Data\Schema\Property\StringType::__set_state(array(
                 'minLength' => 8,
                 'maxLength' => 16,
                 'pattern' => '[A-z]+',
                 'enumeration' => 
                array (
                  0 => 'foo',
                  1 => 'bar',
                ),
                 'name' => 'param_string',
                 'description' => NULL,
                 'required' => NULL,
                 'reference' => NULL,
              )),
              'pages' => 
              PSX\Data\Schema\Property\FloatType::__set_state(array(
                 'max' => NULL,
                 'min' => NULL,
                 'pattern' => NULL,
                 'enumeration' => NULL,
                 'name' => 'pages',
                 'description' => 'The number of pages to return',
                 'required' => NULL,
                 'reference' => NULL,
              )),
            ),
             'name' => 'query',
             'description' => NULL,
             'required' => NULL,
             'reference' => NULL,
          )),
           'request' => NULL,
           'responses' => 
          array (
          ),
        )),
        'POST' => 
        PSX\Api\Resource\Post::__set_state(array(
           'description' => NULL,
           'queryParameters' => 
          PSX\Data\Schema\Property\ComplexType::__set_state(array(
             'properties' => 
            array (
              'pages' => 
              PSX\Data\Schema\Property\FloatType::__set_state(array(
                 'max' => NULL,
                 'min' => NULL,
                 'pattern' => NULL,
                 'enumeration' => NULL,
                 'name' => 'pages',
                 'description' => 'The number of pages to return',
                 'required' => NULL,
                 'reference' => NULL,
              )),
            ),
             'name' => 'query',
             'description' => NULL,
             'required' => NULL,
             'reference' => NULL,
          )),
           'request' => 
          PSX\Data\Schema::__set_state(array(
             'property' => 
            PSX\Data\Schema\Property\ComplexType::__set_state(array(
               'properties' => 
              array (
                'artist' => 
                PSX\Data\Schema\Property\StringType::__set_state(array(
                   'minLength' => NULL,
                   'maxLength' => NULL,
                   'pattern' => NULL,
                   'enumeration' => NULL,
                   'name' => 'artist',
                   'description' => NULL,
                   'required' => true,
                   'reference' => NULL,
                )),
                'title' => 
                PSX\Data\Schema\Property\StringType::__set_state(array(
                   'minLength' => NULL,
                   'maxLength' => NULL,
                   'pattern' => NULL,
                   'enumeration' => NULL,
                   'name' => 'title',
                   'description' => NULL,
                   'required' => true,
                   'reference' => NULL,
                )),
              ),
               'name' => NULL,
               'description' => 'A canonical song',
               'required' => NULL,
               'reference' => NULL,
            )),
          )),
           'responses' => 
          array (
            200 => 
            PSX\Data\Schema::__set_state(array(
               'property' => 
              PSX\Data\Schema\Property\ComplexType::__set_state(array(
                 'properties' => 
                array (
                  'title' => 
                  PSX\Data\Schema\Property\StringType::__set_state(array(
                     'minLength' => NULL,
                     'maxLength' => NULL,
                     'pattern' => NULL,
                     'enumeration' => NULL,
                     'name' => 'title',
                     'description' => NULL,
                     'required' => true,
                     'reference' => NULL,
                  )),
                  'artist' => 
                  PSX\Data\Schema\Property\StringType::__set_state(array(
                     'minLength' => NULL,
                     'maxLength' => NULL,
                     'pattern' => NULL,
                     'enumeration' => NULL,
                     'name' => 'artist',
                     'description' => NULL,
                     'required' => true,
                     'reference' => NULL,
                  )),
                ),
                 'name' => NULL,
                 'description' => 'A canonical song',
                 'required' => NULL,
                 'reference' => NULL,
              )),
            )),
          ),
        )),
      ),
    )),
  ),
   'description' => 'World Music API',
))
PHP;

		$documentation = unserialize($commandTester->getDisplay());

		$this->assertInstanceOf('PSX\Api\DocumentationInterface', $documentation);

		$resource = $documentation->getResource($documentation->getLatestVersion());

		$this->assertInstanceOf('PSX\Api\Resource', $resource);
		$this->assertEquals(['GET', 'POST'], $resource->getAllowedMethods());
	}

	public function testCommandAvailable()
	{
		$command = Environment::getService('console')->find('debug:raml');

		$this->assertInstanceOf('PSX\Console\Debug\RamlCommand', $command);
	}
}
