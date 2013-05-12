<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Oauth\Signature;

/**
 * HMACSHA1Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HMACSHA1Test extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testSignature()
	{
		$signature = new HMACSHA1();

		$this->assertEquals('yUsI5jibqNBZ9W+XPOJaeyrwluM=', $signature->build('', 'djr9rjt0jd78jf88', 'jjd999tj88uiths3'));
		$this->assertEquals('DiK4c9nwDwPEAQPV1XL153JMKvc=', $signature->build('', 'djr9rjt0jd78jf88', 'jjd99$tj88uiths3'));
		$this->assertEquals('QdIl7ldiQOL0oFLNUENqw/mw3ls=', $signature->build('', 'djr9rjt0jd78jf88'));

		$this->assertEquals('egQqG5AJep5sJ7anhXju1unge2I=', $signature->build('bs', 'cs', ''));
		$this->assertEquals('VZVjXceV7JgPq/dOTnNmEfO0Fv8=', $signature->build('bs', 'cs', 'ts'));
		$this->assertEquals('tR3+Ty81lMeYAr/Fid0kMTYa/WM=', $signature->build('GET&http%3A%2F%2Fphotos.example.net%2Fphotos&file%3Dvacation.jpg%26oauth_consumer_key%3Ddpf43f3p2l4k3l03%26oauth_nonce%3Dkllo9940pd9333jh%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestamp%3D1191242096%26oauth_token%3Dnnch734d00sl2jdk%26oauth_version%3D1.0%26size%3Doriginal', 'kd94hf93k423kf44', 'pfkkdhi9sl3r4s00'));
	}
}