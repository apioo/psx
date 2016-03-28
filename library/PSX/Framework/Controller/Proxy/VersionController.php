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

namespace PSX\Framework\Controller\Proxy;

use PSX\Api\DocumentedInterface;
use PSX\Framework\Controller\ApiAbstract;
use PSX\Data\Record;
use PSX\Http\Exception as StatusCode;
use PSX\Http\Request;
use RuntimeException;

/**
 * Through the version controller it is possible to redirect the request to
 * different controllers depending on the provided version
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class VersionController extends ApiAbstract implements DocumentedInterface
{
    const TYPE_ACCEPT = 0x1;
    const TYPE_URI    = 0x2;
    const TYPE_HEADER = 0x3;

    /**
     * @Inject
     * @var \PSX\Framework\Dispatch\Dispatch
     */
    protected $dispatch;

    /**
     * @Inject
     * @var \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    protected $controllerFactory;

    /**
     * @var string
     */
    protected $acceptPattern = 'application\/vnd\.psx\.v(?<version>[\d]+)\+(json|xml)';

    /**
     * @var string
     */
    protected $uriFragment = 'version';

    /**
     * @var string
     */
    protected $headerName = 'Api-Version';

    public function onLoad()
    {
        parent::onLoad();

        $type     = $this->getVersionType();
        $versions = $this->getVersions();

        if ($type === self::TYPE_ACCEPT) {
            $version = $this->getAcceptVersion();
        } elseif ($type == self::TYPE_URI) {
            $version = $this->getUriVersion();
        } elseif ($type == self::TYPE_HEADER) {
            $version = $this->getHeaderVersion();
        } else {
            throw new RuntimeException('Invalid version type');
        }

        if (empty($version)) {
            // in case we have no version number get the last available version
            $controller = end($versions);
        } elseif (isset($versions[$version])) {
            $controller = $versions[$version];
        } else {
            throw new StatusCode\NotAcceptableException('Version is not available');
        }

        $path = $this->reverseRouter->getPath($controller, $this->uriFragments);

        if ($path === null) {
            throw new RuntimeException('Found no path for controller ' . $controller);
        }

        $request = new Request(
            $this->request->getUri()->withPath($path),
            $this->request->getMethod(),
            $this->request->getHeaders(),
            $this->request->getBody()
        );

        $this->dispatch->route($request, $this->response, $this->context);
    }

    public function getDocumentation($version = null)
    {
        $versions = $this->getVersions();
        $class    = null;

        if (empty($version)) {
            $class = end($versions);
        } elseif (isset($versions[$version])) {
            $class = $versions[$version];
        }

        if (!empty($class)) {
            $controller = $this->controllerFactory->getController($class, $this->request, $this->response, $this->context);
            if ($controller instanceof DocumentedInterface) {
                return $controller->getDocumentation($version);
            }
        }

        return null;
    }

    protected function getAcceptVersion()
    {
        $accept  = $this->getHeader('Accept');
        $matches = array();

        preg_match('/^' . $this->acceptPattern . '$/', $accept, $matches);

        return isset($matches['version']) ? $matches['version'] : null;
    }

    protected function getUriVersion()
    {
        return $this->getUriFragment($this->uriFragment);
    }

    protected function getHeaderVersion()
    {
        return $this->getHeader($this->headerName);
    }

    protected function getVersionType()
    {
        return self::TYPE_ACCEPT;
    }

    /**
     * Must return an array which contains as key the version number and as
     * value the name of the controller class
     *
     * @return array
     */
    abstract protected function getVersions();
}

