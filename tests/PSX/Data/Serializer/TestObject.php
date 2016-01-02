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

namespace PSX\Data\Serializer;

use JMS\Serializer\Annotation as JMS;

/**
 * TestObject
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 *
 * @JMS\ExclusionPolicy("none")
 */
class TestObject
{
    private $title;
    private $author;
    private $contributors;
    private $tags;

    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setAuthor(TestAuthor $author)
    {
        $this->author = $author;
    }
    
    public function getAuthor()
    {
        return $this->author;
    }

    public function setContributors(array $contributors)
    {
        $this->contributors = $contributors;
    }
    
    public function getContributors()
    {
        return $this->contributors;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }
    
    public function getTags()
    {
        return $this->tags;
    }
}
