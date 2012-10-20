<?php
/*
 *  $Id: FilterTest.php 560 2012-07-29 02:42:22Z k42b3.x@googlemail.com $
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
 * Test html filter against several xss vectors to see how the filter behaves.
 * The XSS vextors are taken from http://ha.ckers.org/xss.html
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 560 $
 */
class PSX_Html_Filter_XssTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testFilterXss()
	{
		$html = <<<HTML
';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//--></SCRIPT>">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
'';!--"<XSS>=&{()}
HTML;

		$expected = <<<HTML
'';!--"
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="javascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=javascript:alert('XSS')>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=JaVaScRiPt:alert('XSS')>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=javascript:alert(&quot;XSS&quot;)>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=`javascript:alert("RSnake says, 'XSS'")`>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG """><SCRIPT>alert("XSS")</SCRIPT>">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=javascript:alert(String.fromCharCode(88,83,83))>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="jav	ascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="jav&#x09;ascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="jav&#x0A;ascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="jav&#x0D;ascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG
SRC
=
"
j
a
v
a
s
c
r
i
p
t
:
a
l
e
r
t
(
'
X
S
S
'
)
"
>

HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC=" &#14;  javascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT/XSS SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<BODY onload!#$%&()*~+-_.,:;?@[/|\]^`=alert("XSS")>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT/SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<<SCRIPT>alert("XSS");//<</SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT SRC=http://ha.ckers.org/xss.js?<B>
HTML;

		$expected = <<<HTML
<b />
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT SRC=//ha.ckers.org/.j>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="javascript:alert('XSS')"
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<iframe src=http://ha.ckers.org/scriptlet.html <
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT>a=/XSS/
alert(a.source)</SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
</TITLE><SCRIPT>alert("XSS");</SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<INPUT TYPE="IMAGE" SRC="javascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<BODY BACKGROUND="javascript:alert('XSS')">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<BODY ONLOAD=alert('XSS')>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG DYNSRC="javascript:alert('XSS')">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG LOWSRC="javascript:alert('XSS')">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<BGSOUND SRC="javascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<BR SIZE="&{alert('XSS')}">
HTML;

		$expected = <<<HTML
<br />
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<LAYER SRC="http://ha.ckers.org/scriptlet.html"></LAYER>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<LINK REL="stylesheet" HREF="javascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<LINK REL="stylesheet" HREF="http://ha.ckers.org/xss.css">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<STYLE>@import'http://ha.ckers.org/xss.css';</STYLE>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<META HTTP-EQUIV="Link" Content="<http://ha.ckers.org/xss.css>; REL=stylesheet">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<STYLE>BODY{-moz-binding:url("http://ha.ckers.org/xssmoz.xml#xss")}</STYLE>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<XSS STYLE="behavior: url(xss.htc);">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<STYLE>li {list-style-image: url("javascript:alert('XSS')");}</STYLE><UL><LI>XSS
HTML;

		$expected = <<<HTML
<ul><li>XSS</li></ul>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC='vbscript:msgbox("XSS")'>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="mocha:[code]">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="livescript:[code]">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<META HTTP-EQUIV="refresh" CONTENT="0;url=javascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<META HTTP-EQUIV="refresh" CONTENT="0;url=data:text/html;base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4K">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<META HTTP-EQUIV="refresh" CONTENT="0; URL=http://;URL=javascript:alert('XSS');">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IFRAME SRC="javascript:alert('XSS');"></IFRAME>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<FRAMESET><FRAME SRC="javascript:alert('XSS');"></FRAMESET>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<TABLE BACKGROUND="javascript:alert('XSS')">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<TABLE><TD BACKGROUND="javascript:alert('XSS')">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<DIV STYLE="background-image: url(javascript:alert('XSS'))">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<DIV STYLE="background-image:\0075\0072\006C\0028'\006a\0061\0076\0061\0073\0063\0072\0069\0070\0074\003a\0061\006c\0065\0072\0074\0028.1027\0058.1053\0053\0027\0029'\0029">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<DIV STYLE="background-image: url(&#1;javascript:alert('XSS'))">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<DIV STYLE="width: expression(alert('XSS'));">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<STYLE>@im\port'\ja\vasc\ript:alert("XSS")';</STYLE>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG STYLE="xss:expr/*XSS*/ession(alert('XSS'))">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<XSS STYLE="xss:expression(alert('XSS'))">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
exp/*<A STYLE='no\xss:noxss("*//*");
xss:&#101;x&#x2F;*XSS*//*/*/pression(alert("XSS"))'>
HTML;

		$expected = <<<HTML
exp/*<a />
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<STYLE TYPE="text/javascript">alert('XSS');</STYLE>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<STYLE>.XSS{background-image:url("javascript:alert('XSS')");}</STYLE><A CLASS=XSS></A>
HTML;

		$expected = <<<HTML
<a />
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<STYLE type="text/css">BODY{background:url("javascript:alert('XSS')")}</STYLE>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<!--[if gte IE 4]>
<SCRIPT>alert('XSS');</SCRIPT>
<![endif]-->
HTML;

		$expected = <<<HTML



HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<BASE HREF="javascript:alert('XSS');//">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<OBJECT TYPE="text/x-scriptlet" DATA="http://ha.ckers.org/scriptlet.html"></OBJECT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<OBJECT classid=clsid:ae24fdae-03c6-11d1-8b76-0080c744f389><param name=url value=javascript:alert('XSS')></OBJECT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<EMBED SRC="http://ha.ckers.org/xss.swf" AllowScriptAccess="always"></EMBED>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<EMBED SRC="data:image/svg+xml;base64,PHN2ZyB4bWxuczpzdmc9Imh0dH A6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcv MjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hs aW5rIiB2ZXJzaW9uPSIxLjAiIHg9IjAiIHk9IjAiIHdpZHRoPSIxOTQiIGhlaWdodD0iMjAw IiBpZD0ieHNzIj48c2NyaXB0IHR5cGU9InRleHQvZWNtYXNjcmlwdCI+YWxlcnQoIlh TUyIpOzwvc2NyaXB0Pjwvc3ZnPg==" type="image/svg+xml" AllowScriptAccess="always"></EMBED>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
a="get";
b="URL(\"";
c="javascript:";
d="alert('XSS');\")";
eval(a+b+c+d);
HTML;

		$expected = <<<HTML
a="get";
b="URL(\"";
c="javascript:";
d="alert('XSS');\")";
eval(a+b+c+d);
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<HTML xmlns:xss>
  <?import namespace="xss" implementation="http://ha.ckers.org/xss.htc">
  <xss:xss>XSS</xss:xss>
</HTML>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<XML ID=I><X><C><![CDATA[<IMG SRC="javas]]><![CDATA[cript:alert('XSS');">]]>
</C></X></xml><SPAN DATASRC=#I DATAFLD=C DATAFORMATAS=HTML></SPAN>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<XML ID="xss"><I><B>&lt;IMG SRC="javas<!-- -->cript:alert('XSS')"&gt;</B></I></XML>
<SPAN DATASRC="#xss" DATAFLD="B" DATAFORMATAS="HTML"></SPAN>
HTML;

		$expected = <<<HTML


HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<XML SRC="xsstest.xml" ID=I></XML>
<SPAN DATASRC=#I DATAFLD=C DATAFORMATAS=HTML></SPAN>
HTML;

		$expected = <<<HTML


HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<HTML><BODY>
<?xml:namespace prefix="t" ns="urn:schemas-microsoft-com:time">
<?import namespace="t" implementation="#default#time2">
<t:set attributeName="innerHTML" to="XSS&lt;SCRIPT DEFER&gt;alert(&quot;XSS&quot;)&lt;/SCRIPT&gt;">
</BODY></HTML>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT SRC="http://ha.ckers.org/xss.jpg"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<!--#exec cmd="/bin/echo '<SCR'"--><!--#exec cmd="/bin/echo 'IPT SRC=http://ha.ckers.org/xss.js></SCRIPT>'"-->
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<IMG SRC="http://www.thesiteyouareon.com/somecommand.php?somevariables=maliciouscode">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<META HTTP-EQUIV="Set-Cookie" Content="USERID=&lt;SCRIPT&gt;alert('XSS')&lt;/SCRIPT&gt;">
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<HEAD><META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=UTF-7"> </HEAD>+ADw-SCRIPT+AD4-alert('XSS');+ADw-/SCRIPT+AD4-
HTML;

		$expected = <<<HTML
+ADw-SCRIPT+AD4-alert('XSS');+ADw-/SCRIPT+AD4-
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT a=">" SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT =">" SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT a=">" '' SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT "a='>'" SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT a=`>` SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT a=">'>" SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<SCRIPT>document.write("<SCRI");</SCRIPT>PT SRC="http://ha.ckers.org/xss.js"></SCRIPT>
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://66.102.7.147/">XSS</A>
HTML;

		$expected = <<<HTML
<a href="http://66.102.7.147/">XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">XSS</A>
HTML;

		$expected = <<<HTML
<a>XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://1113982867/">XSS</A>
HTML;

		$expected = <<<HTML
<a href="http://1113982867/">XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://0x42.0x0000066.0x7.0x93/">XSS</A>
HTML;

		$expected = <<<HTML
<a href="http://0x42.0x0000066.0x7.0x93/">XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://0102.0146.0007.00000223/">XSS</A>
HTML;

		$expected = <<<HTML
<a href="http://0102.0146.0007.00000223/">XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="h
tt	p://6&#9;6.000146.0x7.147/">XSS</A>
HTML;

		$expected = <<<HTML
<a>XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="//google">XSS</A>
HTML;

		$expected = <<<HTML
<a>XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://ha.ckers.org@google">XSS</A>
HTML;

		$expected = <<<HTML
<a href="http://ha.ckers.org@google">XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://google:ha.ckers.org">XSS</A>
HTML;

		$expected = <<<HTML
<a>XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://google.com/">XSS</A>
HTML;

		$expected = <<<HTML
<a href="http://google.com/">XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://www.google.com./">XSS</A>
HTML;

		$expected = <<<HTML
<a>XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="javascript:document.location='http://www.google.com/'">XSS</A>
HTML;

		$expected = <<<HTML
<a>XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<A HREF="http://www.gohttp://www.google.com/ogle.com/">XSS</A>
HTML;

		$expected = <<<HTML
<a href="http://www.gohttp://www.google.com/ogle.com/">XSS</a>
HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<
%3C
&lt
&lt;
&LT
&LT;
&#60
&#060
&#0060
&#00060
&#000060
&#0000060
&#60;
&#060;
&#0060;
&#00060;
&#000060;
&#0000060;
&#x3c
&#x03c
&#x003c
&#x0003c
&#x00003c
&#x000003c
&#x3c;
&#x03c;
&#x003c;
&#x0003c;
&#x00003c;
&#x000003c;
&#X3c
&#X03c
&#X003c
&#X0003c
&#X00003c
&#X000003c
&#X3c;
&#X03c;
&#X003c;
&#X0003c;
&#X00003c;
&#X000003c;
&#x3C
&#x03C
&#x003C
&#x0003C
&#x00003C
&#x000003C
&#x3C;
&#x03C;
&#x003C;
&#x0003C;
&#x00003C;
&#x000003C;
&#X3C
&#X03C
&#X003C
&#X0003C
&#X00003C
&#X000003C
&#X3C;
&#X03C;
&#X003C;
&#X0003C;
&#X00003C;
&#X000003C;
\x3c
\x3C
\u003c
\u003C
HTML;

		$expected = <<<HTML

HTML;

		$filter = new PSX_Html_Filter($html);

		$this->assertEquals($expected, $filter->filter());
	}
}