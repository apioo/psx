<?php

use PSX\Module\ViewAbstract;
use PSX\Odata\MetaDataWriter;

class sample extends ViewAbstract
{
	public function onLoad()
	{
		$this->template->assign('title', 'PSX Framework');

		$this->template->assign('subTitle', 'Template sample ...');

		$this->template->set('sample.tpl');
	}

	/**
	 * @httpMethod GET
	 * @path /odata
	 */
	public function odata()
	{
		$person = new PSX\Payment\Paypal\Data\Payment();
		$writer = new MetaDataWriter('foo');
		$writer->addEntity($person);
		$writer->close();
	}
}
