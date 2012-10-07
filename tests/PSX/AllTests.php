<?php
/*
 *  $Id: AllTests.php 636 2012-09-01 10:32:42Z k42b3.x@googlemail.com $
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
 * PSXAllTests
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 636 $
 */
class PSXAllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('library');

		$suite->addTestSuite('PSX_Atom_EntryTest');
		$suite->addTestSuite('PSX_AtomTest');
		$suite->addTestSuite('PSX_BaseTest');
		$suite->addTestSuite('PSX_BootstrapTest');
		$suite->addTestSuite('PSX_Cache_Handler_ApcTest');
		$suite->addTestSuite('PSX_Cache_Handler_FileTest');
		$suite->addTestSuite('PSX_Cache_Handler_MemcacheTest');
		$suite->addTestSuite('PSX_Cache_Handler_SqlTest');
		$suite->addTestSuite('PSX_CacheTest');
		$suite->addTestSuite('PSX_ConfigTest');
		$suite->addTestSuite('PSX_Data_ArrayTest');
		$suite->addTestSuite('PSX_Data_MessageTest');
		$suite->addTestSuite('PSX_Data_Reader_DomTest');
		$suite->addTestSuite('PSX_Data_Reader_FormTest');
		$suite->addTestSuite('PSX_Data_Reader_GpcTest');
		$suite->addTestSuite('PSX_Data_Reader_JsonTest');
		$suite->addTestSuite('PSX_Data_Reader_RawTest');
		$suite->addTestSuite('PSX_Data_Reader_XmlTest');
		$suite->addTestSuite('PSX_Data_ReaderResultTest');
		$suite->addTestSuite('PSX_Data_ResultSetTest');
		$suite->addTestSuite('PSX_Data_Writer_Atom_EntryTest');
		$suite->addTestSuite('PSX_Data_Writer_AtomTest');
		$suite->addTestSuite('PSX_Data_Writer_FormTest');
		$suite->addTestSuite('PSX_Data_Writer_JsonTest');
		$suite->addTestSuite('PSX_Data_Writer_Rss_ItemTest');
		$suite->addTestSuite('PSX_Data_Writer_RssTest');
		$suite->addTestSuite('PSX_Data_Writer_XmlTest');
		$suite->addTestSuite('PSX_Data_WriterResultTest');
		$suite->addTestSuite('PSX_FileTest');
		$suite->addTestSuite('PSX_Filter_AlnumTest');
		$suite->addTestSuite('PSX_Filter_AlphaTest');
		$suite->addTestSuite('PSX_Filter_DateIntervalTest');
		$suite->addTestSuite('PSX_Filter_DateTimeTest');
		$suite->addTestSuite('PSX_Filter_DigitTest');
		$suite->addTestSuite('PSX_Filter_EmailTest');
		$suite->addTestSuite('PSX_Filter_HtmlTest');
		$suite->addTestSuite('PSX_Filter_InArrayTest');
		$suite->addTestSuite('PSX_Filter_IpTest');
		$suite->addTestSuite('PSX_Filter_KeyExistsTest');
		$suite->addTestSuite('PSX_Filter_LengthTest');
		$suite->addTestSuite('PSX_Filter_Md5Test');
		$suite->addTestSuite('PSX_Filter_RegexpTest');
		$suite->addTestSuite('PSX_Filter_Sha1Test');
		$suite->addTestSuite('PSX_Filter_TableTest');
		$suite->addTestSuite('PSX_Filter_UrlTest');
		$suite->addTestSuite('PSX_Filter_UrldecodeTest');
		$suite->addTestSuite('PSX_Filter_XdigitTest');
		$suite->addTestSuite('PSX_Html_FilterTest');
		$suite->addTestSuite('PSX_Html_LexerTest');
		$suite->addTestSuite('PSX_Html_PagingTest');
		$suite->addTestSuite('PSX_Html_ParseTest');
		$suite->addTestSuite('PSX_Http_DeleteRequestTest');
		$suite->addTestSuite('PSX_Http_GetRequestTest');
		$suite->addTestSuite('PSX_Http_HeadRequestTest');
		$suite->addTestSuite('PSX_Http_Handler_CurlTest');
		$suite->addTestSuite('PSX_Http_Handler_SocksTest');
		$suite->addTestSuite('PSX_Http_MessageTest');
		$suite->addTestSuite('PSX_Http_PostRequestTest');
		$suite->addTestSuite('PSX_Http_PutRequestTest');
		$suite->addTestSuite('PSX_Http_RequestTest');
		$suite->addTestSuite('PSX_Http_ResponseTest');
		$suite->addTestSuite('PSX_HttpTest');
		$suite->addTestSuite('PSX_Input_CookieTest');
		$suite->addTestSuite('PSX_Input_FilesTest');
		$suite->addTestSuite('PSX_Input_GetTest');
		$suite->addTestSuite('PSX_Input_PostTest');
		$suite->addTestSuite('PSX_Input_RequestTest');
		$suite->addTestSuite('PSX_Input_SessionTest');
		$suite->addTestSuite('PSX_InputTest');
		$suite->addTestSuite('PSX_JsonTest');
		$suite->addTestSuite('PSX_LoaderTest');
		$suite->addTestSuite('PSX_Log_Handler_FileTest');
		$suite->addTestSuite('PSX_Log_Handler_PrintTest');
		$suite->addTestSuite('PSX_Log_Handler_SqlTest');
		$suite->addTestSuite('PSX_Log_Handler_SysTest');
		$suite->addTestSuite('PSX_LogTest');
		$suite->addTestSuite('PSX_Oauth_Provider_Data_ConsumerTest');
		$suite->addTestSuite('PSX_Oauth_Provider_Data_RequestTest');
		$suite->addTestSuite('PSX_Oauth_Provider_Data_ResponseTest');
		$suite->addTestSuite('PSX_Oauth_Signature_HMACSHA1Test');
		$suite->addTestSuite('PSX_Oauth_Signature_PLAINTEXTTest');
		$suite->addTestSuite('PSX_Oauth_Signature_RSASHA1Test');
		$suite->addTestSuite('PSX_OauthTest');
		$suite->addTestSuite('PSX_OembedTest');
		$suite->addTestSuite('PSX_OpengraphTest');
		$suite->addTestSuite('PSX_OpenId_ProviderAbstractTest');
		$suite->addTestSuite('PSX_OpenIdTest');
		$suite->addTestSuite('PSX_PubSubHubBubTest');
		$suite->addTestSuite('PSX_RegistryTest');
		$suite->addTestSuite('PSX_Rss_ItemTest');
		$suite->addTestSuite('PSX_RssTest');
		$suite->addTestSuite('PSX_Session_Handler_SqlTest');
		$suite->addTestSuite('PSX_SessionTest');
		$suite->addTestSuite('PSX_Sql_ConditionTest');
		$suite->addTestSuite('PSX_Sql_Driver_Mysqli_StmtTest');
		$suite->addTestSuite('PSX_Sql_Driver_MysqliTest');
		$suite->addTestSuite('PSX_Sql_Driver_Pdo_StmtTest');
		$suite->addTestSuite('PSX_Sql_Driver_PdoTest');
		$suite->addTestSuite('PSX_Sql_Table_SelectTest');
		$suite->addTestSuite('PSX_Sql_TableTest');
		$suite->addTestSuite('PSX_Sql_UtilTest');
		$suite->addTestSuite('PSX_SqlTest');
		$suite->addTestSuite('PSX_TemplateTest');
		$suite->addTestSuite('PSX_TimeTest');
		$suite->addTestSuite('PSX_Upload_FileTest');
		$suite->addTestSuite('PSX_UriTest');
		$suite->addTestSuite('PSX_UrlTest');
		$suite->addTestSuite('PSX_UrnTest');
		$suite->addTestSuite('PSX_Util_BencodingTest');
		$suite->addTestSuite('PSX_Util_ConversionTest');
		$suite->addTestSuite('PSX_Util_MarkdownTest');
		$suite->addTestSuite('PSX_Util_NumerativeTest');
		$suite->addTestSuite('PSX_Util_RomanTest');
		$suite->addTestSuite('PSX_Util_UnquoteTest');
		$suite->addTestSuite('PSX_Util_UnregisterTest');
		$suite->addTestSuite('PSX_Util_UuidTest');
		$suite->addTestSuite('PSX_ValidateTest');
		$suite->addTestSuite('PSX_WebfingerTest');
		$suite->addTestSuite('PSX_XmlTest');
		$suite->addTestSuite('PSX_Yadis_Xrd_ExpiresTest');
		$suite->addTestSuite('PSX_Yadis_Xrd_LocalidTest');
		$suite->addTestSuite('PSX_Yadis_Xrd_ServerstatusTest');
		$suite->addTestSuite('PSX_Yadis_Xrd_ServiceTest');
		$suite->addTestSuite('PSX_Yadis_Xrd_StatusTest');
		$suite->addTestSuite('PSX_Yadis_Xrd_TypeTest');
		$suite->addTestSuite('PSX_Yadis_XrdTest');
		$suite->addTestSuite('PSX_YadisTest');


		return $suite;
	}
}