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

namespace PSX\Schema\Tests;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Data\Record\Transformer;
use PSX\Schema\Parser;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Tests\SchemaTraverser\RecursionModel;
use PSX\Schema\Visitor\OutgoingVisitor;

/**
 * SchemaTraverserTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SchemaTraverserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    protected function setUp()
    {
        $this->reader = new SimpleAnnotationReader();
        $this->reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');
    }

    public function testRecursion()
    {
        $parser = new Parser\Popo($this->reader);
        $schema = $parser->parse(RecursionModel::class);

        $expect = [
            'title' => 'level1',
            'model' => [
                'title' => 'level2',
                'model' => [
                    'title' => 'level3',
                    'model' => [
                        'title' => 'level4',
                        'model' => [
                            'title' => 'level5',
                            'model' => [
                                'title' => 'level6',
                                'model' => [
                                    'title' => 'level7',
                                    'model' => [
                                        'title' => 'level8'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $traverser = new SchemaTraverser();
        $data = $traverser->traverse($expect, $schema, new OutgoingVisitor());

        $this->assertEquals($expect, Transformer::toArray($data));
    }

    /**
     * @expectedException \PSX\Schema\ValidationException
     */
    public function testMaxRecursion()
    {
        $parser = new Parser\Popo($this->reader);
        $schema = $parser->parse(RecursionModel::class);

        $expect = [
            'title' => 'level1',
            'model' => [
                'title' => 'level2',
                'model' => [
                    'title' => 'level3',
                    'model' => [
                        'title' => 'level4',
                        'model' => [
                            'title' => 'level5',
                            'model' => [
                                'title' => 'level6',
                                'model' => [
                                    'title' => 'level7',
                                    'model' => [
                                        'title' => 'level8',
                                        'model' => [
                                            'title' => 'level9',
                                            'model' => [
                                                'title' => 'level10',
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $traverser = new SchemaTraverser();
        $traverser->traverse($expect, $schema, new OutgoingVisitor());
    }
}