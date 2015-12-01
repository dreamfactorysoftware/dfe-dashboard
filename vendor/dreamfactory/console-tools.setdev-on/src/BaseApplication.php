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
namespace DreamFactory\Library\Console;

use DreamFactory\Library\Console\Components\Collection;
use DreamFactory\Library\Console\Components\Registry;
use DreamFactory\Library\Console\Utility\CommandHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command that reads/writes a JSON configuration file
 *
 * Additional Settings of $configName and $configPath
 * available to customize storage location
 */
class BaseApplication extends Application
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The name of this application
     */
    const APP_NAME = null;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Registry settings
     */
    protected $_registry;
    /**
     * @type string Paths
     */
    protected $_paths;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $name    The name of the application
     * @param string $version The version of the application
     * @param array  $config  Extra configuration settings
     */
    public function __construct( $name = null, $version = null, array $config = array() )
    {
        parent::__construct( $name, $version );

        $_config = new Collection( $this->addConfigDefaults( $config ) );

        $_path = $_config->get( 'registry-path', getcwd(), true );
        $_values = $_config->get( 'registry-values', array(), true );

        if ( null !== ( $_template = $_config->get( 'registry-template', null, true ) ) )
        {
            $this->_registry = Registry::createFromFile( static::APP_NAME, $_path, $_template, $_values );
            $this->_registry->load();
        }
        else
        {
            $this->_registry = new Registry( static::APP_NAME, $_path, $config );
        }

        $this->_setApplicationPaths();
    }

    /** @inheritdoc */
    public function getLongVersion()
    {
        if ( 'UNKNOWN' === $this->getName() && 'UNKNOWN' === $this->getVersion() )
        {
            return parent::getLongVersion();
        }

        $_name = static::APP_NAME ?: ( isset( $argv, $argv[0] ) ? $argv[0] : basename( __CLASS__ ) );

        return sprintf( '<info>%s v%s:</info> %s</comment>', $_name, $this->getVersion(), $this->getName() );
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * @return string
     */
    public function getRegistryPath()
    {
        $_path = getenv( 'HOME' );

        if ( empty( $_path ) )
        {
            $_path = getcwd();
        }

        return $_path . DIRECTORY_SEPARATOR . '.dreamfactory';
    }

    /**
     * Renders a caught exception.
     *
     * @param \Exception      $e      An exception instance
     * @param OutputInterface $output An OutputInterface instance
     */
    public function renderException( $e, $output )
    {
        $output->writeln( $this->getLongVersion() . ' > <comment>ERROR</comment> ' );

        parent::renderException( $e, $output );
    }

    /**
     * Ensures that the necessary configuration defaults are in place
     *
     * @param array $config
     *
     * @return array
     */
    public function addConfigDefaults( array $config = array() )
    {
        $_timestamp = CommandHelper::timestamp();

        //  Our default registry template
        $config = array_merge(
            array(
                'registry-path'     => $this->getRegistryPath(),
                'registry-template' =>
                    dirname( __DIR__ ) .
                    DIRECTORY_SEPARATOR .
                    'config' .
                    DIRECTORY_SEPARATOR .
                    'templates' .
                    DIRECTORY_SEPARATOR .
                    'registry.schema.json',
                'registry-values'   => array(
                    '{created_at}' => $_timestamp,
                    '{updated_at}' => $_timestamp,
                ),
            ),
            $config
        );

        return $config;
    }

    /**
     * Sets the various paths used by this app
     */
    protected function _setApplicationPaths()
    {
        global $argv;

        if ( !isset( $argv ) )
        {
            throw new \LogicException( 'No arguments in $argv.' );
        }

        $this->_paths = array(
            'base'   => $_basePath = dirname( dirname( $argv[0] ) ),
            'config' => $_basePath . DIRECTORY_SEPARATOR . 'config',
            'vendor' => $_basePath . DIRECTORY_SEPARATOR . 'vendor',
        );
    }

    /**
     * Retrieves one the application paths from settings. These can be "base", "config", or "vendor"
     *
     * @param string $which
     */
    public function getAppPath( $which = null )
    {
        if ( null === $which )
        {
            return $this->_paths;
        }

        if ( isset( $this->_paths[$which] ) )
        {
            return $this->_paths[$which];
        }

        throw new \InvalidArgumentException( 'The path "' . $which . '" was not found.' );
    }
}