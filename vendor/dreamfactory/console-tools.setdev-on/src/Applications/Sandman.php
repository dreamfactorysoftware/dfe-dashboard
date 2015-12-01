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
namespace DreamFactory\Library\Console\Applications;

use DreamFactory\Library\Console\BaseApplication;

/**
 * Mr. Sandman
 */
class Sandman extends BaseApplication
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @type string
     */
    const VERSION = '1.0.0';
    /**
     * @type string Our console command name
     */
    const APP_NAME = 'Sandman';
    /**
     * @type string The pattern for locating commands
     */
    const DEFAULT_COMMAND_PATTERN = '*Command.php';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Constructor.
     *
     * @param string $name    The name of the application
     * @param string $version The version of the application
     * @param array  $config  Array of application settings
     *
     * @api
     */
    public function __construct( $name = 'Bringing you dreams!', $version = self::VERSION, array $config = array() )
    {
        parent::__construct( $name, $version, $config );

        //  Get our commands
        $_classes = $this->_locateCommands( $this->getRegistry()->get( 'command-paths', array() ) );

        foreach ( $_classes as $_class )
        {
            $this->add( new $_class );
        }
    }

    /**
     * Returns a list of command classes for this application
     *
     * @param array  $commandPaths An array of namespace-indexed path(s). This library's commands are always included.
     * @param string $pattern      The pattern of the command class files. Defaults to "*Command.php"
     *
     * @return array An array of fully-qualified class names that were found
     */
    protected function _locateCommands( array $commandPaths = array(), $pattern = self::DEFAULT_COMMAND_PATTERN )
    {
        //  Add our namespace in to the paths to search
        $commandPaths['DreamFactory\\Library\\Console\\Commands\Utility'] =
            dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'Utility';

        $_classes = array();

        foreach ( $commandPaths as $_namespace => $_path )
        {
            $_files = glob( $_path . DIRECTORY_SEPARATOR . $pattern, GLOB_MARK );
            $_classes = array();

            foreach ( $_files as $_file )
            {
                if ( is_file( $_file ) )
                {
                    $_classes[] = $_namespace . '\\' . str_ireplace( '.php', null, basename( $_file ) );
                }
            }
        }

        return $_classes;
    }

}
