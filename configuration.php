<?php

/*
This is the configuration file of PSX. Every parameter can be used inside your
application or in the DI container. Which configuration file gets loaded depends 
on the DI container parameter "config.file". See the container.php if you want 
load a different configuration depending on the environment.
*/

return array(

    // The url to the psx public folder (i.e. http://127.0.0.1/psx/public, 
    // http://localhost.com or //localhost.com)
    'psx_url'                 => 'http://127.0.0.1/projects/psx/public',

    // The input path 'index.php/' or '' if you use mod_rewrite
    'psx_dispatch'            => 'index.php/',

    // The default timezone
    'psx_timezone'            => 'UTC',

    // Whether PSX runs in debug mode or not. If not error reporting is set to 0
    'psx_debug'               => true,

    // Log settings, the handler is one of: stream, logcaster, void, system
    'psx_log_level'           => \Monolog\Logger::ERROR,
    'psx_log_handler'         => 'system',
    'psx_log_uri'             => null,

    // Your SQL connections
    'psx_sql_host'            => 'localhost',
    'psx_sql_user'            => 'root',
    'psx_sql_pw'              => '',
    'psx_sql_db'              => 'psx',

    // Path to the routing file
    'psx_routing'             => __DIR__ . '/routes',

    // Folder locations
    'psx_path_cache'          => __DIR__ . '/cache',
    'psx_path_library'        => __DIR__ . '/library',

    // Class name of the error controller
    //'psx_error_controller'    => null,

    // If you only want to change the appearance of the error page you can 
    // specify a custom template
    //'psx_error_template'      => null,

);
