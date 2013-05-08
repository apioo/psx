<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Html\Filter\Collection;

use PSX\Html\Filter\CollectionAbstract;
use PSX\Html\Filter\Element;

/**
 * Collection of the most important html text level semantics tag. Can be used 
 * with untrusted content.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Html5Text extends CollectionAbstract
{
	protected $textSemantics = array('#PCDATA', 'a', 'em', 'strong', 'small', 's', 'cite', 'q', 'dfn', 'abbr', 'time', 'code', 'var', 'samp', 'lbd', 'sub', 'sup', 'i', 'b', 'u', 'mark', 'bdi', 'bdo', 'br', 'wbr', 'ins', 'del');

	public function loadElements()
	{
		// grouping content
		$this->add(new Element('p', array(), $this->textSemantics));
		$this->add(new Element('hr'));
		$this->add(new Element('pre', array(), $this->textSemantics));
		$this->add(new Element('blockquote', array(), array_merge(array('p'), $this->textSemantics)));
		$this->add(new Element('ol', array(), array('li')));
		$this->add(new Element('ul', array(), array('li')));
		$this->add(new Element('li', array(), $this->textSemantics));
		$this->add(new Element('dl', array(), array('dt', 'dd')));
		$this->add(new Element('dt', array(), $this->textSemantics));
		$this->add(new Element('dd', array(), $this->textSemantics));

		// text level semantics
		$this->add(new Element('#PCDATA'));
		$this->add(new Element('em', array(), array('#PCDATA')));
		$this->add(new Element('strong', array(), array('#PCDATA')));
		$this->add(new Element('small', array(), array('#PCDATA')));
		$this->add(new Element('s', array(), array('#PCDATA')));
		$this->add(new Element('cite', array(), array('#PCDATA')));
		$this->add(new Element('q', array(), array('#PCDATA')));
		$this->add(new Element('dfn', array(), array('#PCDATA')));
		$this->add(new Element('abbr', array(), array('#PCDATA')));
		$this->add(new Element('time', array(), array('#PCDATA')));
		$this->add(new Element('code', array(), array('#PCDATA')));
		$this->add(new Element('var', array(), array('#PCDATA')));
		$this->add(new Element('samp', array(), array('#PCDATA')));
		$this->add(new Element('kbd', array(), array('#PCDATA')));
		$this->add(new Element('sub', array(), array('#PCDATA')));
		$this->add(new Element('sup', array(), array('#PCDATA')));
		$this->add(new Element('i', array(), array('#PCDATA')));
		$this->add(new Element('b', array(), array('#PCDATA')));
		$this->add(new Element('u', array(), array('#PCDATA')));
		$this->add(new Element('mark', array(), array('#PCDATA')));
		$this->add(new Element('bdi', array(), array('#PCDATA')));
		$this->add(new Element('bdo', array(), array('#PCDATA')));
		$this->add(new Element('br', array(), array('#PCDATA')));
		$this->add(new Element('wbr', array(), array('#PCDATA')));
		$this->add(new Element('ins', array(), array('#PCDATA')));
		$this->add(new Element('del', array(), array('#PCDATA')));
	}
}

