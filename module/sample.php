<?php

use PSX\Module\ViewAbstract;

class sample extends ViewAbstract
{
	public function onLoad()
	{
		$this->template->assign('title', 'PSX Framework');

		$this->template->assign('subTitle', 'Template sample ...');

		$this->template->set('sample.tpl');
	}
}
