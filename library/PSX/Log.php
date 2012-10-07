<?php
/*
 *  $Id: Log.php 613 2012-08-25 11:14:39Z k42b3.x@googlemail.com $
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
 * The class offers basic log functionality. The following example writes all
 * logs to the file log.txt. If no handler is attached to the logger all
 * messages are discarded
 * <code>
 * PSX_Log::getLogger()->addHandler(new PSX_Log_Handler_File('log.txt'));
 *
 * if($isAdmin === true)
 * {
 * 	PSX_Log::info($_SERVER['REMOTE_ADDR'] . ' has entered admin area');
 * }
 * </code>
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Log
 * @version    $Revision: 613 $
 */
class PSX_Log
{
	const ALL   = 0x7;
	const TRACE = 0x6;
	const DEBUG = 0x5;
	const INFO  = 0x4;
	const WARN  = 0x3;
	const ERROR = 0x2;
	const FATAL = 0x1;
	const OFF   = 0x0;

	public static $instance = null;

	private $handler = array();
	private $level;

	private function __construct()
	{
		$this->setLevel(self::WARN);
	}

	public function addHandler(PSX_Log_HandlerInterface $handler)
	{
		$this->handler[] = $handler;
	}

	public function clearHandler()
	{
		$this->handler = array();
	}

	public function setLevel($level)
	{
		$this->level = (integer) $level;
	}

	public function getLevel()
	{
		return $this->level;
	}

	public function addMessage($level, $msg)
	{
		if($level <= $this->getLevel())
		{
			foreach($this->handler as $handler)
			{
				$handler->write(self::level($level), $msg);
			}
		}
	}

	public static function fatal($msg)
	{
		self::getLogger()->addMessage(self::FATAL, $msg);
	}

	public static function error($msg)
	{
		self::getLogger()->addMessage(self::ERROR, $msg);
	}

	public static function warn($msg)
	{
		self::getLogger()->addMessage(self::WARN, $msg);
	}

	public static function info($msg)
	{
		self::getLogger()->addMessage(self::INFO, $msg);
	}

	public static function debug($msg)
	{
		self::getLogger()->addMessage(self::DEBUG, $msg);
	}

	public static function trace($msg)
	{
		self::getLogger()->addMessage(self::TRACE, $msg);
	}

	public static function getLogger()
	{
		if(self::$instance === null)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function level($level = false)
	{
		$levels = array(

			self::FATAL => 'FATAL',
			self::ERROR => 'ERROR',
			self::WARN  => 'WARN',
			self::INFO  => 'INFO',
			self::DEBUG => 'DEBUG',
			self::TRACE => 'TRACE',

		);

		return isset($levels[$level]) ? $levels[$level] : 'UNKNOWN';
	}
}
