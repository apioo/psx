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

namespace PSX\Framework;

use Doctrine\Common\Annotations\AnnotationRegistry;
use ErrorException;
use PSX\Framework\Config\Config;

/**
 * Bootstrap
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Bootstrap
{
    /**
     * Setup an environment for PSX according to the provided configuration
     *
     * @codeCoverageIgnore
     * @param \PSX\Framework\Config\Config $config
     */
    public static function setupEnvironment(Config $config)
    {
        if (!defined('PSX')) {
            // define benchmark
            $GLOBALS['psx_benchmark'] = microtime(true);

            // define paths
            define('PSX_PATH_CACHE', $config['psx_path_cache']);
            define('PSX_PATH_LIBRARY', $config['psx_path_library']);

            // error handling
            if ($config['psx_debug'] === true) {
                $errorReporting = E_ALL | E_STRICT;
            } else {
                $errorReporting = 0;
            }

            error_reporting($errorReporting);
            set_error_handler('\PSX\Framework\Bootstrap::errorHandler');

            // annotation autoload
            $namespaces = $config->get('psx_annotation_autoload');
            if (!empty($namespaces) && is_array($namespaces)) {
                self::registerAnnotationLoader($namespaces);
            }

            // ini settings
            ini_set('date.timezone', $config['psx_timezone']);
            ini_set('session.use_only_cookies', '1');
            ini_set('docref_root', '');
            ini_set('html_errors', '0');

            // define in psx
            define('PSX', true);
        }
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        // if someone adds an @ to the function call to supress an error message
        // the error reporting is 0 so in this case we dont throw an exception
        if (error_reporting() == 0) {
            return false;
        } else {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
    }

    protected static function registerAnnotationLoader(array $namespaces)
    {
        AnnotationRegistry::reset();
        AnnotationRegistry::registerLoader(function ($class) use ($namespaces) {

            foreach ($namespaces as $namespace) {
                if (strpos($class, $namespace) === 0) {
                    spl_autoload_call($class);

                    return class_exists($class, false);
                }
            }

        });
    }
}
