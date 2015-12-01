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
namespace DreamFactory\Library\Console\Bags;

use DreamFactory\Library\Console\Interfaces\NodeLike;

/**
 * Super simple generic array/bag. API modeled after the many Symfony2 bags
 */
class GenericBag implements \IteratorAggregate, \Countable, NodeLike
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string My ID
     */
    protected $_id;
    /**
     * @type array
     */
    protected $_contents = array();

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Construct a bag
     *
     * @param string                    $id
     * @param \Traversable|object|array $contents An array of key value pairs to stuff into the bag
     */
    public function __construct( $id, $contents = array() )
    {
        $this->_id = $id;
        $this->initialize( $contents );
    }

    /**
     * @param array $contents
     *
     * @return GenericBag
     */
    public function initialize( $contents = array() )
    {
        $this->_contents = $contents;

        return $this;
    }

    /**
     * Removes all bag items
     *
     * @return $this|GenericBag
     */
    public function clear()
    {
        $this->_contents = array();

        return $this;
    }

    /**
     * Returns an array of all node entries
     *
     * @param string $format A data format in which to provide the results. Valid options are null and "json"
     *
     * @return array|string
     */
    public function all( $format = null )
    {
        switch ( $format )
        {
            case 'json':
                return json_encode( $this->all(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
        }

        return empty( $this->_contents ) ? array() : $this->_contents;
    }

    /**
     * Returns all the settings keys
     *
     * @return array
     */
    public function keys()
    {
        return array_keys( $this->_contents );
    }

    /**
     * Returns all the settings keys
     *
     * @return array
     */
    public function values()
    {
        return array_values( $this->_contents );
    }

    /**
     * Replaces the current contents of the bag
     *
     * @param array|mixed $contents The new contents
     *
     * @return $this
     */
    public function replace( $contents = array() )
    {
        $this->_contents = $contents;

        return $this;
    }

    /**
     * Adds new content to the bag
     *
     * @param array $contents An array of key value pairs
     *
     * @return $this
     */
    public function add( array $contents = array() )
    {
        $this->_contents = array_replace( $this->_contents, $contents );

        return $this;
    }

    /**
     * Get an item from the bag
     *
     * @param string $key              The key
     * @param mixed  $defaultValue     A default value if  not found
     *
     * @param bool   $burnAfterReading If true, item is deleted after reading
     *
     * @return mixed
     */
    public function get( $key = null, $defaultValue = null, $burnAfterReading = false )
    {
        if ( null === $key )
        {
            return $this->all();
        }

        $_value = $defaultValue;

        if ( false !== ( $_key = $this->has( $key ) ) )
        {
            $_value = $this->_contents[$_key];

            if ( $burnAfterReading )
            {
                $this->remove( $key );
            }
        }

        return $_value;
    }

    /**
     * Sets a value in the bag
     *
     * @param string $key       The key
     * @param mixed  $value     The value to set
     * @param bool   $overwrite Whether to overwrite existing data. Defaults to true
     *
     * @return $this
     */
    public function set( $key, $value, $overwrite = true )
    {
        $_key = $this->normalizeKey( $key );

        if ( isset( $this->_contents[$_key] ) && !$overwrite )
        {
            throw new \LogicException( 'The key "' . $key . '" exists and overwrite is disabled.' );
        }

        $this->_contents[$_key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param bool   $returnNormalizedKey If true and the key was found, the normalized key is returned. False otherwise
     *
     * @return bool|string The normalized key if found, or false
     */
    public function has( $key, $returnNormalizedKey = true )
    {
        $_key = $this->normalizeKey( $key );

        return
            array_key_exists( $_key, $this->_contents )
                ? ( $returnNormalizedKey ? $_key : true )
                : false;
    }

    /**
     * @param string $key
     *
     * @return bool True if the key existed and was deleted
     */
    public function remove( $key )
    {
        if ( false !== ( $_key = $this->has( $key ) ) )
        {
            unset( $this->_contents[$_key] );

            return true;
        }

        return false;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function normalizeKey( $key )
    {
        return strtolower( $key );
    }

    /**
     * Returns an iterator for the bag
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->_contents );
    }

    /**
     * Returns the size of the bag
     *
     * @return int
     */
    public function count()
    {
        return count( $this->_contents );
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

}
