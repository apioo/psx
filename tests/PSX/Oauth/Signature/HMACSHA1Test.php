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

namespace PSX\Oauth\Signature;

/**
 * HMACSHA1Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class HMACSHA1Test extends \PHPUnit_Framework_TestCase
{
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