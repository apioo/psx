<?php
/*
 *  $Id: TextListenerInterface.php 560 2012-07-29 02:42:22Z k42b3.x@googlemail.com $
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
 * TextListenerInterface
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Html
 * @version    $Revision: 560 $
 */
interface PSX_Html_Filter_TextListenerInterface
{
	/**
	 * Is called if an text occurs if the method returns false the text gets
	 * removed if true the text stays. If an PSX_Html_Lexer_Token_Text is
	 * returned the text gets replaced with the returned text
	 *
	 * @return boolean|PSX_Html_Lexer_Token_Text
	 */
	public function onText(PSX_Html_Lexer_Token_Text $text);
}