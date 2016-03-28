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

namespace PSX\Framework\Tests\Loader;

use PSX\Framework\Loader\PathMatcher;

/**
 * PathMatcherTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PathMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $this->assertMatchTrue('', '');
        $this->assertMatchTrue('', '/');
        $this->assertMatchTrue('foo', 'foo');
        $this->assertMatchTrue('foo', '/foo');
        $this->assertMatchTrue('/foo', 'foo');
        $this->assertMatchTrue('/foo', '/foo');
        $this->assertMatchTrue('/foo/', '/foo/');
        $this->assertMatchTrue('/foo/blub', '/foo/:bar');
        $this->assertMatchTrue('/foo/test', '/foo/:bar');
        $this->assertMatchFalse('/foo', '/foo/:bar');
        $this->assertMatchFalse('/foo/', '/foo/:bar');
        $this->assertMatchTrue('/foo/12', '/foo/$foo<[0-9]+>');
        $this->assertMatchFalse('/foo/test', '/foo/$foo<[0-9]+>');
        $this->assertMatchTrue('/file', '/file/*files');
        $this->assertMatchTrue('/file/', '/file/*files');
        $this->assertMatchTrue('/file/foo', '/file/*files');
        $this->assertMatchTrue('/file/foo/bar', '/file/*files');
        $this->assertMatchTrue('/file/foo/bar/foo.html', '/file/*files');
    }

    protected function assertMatchTrue($srcPath, $destPath)
    {
        $pathMatcher = new PathMatcher($srcPath);
        $result = $pathMatcher->match($destPath);

        $this->assertTrue($result);
    }

    protected function assertMatchFalse($srcPath, $destPath)
    {
        $pathMatcher = new PathMatcher($srcPath);
        $result = $pathMatcher->match($destPath);

        $this->assertFalse($result);
    }
}
