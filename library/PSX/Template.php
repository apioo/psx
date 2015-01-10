<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use PSX\Data\RecordInterface;
use PSX\Template\ErrorException;
use PSX\Util\CurveArray;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Template
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Template implements TemplateInterface
{
	protected $dir;
	protected $file;
	protected $data = array();

	public function setDir($dir)
	{
		$this->dir = $dir;
	}

	public function getDir()
	{
		return $this->dir;
	}

	public function set($file)
	{
		$this->file = $file;
	}

	public function get()
	{
		return $this->file;
	}

	public function hasFile()
	{
		return !empty($this->file);
	}

	public function fileExists()
	{
		return is_file($this->file);
	}

	public function getFile()
	{
		return $this->dir != null ? $this->dir . '/' . $this->file : $this->file;
	}

	public function assign($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function transform()
	{
		$file = $this->getFile();

		if(!is_file($file))
		{
			// in this case we use the fallback template
			$html = $this->getFallbackTemplate();
		}
		else
		{
			// parse template
			try
			{
				ob_start();

				includeTemplateScope($this->data, $file);

				$html = ob_get_clean();
			}
			catch(\Exception $e)
			{
				throw new ErrorException($e->getMessage(), $e, $this->getFile(), ob_get_clean());
			}
		}

		return $html;
	}

	protected function getFallbackTemplate()
	{
		// if we use the fallback template we dont want to expose the default 
		// template values only the actual data set by the user
		$reservedKeys = array(
			'self'     => null, 
			'url'      => null, 
			'base'     => null, 
			'render'   => null, 
			'location' => null, 
			'router'   => null
		);

		$data = $this->getRecData(array_diff_key($this->data, $reservedKeys));
		$dump = $this->getHtml($data);

		return <<<HTML
<!DOCTYPE>
<html>
<head>
	<style type="text/css">
	body
	{
		font-family:monospace;
	}

	dl
	{
		margin:0px;
	}

	dt
	{
		background-color:#eee;
		padding-left:8px;
		padding-top:8px;
		padding-bottom:8px;
		border-bottom:1px solid #999;
	}

	dd
	{
		margin:0px;
		margin-left:24px;
		padding-left:8px;
		padding-top:8px;
		padding-bottom:8px;
	}

	ul
	{
		margin:0px;
		padding:0px;
		list-style-type:none;
	}

	li
	{
		border-bottom:2px solid #222;
		padding-bottom:8px;
		margin-bottom:8px;
	}
	</style>
</head>
<body>

{$dump}

</body>
</html>
HTML;
	}

	protected function getHtml(array $fields)
	{
		$html = '<dl>';

		foreach($fields as $key => $value)
		{
			$html.= '<dt>' . htmlspecialchars($key) . '</dt>';

			if(is_array($value))
			{
				if(CurveArray::isAssoc($value))
				{
					$html.= '<dd class="object">' . $this->getHtml($value) . '</dd>';
				}
				else
				{
					$html.= '<dd class="array">';
					$html.= '<ul>';

					foreach($value as $v)
					{
						if(is_array($v))
						{
							$html.= '<li>' . $this->getHtml($v) . '</li>';
						}
						else
						{
							$html.= '<li>' . htmlspecialchars($v) . '</li>';
						}
					}

					$html.= '</ul>';
					$html.= '</dd>';
				}
			}
			else
			{
				$html.= '<dd class="scalar">' . htmlspecialchars($value) . '</dd>';
			}
		}

		$html.= '</dl>';

		return $html;
	}

	protected function getRecData(array $fields)
	{
		$data = array();

		foreach($fields as $k => $v)
		{
			if(isset($v))
			{
				if(is_array($v))
				{
					$data[$k] = $this->getRecData($v);
				}
				else if($v instanceof RecordInterface)
				{
					$data[$k] = $this->getRecData($v->getRecordInfo()->getData());
				}
				else if($v instanceof \DateTime)
				{
					$data[$k] = $v->format(\DateTime::RFC3339);
				}
				else if(is_object($v))
				{
					$data[$k] = (string) $v;
				}
				else if(is_bool($v))
				{
					$data[$k] = $v ? '1' : '0';
				}
				else
				{
					$data[$k] = $v;
				}
			}
		}

		return $data;
	}
}

/**
 * Includes the file without exposing the properties of the template object
 */
function includeTemplateScope(array $data, $file)
{
	// populate the data vars in the scope of the template
	extract($data, EXTR_SKIP);

	// include file
	require_once($file);
}

