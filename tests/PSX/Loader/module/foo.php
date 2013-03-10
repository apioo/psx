<?php

use PSX\ModuleAbstract;

class foo extends ModuleAbstract
{
	public function onLoad()
	{
	}

	/**
	 * @httpMethod GET
	 * @path /test/{foo}
	 */
	public function test()
	{
	}
}
