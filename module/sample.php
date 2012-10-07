<?php

class sample extends PSX_Module_ViewAbstract
{
	public function onLoad()
	{
		$this->template->assign('title', 'PSX Framework');

		$this->template->assign('subTitle', 'Template sample ...');

		$this->template->set('sample.tpl');
	}
}
