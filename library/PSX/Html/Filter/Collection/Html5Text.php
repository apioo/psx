<?php
/*
 *  $Id: Html5Basic.php 560 2012-07-29 02:42:22Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * Collection of the most important html text level semantics tag. Can be used 
 * with untrusted content.
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Html
 * @version    $Revision: 560 $
 */
class PSX_Html_Filter_Collection_Html5Text extends PSX_Html_Filter_CollectionAbstract
{
	private $textSemantics = array('#PCDATA', 'a', 'em', 'strong', 'small', 's', 'cite', 'q', 'dfn', 'abbr', 'time', 'code', 'var', 'samp', 'lbd', 'sub', 'sup', 'i', 'b', 'u', 'mark', 'bdi', 'bdo', 'br', 'wbr', 'ins', 'del');

	public function loadElements()
	{
		// grouping content
		$this->add(new PSX_Html_Filter_Element('p', array(), $this->textSemantics));
		$this->add(new PSX_Html_Filter_Element('hr'));
		$this->add(new PSX_Html_Filter_Element('pre', array(), $this->textSemantics));
		$this->add(new PSX_Html_Filter_Element('blockquote', array(), array_merge(array('p'), $this->textSemantics)));
		$this->add(new PSX_Html_Filter_Element('ol', array(), array('li')));
		$this->add(new PSX_Html_Filter_Element('ul', array(), array('li')));
		$this->add(new PSX_Html_Filter_Element('li', array(), $this->textSemantics));
		$this->add(new PSX_Html_Filter_Element('dl', array(), array('dt', 'dd')));
		$this->add(new PSX_Html_Filter_Element('dt', array(), $this->textSemantics));
		$this->add(new PSX_Html_Filter_Element('dd', array(), $this->textSemantics));

		// text level semantics
		$this->add(new PSX_Html_Filter_Element('#PCDATA'));
		$this->add(new PSX_Html_Filter_Element('em', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('strong', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('small', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('s', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('cite', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('q', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('dfn', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('abbr', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('time', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('code', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('var', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('samp', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('kbd', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('sub', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('sup', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('i', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('b', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('u', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('mark', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('bdi', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('bdo', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('br', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('wbr', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('ins', array(), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('del', array(), array('#PCDATA')));
	}
}

