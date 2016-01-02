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

namespace PSX;

use PSX\Atom\Category;
use PSX\Atom\Generator;
use PSX\Atom\Link;
use PSX\Atom\Person;
use PSX\Atom\Text;
use PSX\Data\CollectionAbstract;
use PSX\Data\RecordInfo;

/**
 * This record represents an atom feed
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc4287.txt
 */
class Atom extends CollectionAbstract
{
    public static $xmlns = 'http://www.w3.org/2005/Atom';

    /**
     * @var \PSX\Atom\Person
     */
    protected $author;

    /**
     * @var \PSX\Atom\Category
     */
    protected $category;

    /**
     * @var \PSX\Atom\Person
     */
    protected $contributor;

    /**
     * @var \PSX\Atom\Generator
     */
    protected $generator;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $logo;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var \PSX\Atom\Link[]
     */
    protected $link;

    /**
     * @var string
     */
    protected $rights;

    /**
     * @var \PSX\Atom\Text
     */
    protected $subTitle;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \DateTime
     */
    protected $updated;

    public function getRecordInfo()
    {
        return new RecordInfo('feed', array(
            'author'      => $this->author,
            'category'    => $this->category,
            'contributor' => $this->contributor,
            'generator'   => $this->generator,
            'icon'        => $this->icon,
            'logo'        => $this->logo,
            'id'          => $this->id,
            'link'        => $this->link,
            'rights'      => $this->rights,
            'subTitle'    => $this->subTitle,
            'title'       => $this->title,
            'updated'     => $this->updated,
            'entry'       => $this->collection,
        ));
    }

    /**
     * @param \PSX\Atom\Person $author
     */
    public function addAuthor(Person $author)
    {
        if ($this->author === null) {
            $this->author = array();
        }

        $this->author[] = $author;
    }

    /**
     * @param \PSX\Atom\Person[] $author
     */
    public function setAuthor(array $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param \PSX\Atom\Category $category
     */
    public function addCategory(Category $category)
    {
        if ($this->category === null) {
            $this->category = array();
        }

        $this->category[] = $category;
    }

    /**
     * @param \PSX\Atom\Category[] $category
     */
    public function setCategory(array $category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param \PSX\Atom\Person $contributor
     */
    public function addContributor(Person $contributor)
    {
        if ($this->contributor === null) {
            $this->contributor = array();
        }

        $this->contributor[] = $contributor;
    }

    /**
     * @param \PSX\Atom\Person[] $contributor
     */
    public function setContributor(array $contributor)
    {
        $this->contributor = $contributor;
    }

    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * @param \PSX\Atom\Generator $generator
     */
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

    /**
     * @param \PSX\DateTime $updated
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
    }
    
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \PSX\Atom\Link $link
     */
    public function addLink(Link $link)
    {
        if ($this->link === null) {
            $this->link = array();
        }

        $this->link[] = $link;
    }

    /**
     * @param \PSX\Atom\Link[] $link
     */
    public function setLink(array $link)
    {
        $this->link = $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param \PSX\Atom\Text $subTitle
     */
    public function setSubTitle(Text $subTitle)
    {
        $this->subTitle = $subTitle;
    }
    
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @param \PSX\Atom\Entry[]
     */
    public function setEntry(array $entry)
    {
        $this->collection = $entry;
    }

    public function getEntry()
    {
        return $this->collection;
    }
}
