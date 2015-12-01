<?php
/**
 * This file is part of the DreamFactory Console Tools Library
 *
 * Copyright 2014 DreamFactory Software, Inc. <support@dreamfactory.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace DreamFactory\Library\Console\Utility;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Some utilities for working with Eloquent outside of Laravel
 */
class EloquentHelper
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The default pattern of files to auto-discover "_*.database.config.php" is the default.
     */
    const DEFAULT_CONFIG_PATTERN = '_*.database.config.php';
    /**
     * @type string
     */
    const DEFAULT_COLLATION = 'utf8_unicode_ci';
    /**
     * @type string
     */
    const DEFAULT_CHARSET = 'utf8';
    /**
     * @type string
     */
    const DEFAULT_DRIVER = 'mysql';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Capsule
     */
    protected static $_capsule = null;
    /**
     * @type array The complete database configuration
     */
    protected static $_config = array();

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Discovers individual database configurations in $path that match $pattern.
     * Discovered configurations are added to the database manager.
     *
     * See config/_default.database.config.php-dist for examples
     *
     * @param string $path
     * @param string $pattern
     *
     * @return bool True if files were autoloaded
     */
    public static function autoload( $path, $pattern = self::DEFAULT_CONFIG_PATTERN )
    {
        $_files = glob( $path . DIRECTORY_SEPARATOR . $pattern, GLOB_MARK );
        $_found = false;

        foreach ( $_files as $_file )
        {
            if ( '.' == $_file || '..' == $_file )
            {
                continue;
            }

            if ( is_file( $_file ) )
            {
                /** @noinspection PhpIncludeInspection */
                $_config = include( $_file );

                if ( is_array( $_config ) )
                {
                    foreach ( $_config as $_id => $_dbConfig )
                    {
                        static::addConnection( static::createCompleteConfig( $_dbConfig ), $_id );
                        $_found = true;
                    }
                }
            }
        }

        if ( !$_found )
        {
            throw new \RuntimeException( 'No database configuration found.' );
        }

        return $_found;
    }

    /**
     * Register a connection with the manager.
     *
     * @param  array  $config
     * @param  string $name
     *
     * @return void
     */
    public static function addConnection( array $config = array(), $name = null )
    {
        static::getCapsule()->addConnection( $config, $name );
    }

    /**
     * @return Capsule
     */
    public static function getCapsule()
    {
        return static::$_capsule ?: static::$_capsule = new Capsule();
    }

    /**
     * Optionally sets this config as the global and boots Eloquent
     *
     * @param bool $asGlobal If true this configuration will be available globally
     */
    public static function boot( $asGlobal = true )
    {
        if ( $asGlobal )
        {
            static::getCapsule()->setAsGlobal();
        }

        static::getCapsule()->bootEloquent();
    }

    /**
     * Given a minimal configuration for a database, fill in the missing requirements
     *
     * @param array $db
     *
     * @return array
     */
    public static function createCompleteConfig( array $db = array() )
    {
        static $_defaults = array(
            '{driver}'    => self::DEFAULT_DRIVER,
            '{charset}'   => self::DEFAULT_CHARSET,
            '{collation}' => self::DEFAULT_COLLATION,
            '{prefix}'    => '',
        );

        static $_template = <<<JSON
{
    "read": {
        "host":  "{host}"
    },
    "write": {
        "host":  "{host}"
    },
    "driver":    "{driver}",
    "database":  "{database}",
    "username":  "{username}",
    "password":  "{password}",
    "charset":   "{charset}",
    "collation": "{collation}",
    "prefix":    "{prefix}"
}
JSON;
        //  Fix database-name => database
        if ( array_key_exists( 'database-name', $db ) )
        {
            $db['database'] = $db['database-name'];
            unset( $db['database-name'] );
        }

        //  Wrap the keys with {}
        foreach ( $db as $_key => $_value )
        {
            $db['{' . $_key . '}'] = $_value;
            unset( $db[$_key] );
        }

        $_config = array_merge( $_defaults, $db );
        $_config = json_decode( str_ireplace( array_keys( $_config ), array_values( $_config ), $_template ), true );

        if ( false === $_config || JSON_ERROR_NONE != json_last_error() )
        {
            throw new \RuntimeException( 'Database configuration file could not be read: ' . print_r( $db, true ) );
        }

        return $_config;
    }
}