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

namespace PSX\Model\Atom;

/**
 * This record represents an atom feed
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc4287.txt
 */
class Atom
{
    public static $xmlns = 'http://www.w3.org/2005/Atom';

    /**
     * @Type("array<\PSX\Model\Atom\Person>")
     */
    protected $author;

    /**
     * @Type("array<\PSX\Model\Atom\Category>")
     */
    protected $category;

    /**
     * @Type("array<\PSX\Model\Atom\Person>")
     */
    protected $contributor;

    /**
     * @Type("\PSX\Model\Atom\Generator")
     */
    protected $generator;

    /**
     * @Type("string")
     */
    protected $icon;

    /**
     * @Type("string")
     */
    protected $logo;

    /**
     * @Type("string")
     */
    protected $id;

    /**
     * @Type("array<\PSX\Model\Atom\Link>")
     */
    protected $link;

    /**
     * @Type("string")
     */
    protected $rights;

    /**
     * @Type("\PSX\Model\Atom\Text")
     */
    protected $subTitle;

    /**
     * @Type("string")
     */
    protected $title;

    /**
     * @Type("\DateTime")
     */
    protected $updated;

    /**
     * @Type("array<\PSX\Model\Atom\Entry>")
     */
    protected $entry;

    public function addAuthor(Person $author)
    {
        if ($this->author === null) {
            $this->author = array();
        }

        $this->author[] = $author;
    }

    public function setAuthor(array $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function addCategory(Category $category)
    {
        if ($this->category === null) {
            $this->category = array();
        }

        $this->category[] = $category;
    }

    public function setCategory(array $category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function addContributor(Person $contributor)
    {
        if ($this->contributor === null) {
            $this->contributor = array();
        }

        $this->contributor[] = $contributor;
    }

    public function setContributor(array $contributor)
    {
        $this->contributor = $contributor;
    }

    public function getContributor()
    {
        return $this->contributor;
    }

    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;
    }
    
    public function getGenerator()
    {
        return $this->generator;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
    
    public function getIcon()
    {
        return $this->icon;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }
    
    public function getLogo()
    {
        return $this->logo;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setRights($rights)
    {
        $this->rights = $rights;
    }
    
    public function getRights()
    {
        return $this->rights;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
    }
    
    public function getUpdated()
    {
        return $this->updated;
    }

    public function addLink(Link $link)
    {
        if ($this->link === null) {
            $this->link = array();
        }

        $this->link[] = $link;
    }

    public function setLink(array $link)
    {
        $this->link = $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setSubTitle(Text $subTitle)
    {
        $this->subTitle = $subTitle;
    }
    
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    public function setEntry(array $entry)
    {
        $this->entry = $entry;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function addEntry(Entry $entry)
    {
        if ($this->entry === null) {
            $this->entry = array();
        }

        $this->entry[] = $entry;
    }
}
