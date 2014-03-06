<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Html\Filter;
use PSX\Html\Filter\CollectionAbstract;
use PSX\Html\Filter\Element;
use PSX\Filter\Digit;
use PSX\Filter\InArray;

/**
 * Collection off all html5 elements from the current specification. See
 * http://www.w3.org/TR/html5/ for more informations. Must be only used with
 * trusted content because it allows all elements from the specification 
 * (script, forms, etc.). You can extend this collection and allow only the 
 * elements wich are needed in your case
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Html5 extends CollectionAbstract
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
		'id' => Filter::ANY_VALUE, 
		'title' => Filter::ANY_VALUE,
		'lang' => Filter::ANY_VALUE, 
		'translate' => array('yes', 'no'), 
		'dir' => array('ltr', 'rtl', 'auto'),
		'class' => Filter::ANY_VALUE,
		'style' => Filter::ANY_VALUE,
		'data-*' => Filter::ANY_VALUE,
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
		$this->add(new Element('html', array_merge($this->globalAttributes, array(
			'manifest' => Filter::ANY_VALUE,
		)), array('head', 'body')));
	}

	protected function loadDocumentMetadataElements()
	{
		$this->add(new Element('head', $this->globalAttributes, $this->metaContent));
		$this->add(new Element('title', $this->globalAttributes, array('#PCDATA')));
		$this->add(new Element('base', array_merge($this->globalAttributes, array(
			'href' => Filter::ANY_VALUE,
			'target' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('link', array_merge($this->globalAttributes, array(
			'href' => Filter::ANY_VALUE,
			'rel' => Filter::ANY_VALUE,
			'media' => Filter::ANY_VALUE,
			'hreflang' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
			'sizes' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('meta', array_merge($this->globalAttributes, array(
			'name' => Filter::ANY_VALUE,
			'http-equiv' => Filter::ANY_VALUE,
			'content' => Filter::ANY_VALUE,
			'charset' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('style', array_merge($this->globalAttributes, array(
			'media' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
			'scoped' => Filter::ANY_VALUE,
		)), array('#PCDATA')));
	}

	protected function loadScriptingElements()
	{
		$this->add(new Element('script', array_merge($this->globalAttributes, array(
			'src' => Filter::ANY_VALUE,
			'async' => Filter::ANY_VALUE,
			'defer' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
			'charset' => Filter::ANY_VALUE,
		)), array('#PCDATA')));
		$this->add(new Element('noscript', $this->globalAttributes, Filter::CONTENT_TRANSPARENT));
	}

	protected function loadSectionElements()
	{
		$this->add(new Element('body', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('section', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('nav', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('article', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('aside', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('h1', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('h2', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('h3', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('h4', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('h5', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('h6', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('hgroup', $this->globalAttributes, $this->headingContent));
		$this->add(new Element('header', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('footer', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('address', $this->globalAttributes, $this->flowContent));
	}

	protected function loadGroupingContentElements()
	{
		$this->add(new Element('p', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('hr', $this->globalAttributes));
		$this->add(new Element('pre', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('blockquote', array_merge($this->globalAttributes, array(
			'cite' => Filter::ANY_VALUE,
		)), $this->flowContent));
		$this->add(new Element('ol', array_merge($this->globalAttributes, array(
			'reversed' => Filter::ANY_VALUE,
			'start' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
		)), array('li')));
		$this->add(new Element('ul', $this->globalAttributes, array('li')));
		$this->add(new Element('li', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('dl', $this->globalAttributes, array('dt', 'dd')));
		$this->add(new Element('dt', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('dd', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('figure', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('figcaption', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('div', $this->globalAttributes, $this->flowContent));
	}

	protected function loadTextLevelSemanticElements()
	{
		$this->add(new Element('#PCDATA'));
		$this->add(new Element('a', array_merge($this->globalAttributes, array(
			'href' => Filter::ANY_VALUE,
			'target' => Filter::ANY_VALUE,
			'rel' => Filter::ANY_VALUE,
			'media' => Filter::ANY_VALUE,
			'hreflang' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
		)), Filter::CONTENT_TRANSPARENT));
		$this->add(new Element('em', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('strong', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('small', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('s', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('cite', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('q', array_merge($this->globalAttributes, array(
			'cite' => Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new Element('dfn', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('abbr', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('time', array_merge($this->globalAttributes, array(
			'datetime' => Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new Element('code', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('var', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('samp', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('kbd', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('sub', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('sup', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('i', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('b', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('u', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('mark', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('bdi', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('bdo', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('span', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('br'));
		$this->add(new Element('wbr'));
	}

	protected function loadEditElements()
	{
		$this->add(new Element('ins', array_merge($this->globalAttributes, array(
			'cite' => Filter::ANY_VALUE,
			'datetime' => Filter::ANY_VALUE,
		)), Filter::CONTENT_TRANSPARENT));
		$this->add(new Element('del', array_merge($this->globalAttributes, array(
			'cite' => Filter::ANY_VALUE,
			'datetime' => Filter::ANY_VALUE,
		)), Filter::CONTENT_TRANSPARENT));
	}

	protected function loadEmbeddedContentElements()
	{
		$this->add(new Element('img', array_merge($this->globalAttributes, array(
			'alt' => Filter::ANY_VALUE, 
			'src' => Filter::ANY_VALUE, 
			'crossorigin' => Filter::ANY_VALUE, 
			'usemap' => Filter::ANY_VALUE, 
			'ismap' => Filter::ANY_VALUE, 
			'width' => Filter::ANY_VALUE, 
			'height' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('iframe', array_merge($this->globalAttributes, array(
			'src' => Filter::ANY_VALUE,
			'srcdoc' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
			'sandbox' => Filter::ANY_VALUE,
			'seamless' => Filter::ANY_VALUE,
			'width' => Filter::ANY_VALUE,
			'height' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('embed', array_merge($this->globalAttributes, array(
			'src' => Filter::ANY_VALUE, 
			'type' => Filter::ANY_VALUE,
			'width' => Filter::ANY_VALUE, 
			'height' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('object', array_merge($this->globalAttributes, array(
			'data' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
			'typemustmatch' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
			'usemap' => Filter::ANY_VALUE,
			'form' => Filter::ANY_VALUE,
			'width' => Filter::ANY_VALUE,
			'height' => Filter::ANY_VALUE,
		)), array('param')));
		$this->add(new Element('param', array_merge($this->globalAttributes, array(
			'name' => Filter::ANY_VALUE,
			'value' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('video', array_merge($this->globalAttributes, array(
			'src' => Filter::ANY_VALUE,
			'crossorigin' => Filter::ANY_VALUE,
			'poster' => Filter::ANY_VALUE,
			'preload' => Filter::ANY_VALUE,
			'autoplay' => Filter::ANY_VALUE,
			'mediagroup' => Filter::ANY_VALUE,
			'loop' => Filter::ANY_VALUE,
			'muted' => Filter::ANY_VALUE,
			'controls' => Filter::ANY_VALUE,
			'width' => Filter::ANY_VALUE,
			'height' => Filter::ANY_VALUE,
		)), array('track', 'source')));
		$this->add(new Element('audio', array_merge($this->globalAttributes, array(
			'src' => Filter::ANY_VALUE,
			'crossorigin' => Filter::ANY_VALUE,
			'preload' => Filter::ANY_VALUE,
			'autoplay' => Filter::ANY_VALUE,
			'mediagroup' => Filter::ANY_VALUE,
			'loop' => Filter::ANY_VALUE,
			'muted' => Filter::ANY_VALUE,
			'controls' => Filter::ANY_VALUE,
		)), array('track', 'source')));
		$this->add(new Element('source', array_merge($this->globalAttributes, array(
			'src' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
			'media' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('track', array_merge($this->globalAttributes, array(
			'kind' => Filter::ANY_VALUE,
			'src' => Filter::ANY_VALUE,
			'srclang' => Filter::ANY_VALUE,
			'label' => Filter::ANY_VALUE,
			'default' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('canvas', array_merge($this->globalAttributes, array(
			'width' => Filter::ANY_VALUE,
			'height' => Filter::ANY_VALUE,
		)), Filter::CONTENT_TRANSPARENT));
		$this->add(new Element('map', array_merge($this->globalAttributes, array(
			'name' => Filter::ANY_VALUE,
		)), Filter::CONTENT_TRANSPARENT));
		$this->add(new Element('area', array_merge($this->globalAttributes, array(
			'alt' => Filter::ANY_VALUE,
			'coords' => Filter::ANY_VALUE,
			'shape' => Filter::ANY_VALUE,
			'href' => Filter::ANY_VALUE,
			'target' => Filter::ANY_VALUE,
			'rel' => Filter::ANY_VALUE,
			'media' => Filter::ANY_VALUE,
			'hreflang' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
		))));
	}

	protected function loadTabularDataElements()
	{
		$this->add(new Element('table', array_merge($this->globalAttributes, array(
			'border' => Filter::ANY_VALUE,
		)), array('caption', 'colgroup', 'thead', 'tfoot', 'tbody', 'tr')));
		$this->add(new Element('caption', $this->globalAttributes, $this->flowContent));
		$this->add(new Element('colgroup', array_merge($this->globalAttributes, array(
			'span' => new Digit(),
		)), array('col')));
		$this->add(new Element('col', array_merge($this->globalAttributes, array(
			'span' => new Digit(),
		))));
		$this->add(new Element('tbody', $this->globalAttributes, array('tr')));
		$this->add(new Element('thead', $this->globalAttributes, array('tr')));
		$this->add(new Element('tfoot', $this->globalAttributes, array('tr')));
		$this->add(new Element('tr', $this->globalAttributes, array('td', 'th')));
		$this->add(new Element('td', array_merge($this->globalAttributes, array(
			'colspan' => Filter::ANY_VALUE,
			'rowspan' => Filter::ANY_VALUE,
			'headers' => Filter::ANY_VALUE,
		)), $this->flowContent));
		$this->add(new Element('th', array_merge($this->globalAttributes, array(
			'colspan' => Filter::ANY_VALUE,
			'rowspan' => Filter::ANY_VALUE,
			'headers' => Filter::ANY_VALUE,
		)), $this->flowContent));
	}

	protected function loadFormElements()
	{
		$this->add(new Element('form', array_merge($this->globalAttributes, array(
			'accept-charset' => Filter::ANY_VALUE,
			'action' => Filter::ANY_VALUE,
			'autocomplete' => Filter::ANY_VALUE,
			'enctype' => Filter::ANY_VALUE,
			'method' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
			'novalidate' => Filter::ANY_VALUE,
			'target' => Filter::ANY_VALUE,
		)), $this->flowContent));
		$this->add(new Element('fieldset', array_merge($this->globalAttributes, array(
			'disabled' => Filter::ANY_VALUE,
			'form' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
		)), array_merge(array('legend'), $this->flowContent)));
		$this->add(new Element('legend', $this->globalAttributes, $this->phrasingContent));
		$this->add(new Element('label', array_merge($this->globalAttributes, array(
			'form' => Filter::ANY_VALUE,
			'for' => Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new Element('input', array_merge($this->globalAttributes, array(
			'accept' => Filter::ANY_VALUE,
			'alt' => Filter::ANY_VALUE,
			'autocomplete' => Filter::ANY_VALUE,
			'autofocus' => Filter::ANY_VALUE,
			'checked' => Filter::ANY_VALUE,
			'dirname' => Filter::ANY_VALUE,
			'disabled' => Filter::ANY_VALUE,
			'form' => Filter::ANY_VALUE,
			'formaction' => Filter::ANY_VALUE,
			'formenctype' => Filter::ANY_VALUE,
			'formmethod' => Filter::ANY_VALUE,
			'formnovalidate' => Filter::ANY_VALUE,
			'formtarget' => Filter::ANY_VALUE,
			'height' => Filter::ANY_VALUE,
			'list' => Filter::ANY_VALUE,
			'max' => Filter::ANY_VALUE,
			'maxlength' => Filter::ANY_VALUE,
			'min' => Filter::ANY_VALUE,
			'multiple' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
			'pattern' => Filter::ANY_VALUE,
			'placeholder' => Filter::ANY_VALUE,
			'readonly' => Filter::ANY_VALUE,
			'required' => Filter::ANY_VALUE,
			'size' => Filter::ANY_VALUE,
			'src' => Filter::ANY_VALUE,
			'step' => Filter::ANY_VALUE,
			'type' => new InArray(array('hidden', 'text', 'search', 'tel', 'url', 'email', 'password', 'datetime', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'range', 'color', 'checkbox', 'radio', 'file', 'submit', 'image', 'reset', 'button')),
			'value' => Filter::ANY_VALUE,
			'width' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('button', array_merge($this->globalAttributes, array(
			'autofocus' => Filter::ANY_VALUE,
			'disabled' => Filter::ANY_VALUE,
			'form' => Filter::ANY_VALUE,
			'formaction' => Filter::ANY_VALUE,
			'formenctype' => Filter::ANY_VALUE,
			'formmethod' => Filter::ANY_VALUE,
			'formnovalidate' => Filter::ANY_VALUE,
			'formtarget' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
			'type' => Filter::ANY_VALUE,
			'value' => Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new Element('select', array_merge($this->globalAttributes, array(
			'autofocus' => Filter::ANY_VALUE,
			'disabled' => Filter::ANY_VALUE,
			'form' => Filter::ANY_VALUE,
			'multiple' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
			'required' => Filter::ANY_VALUE,
			'size' => Filter::ANY_VALUE,
		)), array('option', 'optgroup')));
		$this->add(new Element('datalist', $this->globalAttributes, array_merge(array('option'), $this->phrasingContent)));
		$this->add(new Element('optgroup', array_merge($this->globalAttributes, array(
			'disabled' => Filter::ANY_VALUE,
			'label' => Filter::ANY_VALUE,
		)), array('option')));
		$this->add(new Element('option', array_merge($this->globalAttributes, array(
			'disabled' => Filter::ANY_VALUE,
			'label' => Filter::ANY_VALUE,
			'selected' => Filter::ANY_VALUE,
			'value' => Filter::ANY_VALUE,
		)), array('#PCDATA')));
		$this->add(new Element('textarea', array_merge($this->globalAttributes, array(
			'autofocus' => Filter::ANY_VALUE,
			'cols' => Filter::ANY_VALUE,
			'dirname' => Filter::ANY_VALUE,
			'disabled' => Filter::ANY_VALUE,
			'form' => Filter::ANY_VALUE,
			'maxlength' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
			'placeholder' => Filter::ANY_VALUE,
			'readonly' => Filter::ANY_VALUE,
			'required' => Filter::ANY_VALUE,
			'rows' => Filter::ANY_VALUE,
			'wrap' => Filter::ANY_VALUE,
		)), array('#PCDATA')));
		$this->add(new Element('keygen', array_merge($this->globalAttributes, array(
			'autofocus' => Filter::ANY_VALUE,
			'challenge' => Filter::ANY_VALUE,
			'disabled' => Filter::ANY_VALUE,
			'form' => Filter::ANY_VALUE,
			'keytype' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('output', array_merge($this->globalAttributes, array(
			'for' => Filter::ANY_VALUE,
			'form' => Filter::ANY_VALUE,
			'name' => Filter::ANY_VALUE,
		))));
		$this->add(new Element('progress', array_merge($this->globalAttributes, array(
			'value' => Filter::ANY_VALUE,
			'max' => Filter::ANY_VALUE,
		)), $this->phrasingContent));
		$this->add(new Element('meter', array_merge($this->globalAttributes, array(
			'value' => Filter::ANY_VALUE,
			'min' => Filter::ANY_VALUE,
			'max' => Filter::ANY_VALUE,
			'low' => Filter::ANY_VALUE,
			'high' => Filter::ANY_VALUE,
			'optimum' => Filter::ANY_VALUE,
		)), $this->phrasingContent));
	}
}
