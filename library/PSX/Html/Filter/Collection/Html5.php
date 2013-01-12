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
 * Collection off all html5 elements from the current specification. See
 * http://www.w3.org/TR/html5/ for more informations. Must be only used with
 * trusted content because it allows all elements from the specification 
 * (script, forms, etc.). You can extend this collection and allow only the 
 * elements wich are needed in your case
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Html
 * @version    $Revision: 560 $
 */
class PSX_Html_Filter_Collection_Html5 extends PSX_Html_Filter_CollectionAbstract
{
	protected $metaContent = array('base', 'command', 'link', 'meta', 'noscript', 'script', 'style', 'title');
	protected $flowContent = array('#PCDATA', 'a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'bdi', 'bdo', 'blockquote', 'br', 'button', 'canvas', 'cite', 'code', 'command', 'datalist', 'del', 'details', 'dfn', 'div', 'dl', 'em', 'embed', 'fieldset', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup', 'hr', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'keygen', 'label', 'map', 'mark', 'math', 'menu', 'meter', 'nav', 'noscript', 'object', 'ol', 'output', 'p', 'pre', 'progress', 'q', 'ruby', 's', 'samp', 'script', 'section', 'select', 'small', 'span', 'strong', 'style', 'sub', 'sup', 'svg', 'table', 'textarea', 'time', 'u', 'ul', 'var', 'video', 'wbr', 'text');
	protected $sectioningContent = array('article', 'aside', 'nav', 'section');
	protected $headingContent = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hgroup');
	protected $phrasingContent = array('#PCDATA', 'a', 'abbr', 'area', 'audio', 'b', 'bdi', 'bdo', 'br', 'button', 'canvas', 'cite', 'code', 'command', 'datalist', 'del', 'dfn', 'em', 'embed', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'keygen', 'label', 'map', 'mark', 'math', 'meter', 'noscript', 'object', 'output', 'progress', 'q', 'ruby', 's', 'samp', 'script', 'select', 'small', 'span', 'strong', 'sub', 'sup', 'svg', 'textarea', 'time', 'u', 'var', 'video', 'wbr');
	protected $embeddedContent = array('audio', 'canvas', 'embed', 'iframe', 'img', 'math', 'object', 'svg', 'video');
	protected $interactiveContent = array('a', 'audio', 'button', 'details', 'embed', 'iframe', 'img', 'input', 'keygen', 'label', 'menu', 'object', 'select', 'textarea', 'video');

	// attributes
	protected $globalAttributes = array(
		'id' => PSX_Html_Filter::ANY_VALUE, 
		'title' => PSX_Html_Filter::ANY_VALUE,
		'lang' => PSX_Html_Filter::ANY_VALUE, 
		'translate' => array('yes', 'no'), 
		'dir' => array('ltr', 'rtl', 'auto'),
		'class' => PSX_Html_Filter::ANY_VALUE,
		'style' => PSX_Html_Filter::ANY_VALUE,
		'data-*' => PSX_Html_Filter::ANY_VALUE,
	);

	public function loadElements()
	{
		// the root element
		$this->loadRootElement();

		// document metadata
		$this->loadDocumentMetadataElements();

		// scripting
		$this->loadScriptingElements();

		// sections
		$this->loadSectionElements();

		// grouping content
		$this->loadGroupingContentElements();

		// text level semantics
		$this->loadTextLevelSemanticElements();

		// edits
		$this->loadEditElements();

		// embedded content
		$this->loadEmbeddedContentElements();

		// tabular data
		$this->loadTabularDataElements();

		// forms
		$this->loadFormElements();
	}

	protected function loadRootElement()
	{
		$this->add(new PSX_Html_Filter_Element('html', array_merge($this->globalAttributes, array(
			'manifest' => PSX_Html_Filter::ANY_VALUE,
		)), array('head', 'body')));
	}

	protected function loadDocumentMetadataElements()
	{
		$this->add(new PSX_Html_Filter_Element('head', $this->globalAttributes, $this->metaContent));
		$this->add(new PSX_Html_Filter_Element('title', $this->globalAttributes, array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('base', array_merge($this->globalAttributes, array(
			'href' => PSX_Html_Filter::ANY_VALUE,
			'target' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('link', array_merge($this->globalAttributes, array(
			'href' => PSX_Html_Filter::ANY_VALUE,
			'rel' => PSX_Html_Filter::ANY_VALUE,
			'media' => PSX_Html_Filter::ANY_VALUE,
			'hreflang' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
			'sizes' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('meta', array_merge($this->globalAttributes, array(
			'name' => PSX_Html_Filter::ANY_VALUE,
			'http-equiv' => PSX_Html_Filter::ANY_VALUE,
			'content' => PSX_Html_Filter::ANY_VALUE,
			'charset' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('style', array_merge($this->globalAttributes, array(
			'media' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
			'scoped' => PSX_Html_Filter::ANY_VALUE,
		)), array('#PCDATA')));
	}

	protected function loadScriptingElements()
	{
		$this->add(new PSX_Html_Filter_Element('script', array_merge($this->globalAttributes, array(
			'src' => PSX_Html_Filter::ANY_VALUE,
			'async' => PSX_Html_Filter::ANY_VALUE,
			'defer' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
			'charset' => PSX_Html_Filter::ANY_VALUE,
		)), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('noscript', $this->globalAttributes, PSX_Html_Filter::CONTENT_TRANSPARENT));
	}

	protected function loadSectionElements()
	{
		$this->add(new PSX_Html_Filter_Element('body', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('section', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('nav', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('article', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('aside', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('h1', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('h2', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('h3', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('h4', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('h5', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('h6', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('hgroup', $this->globalAttributes, $this->headingContent));
		$this->add(new PSX_Html_Filter_Element('header', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('footer', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('address', $this->globalAttributes, $this->flowContent));
	}

	protected function loadGroupingContentElements()
	{
		$this->add(new PSX_Html_Filter_Element('p', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('hr', $this->globalAttributes));
		$this->add(new PSX_Html_Filter_Element('pre', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('blockquote', array_merge($this->globalAttributes, array(
			'cite' => PSX_Html_Filter::ANY_VALUE,
		)), $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('ol', array_merge($this->globalAttributes, array(
			'reversed' => PSX_Html_Filter::ANY_VALUE,
			'start' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
		)), array('li')));
		$this->add(new PSX_Html_Filter_Element('ul', $this->globalAttributes, array('li')));
		$this->add(new PSX_Html_Filter_Element('li', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('dl', $this->globalAttributes, array('dt', 'dd')));
		$this->add(new PSX_Html_Filter_Element('dt', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('dd', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('figure', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('figcaption', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('div', $this->globalAttributes, $this->flowContent));
	}

	protected function loadTextLevelSemanticElements()
	{
		$this->add(new PSX_Html_Filter_Element('#PCDATA'));
		$this->add(new PSX_Html_Filter_Element('a', array_merge($this->globalAttributes, array(
			'href' => PSX_Html_Filter::ANY_VALUE,
			'target' => PSX_Html_Filter::ANY_VALUE,
			'rel' => PSX_Html_Filter::ANY_VALUE,
			'media' => PSX_Html_Filter::ANY_VALUE,
			'hreflang' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
		)), PSX_Html_Filter::CONTENT_TRANSPARENT));
		$this->add(new PSX_Html_Filter_Element('em', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('strong', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('small', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('s', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('cite', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('q', array_merge($this->globalAttributes, array(
			'cite' => PSX_Html_Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('dfn', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('abbr', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('time', array_merge($this->globalAttributes, array(
			'datetime' => PSX_Html_Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('code', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('var', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('samp', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('kbd', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('sub', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('sup', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('i', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('b', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('u', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('mark', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('bdi', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('bdo', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('span', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('br'));
		$this->add(new PSX_Html_Filter_Element('wbr'));
	}

	protected function loadEditElements()
	{
		$this->add(new PSX_Html_Filter_Element('ins', array_merge($this->globalAttributes, array(
			'cite' => PSX_Html_Filter::ANY_VALUE,
			'datetime' => PSX_Html_Filter::ANY_VALUE,
		)), PSX_Html_Filter::CONTENT_TRANSPARENT));
		$this->add(new PSX_Html_Filter_Element('del', array_merge($this->globalAttributes, array(
			'cite' => PSX_Html_Filter::ANY_VALUE,
			'datetime' => PSX_Html_Filter::ANY_VALUE,
		)), PSX_Html_Filter::CONTENT_TRANSPARENT));
	}

	protected function loadEmbeddedContentElements()
	{
		$this->add(new PSX_Html_Filter_Element('img', array_merge($this->globalAttributes, array(
			'alt' => PSX_Html_Filter::ANY_VALUE, 
			'src' => PSX_Html_Filter::ANY_VALUE, 
			'crossorigin' => PSX_Html_Filter::ANY_VALUE, 
			'usemap' => PSX_Html_Filter::ANY_VALUE, 
			'ismap' => PSX_Html_Filter::ANY_VALUE, 
			'width' => PSX_Html_Filter::ANY_VALUE, 
			'height' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('iframe', array_merge($this->globalAttributes, array(
			'src' => PSX_Html_Filter::ANY_VALUE,
			'srcdoc' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
			'sandbox' => PSX_Html_Filter::ANY_VALUE,
			'seamless' => PSX_Html_Filter::ANY_VALUE,
			'width' => PSX_Html_Filter::ANY_VALUE,
			'height' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('embed', array_merge($this->globalAttributes, array(
			'src' => PSX_Html_Filter::ANY_VALUE, 
			'type' => PSX_Html_Filter::ANY_VALUE,
			'width' => PSX_Html_Filter::ANY_VALUE, 
			'height' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('object', array_merge($this->globalAttributes, array(
			'data' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
			'typemustmatch' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
			'usemap' => PSX_Html_Filter::ANY_VALUE,
			'form' => PSX_Html_Filter::ANY_VALUE,
			'width' => PSX_Html_Filter::ANY_VALUE,
			'height' => PSX_Html_Filter::ANY_VALUE,
		)), array('param')));
		$this->add(new PSX_Html_Filter_Element('param', array_merge($this->globalAttributes, array(
			'name' => PSX_Html_Filter::ANY_VALUE,
			'value' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('video', array_merge($this->globalAttributes, array(
			'src' => PSX_Html_Filter::ANY_VALUE,
			'crossorigin' => PSX_Html_Filter::ANY_VALUE,
			'poster' => PSX_Html_Filter::ANY_VALUE,
			'preload' => PSX_Html_Filter::ANY_VALUE,
			'autoplay' => PSX_Html_Filter::ANY_VALUE,
			'mediagroup' => PSX_Html_Filter::ANY_VALUE,
			'loop' => PSX_Html_Filter::ANY_VALUE,
			'muted' => PSX_Html_Filter::ANY_VALUE,
			'controls' => PSX_Html_Filter::ANY_VALUE,
			'width' => PSX_Html_Filter::ANY_VALUE,
			'height' => PSX_Html_Filter::ANY_VALUE,
		)), array('track', 'source')));
		$this->add(new PSX_Html_Filter_Element('audio', array_merge($this->globalAttributes, array(
			'src' => PSX_Html_Filter::ANY_VALUE,
			'crossorigin' => PSX_Html_Filter::ANY_VALUE,
			'preload' => PSX_Html_Filter::ANY_VALUE,
			'autoplay' => PSX_Html_Filter::ANY_VALUE,
			'mediagroup' => PSX_Html_Filter::ANY_VALUE,
			'loop' => PSX_Html_Filter::ANY_VALUE,
			'muted' => PSX_Html_Filter::ANY_VALUE,
			'controls' => PSX_Html_Filter::ANY_VALUE,
		)), array('track', 'source')));
		$this->add(new PSX_Html_Filter_Element('source', array_merge($this->globalAttributes, array(
			'src' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
			'media' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('track', array_merge($this->globalAttributes, array(
			'kind' => PSX_Html_Filter::ANY_VALUE,
			'src' => PSX_Html_Filter::ANY_VALUE,
			'srclang' => PSX_Html_Filter::ANY_VALUE,
			'label' => PSX_Html_Filter::ANY_VALUE,
			'default' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('canvas', array_merge($this->globalAttributes, array(
			'width' => PSX_Html_Filter::ANY_VALUE,
			'height' => PSX_Html_Filter::ANY_VALUE,
		)), PSX_Html_Filter::CONTENT_TRANSPARENT));
		$this->add(new PSX_Html_Filter_Element('map', array_merge($this->globalAttributes, array(
			'name' => PSX_Html_Filter::ANY_VALUE,
		)), PSX_Html_Filter::CONTENT_TRANSPARENT));
		$this->add(new PSX_Html_Filter_Element('area', array_merge($this->globalAttributes, array(
			'alt' => PSX_Html_Filter::ANY_VALUE,
			'coords' => PSX_Html_Filter::ANY_VALUE,
			'shape' => PSX_Html_Filter::ANY_VALUE,
			'href' => PSX_Html_Filter::ANY_VALUE,
			'target' => PSX_Html_Filter::ANY_VALUE,
			'rel' => PSX_Html_Filter::ANY_VALUE,
			'media' => PSX_Html_Filter::ANY_VALUE,
			'hreflang' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
		))));
	}

	protected function loadTabularDataElements()
	{
		$this->add(new PSX_Html_Filter_Element('table', array_merge($this->globalAttributes, array(
			'border' => PSX_Html_Filter::ANY_VALUE,
		)), array('caption', 'colgroup', 'thead', 'tfoot', 'tbody', 'tr')));
		$this->add(new PSX_Html_Filter_Element('caption', $this->globalAttributes, $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('colgroup', array_merge($this->globalAttributes, array(
			'span' => new PSX_Filter_Digit(),
		)), array('col')));
		$this->add(new PSX_Html_Filter_Element('col', array_merge($this->globalAttributes, array(
			'span' => new PSX_Filter_Digit(),
		))));
		$this->add(new PSX_Html_Filter_Element('tbody', $this->globalAttributes, array('tr')));
		$this->add(new PSX_Html_Filter_Element('thead', $this->globalAttributes, array('tr')));
		$this->add(new PSX_Html_Filter_Element('tfoot', $this->globalAttributes, array('tr')));
		$this->add(new PSX_Html_Filter_Element('tr', $this->globalAttributes, array('td', 'th')));
		$this->add(new PSX_Html_Filter_Element('td', array_merge($this->globalAttributes, array(
			'colspan' => PSX_Html_Filter::ANY_VALUE,
			'rowspan' => PSX_Html_Filter::ANY_VALUE,
			'headers' => PSX_Html_Filter::ANY_VALUE,
		)), $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('th', array_merge($this->globalAttributes, array(
			'colspan' => PSX_Html_Filter::ANY_VALUE,
			'rowspan' => PSX_Html_Filter::ANY_VALUE,
			'headers' => PSX_Html_Filter::ANY_VALUE,
		)), $this->flowContent));
	}

	protected function loadFormElements()
	{
		$this->add(new PSX_Html_Filter_Element('form', array_merge($this->globalAttributes, array(
			'accept-charset' => PSX_Html_Filter::ANY_VALUE,
			'action' => PSX_Html_Filter::ANY_VALUE,
			'autocomplete' => PSX_Html_Filter::ANY_VALUE,
			'enctype' => PSX_Html_Filter::ANY_VALUE,
			'method' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
			'novalidate' => PSX_Html_Filter::ANY_VALUE,
			'target' => PSX_Html_Filter::ANY_VALUE,
		)), $this->flowContent));
		$this->add(new PSX_Html_Filter_Element('fieldset', array_merge($this->globalAttributes, array(
			'disabled' => PSX_Html_Filter::ANY_VALUE,
			'form' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
		)), array_merge(array('legend'), $this->flowContent)));
		$this->add(new PSX_Html_Filter_Element('legend', $this->globalAttributes, $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('label', array_merge($this->globalAttributes, array(
			'form' => PSX_Html_Filter::ANY_VALUE,
			'for' => PSX_Html_Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('input', array_merge($this->globalAttributes, array(
			'accept' => PSX_Html_Filter::ANY_VALUE,
			'alt' => PSX_Html_Filter::ANY_VALUE,
			'autocomplete' => PSX_Html_Filter::ANY_VALUE,
			'autofocus' => PSX_Html_Filter::ANY_VALUE,
			'checked' => PSX_Html_Filter::ANY_VALUE,
			'dirname' => PSX_Html_Filter::ANY_VALUE,
			'disabled' => PSX_Html_Filter::ANY_VALUE,
			'form' => PSX_Html_Filter::ANY_VALUE,
			'formaction' => PSX_Html_Filter::ANY_VALUE,
			'formenctype' => PSX_Html_Filter::ANY_VALUE,
			'formmethod' => PSX_Html_Filter::ANY_VALUE,
			'formnovalidate' => PSX_Html_Filter::ANY_VALUE,
			'formtarget' => PSX_Html_Filter::ANY_VALUE,
			'height' => PSX_Html_Filter::ANY_VALUE,
			'list' => PSX_Html_Filter::ANY_VALUE,
			'max' => PSX_Html_Filter::ANY_VALUE,
			'maxlength' => PSX_Html_Filter::ANY_VALUE,
			'min' => PSX_Html_Filter::ANY_VALUE,
			'multiple' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
			'pattern' => PSX_Html_Filter::ANY_VALUE,
			'placeholder' => PSX_Html_Filter::ANY_VALUE,
			'readonly' => PSX_Html_Filter::ANY_VALUE,
			'required' => PSX_Html_Filter::ANY_VALUE,
			'size' => PSX_Html_Filter::ANY_VALUE,
			'src' => PSX_Html_Filter::ANY_VALUE,
			'step' => PSX_Html_Filter::ANY_VALUE,
			'type' => new PSX_Filter_InArray(array('hidden', 'text', 'search', 'tel', 'url', 'email', 'password', 'datetime', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'range', 'color', 'checkbox', 'radio', 'file', 'submit', 'image', 'reset', 'button')),
			'value' => PSX_Html_Filter::ANY_VALUE,
			'width' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('button', array_merge($this->globalAttributes, array(
			'autofocus' => PSX_Html_Filter::ANY_VALUE,
			'disabled' => PSX_Html_Filter::ANY_VALUE,
			'form' => PSX_Html_Filter::ANY_VALUE,
			'formaction' => PSX_Html_Filter::ANY_VALUE,
			'formenctype' => PSX_Html_Filter::ANY_VALUE,
			'formmethod' => PSX_Html_Filter::ANY_VALUE,
			'formnovalidate' => PSX_Html_Filter::ANY_VALUE,
			'formtarget' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
			'type' => PSX_Html_Filter::ANY_VALUE,
			'value' => PSX_Html_Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('select', array_merge($this->globalAttributes, array(
			'autofocus' => PSX_Html_Filter::ANY_VALUE,
			'disabled' => PSX_Html_Filter::ANY_VALUE,
			'form' => PSX_Html_Filter::ANY_VALUE,
			'multiple' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
			'required' => PSX_Html_Filter::ANY_VALUE,
			'size' => PSX_Html_Filter::ANY_VALUE,
		)), array('option', 'optgroup')));
		$this->add(new PSX_Html_Filter_Element('datalist', $this->globalAttributes, array_merge(array('option'), $this->phrasingContent)));
		$this->add(new PSX_Html_Filter_Element('optgroup', array_merge($this->globalAttributes, array(
			'disabled' => PSX_Html_Filter::ANY_VALUE,
			'label' => PSX_Html_Filter::ANY_VALUE,
		)), array('option')));
		$this->add(new PSX_Html_Filter_Element('option', array_merge($this->globalAttributes, array(
			'disabled' => PSX_Html_Filter::ANY_VALUE,
			'label' => PSX_Html_Filter::ANY_VALUE,
			'selected' => PSX_Html_Filter::ANY_VALUE,
			'value' => PSX_Html_Filter::ANY_VALUE,
		)), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('textarea', array_merge($this->globalAttributes, array(
			'autofocus' => PSX_Html_Filter::ANY_VALUE,
			'cols' => PSX_Html_Filter::ANY_VALUE,
			'dirname' => PSX_Html_Filter::ANY_VALUE,
			'disabled' => PSX_Html_Filter::ANY_VALUE,
			'form' => PSX_Html_Filter::ANY_VALUE,
			'maxlength' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
			'placeholder' => PSX_Html_Filter::ANY_VALUE,
			'readonly' => PSX_Html_Filter::ANY_VALUE,
			'required' => PSX_Html_Filter::ANY_VALUE,
			'rows' => PSX_Html_Filter::ANY_VALUE,
			'wrap' => PSX_Html_Filter::ANY_VALUE,
		)), array('#PCDATA')));
		$this->add(new PSX_Html_Filter_Element('keygen', array_merge($this->globalAttributes, array(
			'autofocus' => PSX_Html_Filter::ANY_VALUE,
			'challenge' => PSX_Html_Filter::ANY_VALUE,
			'disabled' => PSX_Html_Filter::ANY_VALUE,
			'form' => PSX_Html_Filter::ANY_VALUE,
			'keytype' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('output', array_merge($this->globalAttributes, array(
			'for' => PSX_Html_Filter::ANY_VALUE,
			'form' => PSX_Html_Filter::ANY_VALUE,
			'name' => PSX_Html_Filter::ANY_VALUE,
		))));
		$this->add(new PSX_Html_Filter_Element('progress', array_merge($this->globalAttributes, array(
			'value' => PSX_Html_Filter::ANY_VALUE,
			'max' => PSX_Html_Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new PSX_Html_Filter_Element('meter', array_merge($this->globalAttributes, array(
			'value' => PSX_Html_Filter::ANY_VALUE,
			'min' => PSX_Html_Filter::ANY_VALUE,
			'max' => PSX_Html_Filter::ANY_VALUE,
			'low' => PSX_Html_Filter::ANY_VALUE,
			'high' => PSX_Html_Filter::ANY_VALUE,
			'optimum' => PSX_Html_Filter::ANY_VALUE,
		)), $this->phrasingContent));
	}
}
