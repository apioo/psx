<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

return array(

	// The url to the psx public folder (i.e. http://127.0.0.1/psx/public)
	'psx_url'                 => 'http://127.0.0.1/projects/psx/public',

	// The input path 'index.php/' or '' if you use mod_rewrite
	'psx_dispatch'            => 'index.php/',

	// The default timezone
	'psx_timezone'            => 'UTC',

	// Whether PSX runs in debug mode or not. If not the error reporting is 
	// set to 0
	'psx_debug'               => true,

	// Your SQL connections
	'psx_sql_host'            => 'localhost',
	'psx_sql_user'            => 'root',
	'psx_sql_pw'              => '',
	'psx_sql_db'              => 'psx',

	// Path to the routing file
	'psx_routing'             => __DIR__ . '/routes',

	// Path to the cache folder
	'psx_path_cache'          => __DIR__ . '/cache',

	// Path to the library folder
	'psx_path_library'        => __DIR__ . '/library',

	// Class name of the error controller
	//'psx_error_controller'    => null,

	// If you only want to change the appearance of the error page you can 
	// specify a custom template
	//'psx_error_template'      => null,

);
