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

namespace PSX\Html\Filter;

/**
 * Test html filter against several xss vectors to see how the filter behaves.
 * The XSS vextors are taken from http://ha.ckers.org/xss.html
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class XssFilterEvasionCheatSheetTest extends XssTestCase
{
	public function testFilterXss()
	{
		// XSS Locator
		$actual = <<<HTML
';alert(String.fromCharCode(88,83,83))//';alert(String.fromCharCode(88,83,83))//";
alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//--
></SCRIPT>">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>
HTML;

		$this->assertXssEmpty($actual);

		// XSS locator 2
		$actual = <<<HTML
'';!--"<XSS>=&{()}
HTML;

		$expected = <<<HTML
'';!--"
HTML;

		$this->assertXss($expected, $actual);

		// No Filter Evasion
		$actual = <<<HTML
<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);

		// Image XSS using the JavaScript directive
		$actual = <<<HTML
<IMG SRC="javascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);

		// No quotes and no semicolon
		$actual = <<<HTML
<IMG SRC=javascript:alert('XSS')>
HTML;

		$this->assertXssEmpty($actual);

		// Case insensitive XSS attack vector
		$actual = <<<HTML
<IMG SRC=JaVaScRiPt:alert('XSS')>
HTML;

		$this->assertXssEmpty($actual);

		// HTML entities
		$actual = <<<HTML
<IMG SRC=javascript:alert(&quot;XSS&quot;)>
HTML;

		$this->assertXssEmpty($actual);

		// Grave accent obfuscation
		$actual = <<<HTML
<IMG SRC=`javascript:alert("RSnake says, 'XSS'")`>
HTML;

		$this->assertXssEmpty($actual);

		// Malformed IMG tags
		$actual = <<<HTML
<IMG """><SCRIPT>alert("XSS")</SCRIPT>">
HTML;

		$this->assertXssEmpty($actual);

		// fromCharCode
		$actual = <<<HTML
<IMG SRC=javascript:alert(String.fromCharCode(88,83,83))>
HTML;

		$this->assertXssEmpty($actual);

		// UTF-8 Unicode encoding
		$actual = <<<HTML
<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>
HTML;

		$this->assertXssEmpty($actual);

		// Long UTF-8 Unicode encoding without semicolons
		$actual = <<<HTML
<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>
HTML;

		$this->assertXssEmpty($actual);

		// Hex encoding without semicolons
		$actual = <<<HTML
<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>
HTML;

		$this->assertXssEmpty($actual);

		// Embedded tab
		$actual = <<<HTML
<IMG SRC="jav	ascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);

		// Embedded Encoded tab
		$actual = <<<HTML
<IMG SRC="jav&#x09;ascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);

		// Embedded newline to break up XSS
		$actual = <<<HTML
<IMG SRC="jav&#x0A;ascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);

		// Embedded carriage return to break up XSS
		$actual = <<<HTML
<IMG SRC="jav&#x0D;ascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);

		// Null breaks up JavaScript directive
		$actual = '<IMG SRC=java' . chr(0) . 'script:alert("XSS")>';

		$this->assertXssEmpty($actual);


		// Spaces and meta chars before the JavaScript in images for XSS
		$actual = <<<HTML
<IMG SRC=" &#14;  javascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);

		// Non-alpha-non-digit XSS
		$actual = <<<HTML
<SCRIPT/XSS SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);


		$actual = <<<HTML
<BODY onload!#$%&()*~+-_.,:;?@[/|\]^`=alert("XSS")>
HTML;

		$this->assertXssEmpty($actual);


		$actual = <<<HTML
<SCRIPT/SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);

		// Extraneous open brackets
		$actual = <<<HTML
<<SCRIPT>alert("XSS");//<</SCRIPT>
HTML;

		$this->assertXssEmpty($actual);

		// No closing script tags
		$actual = <<<HTML
<SCRIPT SRC=http://ha.ckers.org/xss.js?< B >
HTML;

		$expected = <<<HTML
<b />
HTML;

		$this->assertXss($expected, $actual);

		// Protocol resolution in script tags
		$actual = <<<HTML
<SCRIPT SRC=//ha.ckers.org/.j>
HTML;

		$this->assertXssEmpty($actual);

		// Half open HTML/JavaScript XSS vector
		$actual = <<<HTML
<IMG SRC="javascript:alert('XSS')"
HTML;

		$this->assertXssEmpty($actual);

		// Double open angle brackets
		$actual = <<<HTML
<iframe src=http://ha.ckers.org/scriptlet.html <
HTML;

		$this->assertXssEmpty($actual);

		// End title tag
		$actual = <<<HTML
</TITLE><SCRIPT>alert("XSS");</SCRIPT>
HTML;

		$this->assertXssEmpty($actual);

		// INPUT image
		$actual = <<<HTML
<INPUT TYPE="IMAGE" SRC="javascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);

		// BODY image
		$actual = <<<HTML
<BODY BACKGROUND="javascript:alert('XSS')">
HTML;

		$this->assertXssEmpty($actual);

		// IMG Dynsrc
		$actual = <<<HTML
<IMG DYNSRC="javascript:alert('XSS')">
HTML;

		$this->assertXssEmpty($actual);

		// IMG lowsrc
		$actual = <<<HTML
<IMG LOWSRC="javascript:alert('XSS')">
HTML;

		$this->assertXssEmpty($actual);

		// List-style-image
		$actual = <<<HTML
<STYLE>li {list-style-image: url("javascript:alert('XSS')");}</STYLE><UL><LI>XSS</br>
HTML;

		$expected = <<<HTML
<ul><li>XSS</li></ul>
HTML;

		$this->assertXss($expected, $actual);
		
		// VBscript in an image
		$actual = <<<HTML
<IMG SRC='vbscript:msgbox("XSS")'>
HTML;

		$this->assertXssEmpty($actual);
		
		// Livescript (older versions of Netscape only)
		$actual = <<<HTML
<IMG SRC="livescript:[code]">
HTML;

		$this->assertXssEmpty($actual);
		
		// BODY tag
		$actual = <<<HTML
<BODY ONLOAD=alert('XSS')>
HTML;

		$this->assertXssEmpty($actual);
		
		// BGSOUND
		$actual = <<<HTML
<BGSOUND SRC="javascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);

		// & JavaScript includes
		$actual = <<<HTML
<BR SIZE="&{alert('XSS')}">
HTML;

		$expected = <<<HTML
<br />
HTML;

		$this->assertXss($expected, $actual);

		// STYLE sheet
		$actual = <<<HTML
<LINK REL="stylesheet" HREF="javascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);
		
		// Remote style sheet
		$actual = <<<HTML
<LINK REL="stylesheet" HREF="http://ha.ckers.org/xss.css">
HTML;

		$this->assertXssEmpty($actual);

		// Remote style sheet part 2
		$actual = <<<HTML
<STYLE>@import'http://ha.ckers.org/xss.css';</STYLE>
HTML;

		$this->assertXssEmpty($actual);

		// Remote style sheet part 3
		$actual = <<<HTML
<META HTTP-EQUIV="Link" Content="<http://ha.ckers.org/xss.css>; REL=stylesheet">
HTML;

		$this->assertXssEmpty($actual);

		// Remote style sheet part 4
		$actual = <<<HTML
<STYLE>BODY{-moz-binding:url("http://ha.ckers.org/xssmoz.xml#xss")}</STYLE>
HTML;

		$this->assertXssEmpty($actual);

		// STYLE tags with broken up JavaScript for XSS
		$actual = <<<HTML
<STYLE>@im\port'\ja\vasc\ript:alert("XSS")';</STYLE>
HTML;

		$this->assertXssEmpty($actual);

		// STYLE attribute using a comment to break up expression
		$actual = <<<HTML
<IMG STYLE="xss:expr/*XSS*/ession(alert('XSS'))">
HTML;

		$this->assertXssEmpty($actual);
		
		// IMG STYLE with expression
		$actual = <<<HTML
exp/*<A STYLE='no\xss:noxss("*//*");
xss:ex/*XSS*//*/*/pression(alert("XSS"))'>
HTML;

		$expected = <<<HTML
exp/*
HTML;

		$this->assertXss($expected, $actual);
		
		// STYLE tag (Older versions of Netscape only)
		$actual = <<<HTML
<STYLE TYPE="text/javascript">alert('XSS');</STYLE>
HTML;

		$this->assertXssEmpty($actual);
		
		// STYLE tag using background-image
		$actual = <<<HTML
<STYLE>.XSS{background-image:url("javascript:alert('XSS')");}</STYLE><A CLASS=XSS></A>
HTML;

		$this->assertXssEmpty($actual);
		
		// STYLE tag using background
		$actual = <<<HTML
<STYLE type="text/css">BODY{background:url("javascript:alert('XSS')")}</STYLE>
HTML;

		$this->assertXssEmpty($actual);
		
		// Anonymous HTML with STYLE attribute
		$actual = <<<HTML
<XSS STYLE="xss:expression(alert('XSS'))">
HTML;

		$this->assertXssEmpty($actual);
		
		// Local htc file
		$actual = <<<HTML
<XSS STYLE="behavior: url(xss.htc);">
HTML;

		$this->assertXssEmpty($actual);
		
		// META
		$actual = <<<HTML
<META HTTP-EQUIV="refresh" CONTENT="0;url=javascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);
		
		// META using data
		$actual = <<<HTML
<META HTTP-EQUIV="refresh" CONTENT="0;url=data:text/html base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4K">
HTML;

		$this->assertXssEmpty($actual);
		
		// META with additional URL parameter
		$actual = <<<HTML
<META HTTP-EQUIV="refresh" CONTENT="0; URL=http://;URL=javascript:alert('XSS');">
HTML;

		$this->assertXssEmpty($actual);
		
		// IFRAME
		$actual = <<<HTML
<IFRAME SRC="javascript:alert('XSS');"></IFRAME>
HTML;

		$this->assertXssEmpty($actual);
		
		// FRAME
		$actual = <<<HTML
<FRAMESET><FRAME SRC="javascript:alert('XSS');"></FRAMESET>
HTML;

		$this->assertXssEmpty($actual);
		
		// TABLE
		$actual = <<<HTML
<TABLE BACKGROUND="javascript:alert('XSS')">
HTML;

		$this->assertXssEmpty($actual);
		
		// TD
		$actual = <<<HTML
<TABLE><TD BACKGROUND="javascript:alert('XSS')">
HTML;

		$this->assertXssEmpty($actual);
		
		// DIV background-image
		$actual = <<<HTML
<DIV STYLE="background-image: url(javascript:alert('XSS'))">
HTML;

		$this->assertXssEmpty($actual);
		
		// DIV background-image with unicoded XSS exploit
		$actual = <<<HTML
<DIV STYLE="background-image:\0075\0072\006C\0028'\006a\0061\0076\0061\0073\0063\0072\0069\0070\0074\003a\0061\006c\0065\0072\0074\0028.1027\0058.1053\0053\0027\0029'\0029">
HTML;

		$this->assertXssEmpty($actual);
		
		// DIV background-image plus extra characters
		$actual = <<<HTML
<DIV STYLE="background-image: url(&#1;javascript:alert('XSS'))">
HTML;

		$this->assertXssEmpty($actual);
		
		// DIV expression
		$actual = <<<HTML
<DIV STYLE="width: expression(alert('XSS'));">
HTML;

		$this->assertXssEmpty($actual);
		
		// Downlevel-Hidden block
		$actual = <<<HTML
<!--[if gte IE 4]>
 <SCRIPT>alert('XSS');</SCRIPT>
 <![endif]-->
HTML;

		$this->assertXssEmpty($actual);
		
		// BASE tag
		$actual = <<<HTML
<BASE HREF="javascript:alert('XSS');//">
HTML;

		$this->assertXssEmpty($actual);
		
		// OBJECT tag
		$actual = <<<HTML
<OBJECT TYPE="text/x-scriptlet" DATA="http://ha.ckers.org/scriptlet.html"></OBJECT>
HTML;

		$this->assertXssEmpty($actual);
		
		// Using an EMBED tag you can embed a Flash movie that contains XSS
		$actual = <<<HTML
<EMBED SRC="http://ha.ckers.org/xss.swf" AllowScriptAccess="always"></EMBED>
HTML;

		$this->assertXssEmpty($actual);
		
		// You can EMBED SVG which can contain your XSS vector
		$actual = <<<HTML
<EMBED SRC="data:image/svg+xml;base64,PHN2ZyB4bWxuczpzdmc9Imh0dH A6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcv MjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hs aW5rIiB2ZXJzaW9uPSIxLjAiIHg9IjAiIHk9IjAiIHdpZHRoPSIxOTQiIGhlaWdodD0iMjAw IiBpZD0ieHNzIj48c2NyaXB0IHR5cGU9InRleHQvZWNtYXNjcmlwdCI+YWxlcnQoIlh TUyIpOzwvc2NyaXB0Pjwvc3ZnPg==" type="image/svg+xml" AllowScriptAccess="always"></EMBED>
HTML;

		$this->assertXssEmpty($actual);
		
		// XML data island with CDATA obfuscation
		$actual = <<<HTML
<XML ID="xss"><I><B><IMG SRC="javas<!-- -->cript:alert('XSS')"></B></I></XML>
<SPAN DATASRC="#xss" DATAFLD="B" DATAFORMATAS="HTML"></SPAN>
HTML;

		$expected = <<<HTML


HTML;

		$this->assertXss($expected, $actual);
		
		// Locally hosted XML with embedded JavaScript that is generated using an XML data island
		$actual = <<<HTML
<XML SRC="xsstest.xml" ID=I></XML>
<SPAN DATASRC=#I DATAFLD=C DATAFORMATAS=HTML></SPAN>
HTML;

		$expected = <<<HTML


HTML;

		$this->assertXss($expected, $actual);
		
		// HTML+TIME in XML
		$actual = <<<HTML
<HTML><BODY>
<?xml:namespace prefix="t" ns="urn:schemas-microsoft-com:time">
<?import namespace="t" implementation="#default#time2">
<t:set attributeName="innerHTML" to="XSS<SCRIPT DEFER>alert("XSS")</SCRIPT>">
</BODY></HTML>
HTML;

		$this->assertXssEmpty($actual);
		
		// Assuming you can only fit in a few characters and it filters against ".js"
		$actual = <<<HTML
<SCRIPT SRC="http://ha.ckers.org/xss.jpg"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);
		
		// SSI (Server Side Includes)
		$actual = <<<HTML
<!--#exec cmd="/bin/echo '<SCR'"--><!--#exec cmd="/bin/echo 'IPT SRC=http://ha.ckers.org/xss.js></SCRIPT>'"-->
HTML;

		$this->assertXssEmpty($actual);
		
		// PHP
		$actual = <<<HTML
<? echo('<SCR)';
echo('IPT>alert("XSS")</SCRIPT>'); ?>
HTML;

		$this->assertXssEmpty($actual);
		
		// IMG Embedded commands
		$actual = <<<HTML
<IMG SRC="http://www.thesiteyouareon.com/somecommand.php?somevariables=maliciouscode">
HTML;

		$this->assertXssEmpty($actual);
		
		// Cookie manipulation
		$actual = <<<HTML
<META HTTP-EQUIV="Set-Cookie" Content="USERID=<SCRIPT>alert('XSS')</SCRIPT>">
HTML;

		$this->assertXssEmpty($actual);

		// XSS using HTML quote encapsulation
		$actual = <<<HTML
<SCRIPT a=">" SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);

		$actual = <<<HTML
<SCRIPT =">" SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);
		
		$actual = <<<HTML
<SCRIPT a=">" '' SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);
		
		$actual = <<<HTML
<SCRIPT "a='>'" SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);
		
		$actual = <<<HTML
<SCRIPT a=`>` SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);
		
		$actual = <<<HTML
<SCRIPT a=">'>" SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);
		
		$actual = <<<HTML
<SCRIPT>document.write("<SCRI");</SCRIPT>PT SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$this->assertXssEmpty($actual);
	}
}
