<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Template\Generator;

use PSX\Data\GraphTraverser;
use PSX\Data\Visitor;
use PSX\Data\RecordInterface;
use PSX\Framework\Template\GeneratorInterface;

/**
 * Html
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Html implements GeneratorInterface
{
    public function generate(RecordInterface $data)
    {
        $visitor = new Visitor\HtmlWriterVisitor();
        $graph   = new GraphTraverser();
        $graph->traverse($data, $visitor);

        return $this->getTemplate($visitor->getOutput());
    }

    protected function getTemplate($html)
    {
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

{$html}

</body>
</html>
HTML;
    }
}
