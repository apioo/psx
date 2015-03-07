<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Template;

use PSX\Data\RecordInterface;
use PSX\Util\CurveArray;

/**
 * FallbackGenerator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FallbackGenerator implements GeneratorInterface
{
	public function generate(array $data)
	{
		$data = $this->getRecData($data);
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
		white-space:pre-wrap;
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
		white-space:pre-wrap;
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
