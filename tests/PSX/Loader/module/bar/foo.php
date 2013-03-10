<?php

namespace bar;

use PSX\ModuleAbstract;

class foo extends ModuleAbstract
{
	public function onLoad()
	{
	}

	/**
	 * @httpMethod GET
	 * @path /test
	 */
	public function test()
	{
	}
}
