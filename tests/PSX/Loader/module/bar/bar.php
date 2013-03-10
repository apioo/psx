<?php

namespace bar;

use PSX\ModuleAbstract;

class bar extends ModuleAbstract
{
	public function onLoad()
	{
	}

	/**
	 * @httpMethod GET
	 * @path /test/{bar}
	 */
	public function test()
	{
	}
}
