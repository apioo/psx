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

namespace PSX\Oauth\Tests\Provider;

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Filter\OauthAuthentication;
use PSX\Oauth\Provider\Data\Credentials;
use PSX\Oauth\Tests\ConsumerTest;

/**
 * TestOauth
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestOauth extends ControllerAbstract
{
    public function getRequestFilter()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == ConsumerTest::CONSUMER_KEY && $token == ConsumerTest::TOKEN) {
                return new Credentials(ConsumerTest::CONSUMER_KEY, ConsumerTest::CONSUMER_SECRET, ConsumerTest::TOKEN, ConsumerTest::TOKEN_SECRET);
            }

        });

        return array($handle);
    }

    public function doIndex()
    {
        $this->response->setStatus(200);
        $this->response->getBody()->write('SUCCESS');
    }
}
