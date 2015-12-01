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
namespace DreamFactory\Library\Console\Interfaces;

interface NodeLike
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string Our key
     */
    const META_DATA_KEY = '_metadata';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Initializes the contents of the bag
     *
     * @param mixed $contents
     *
     * @return NodeLike
     */
    public function initialize( $contents = null );

    /**
     * Removes all bag items
     *
     * @return NodeLike
     */
    public function clear();

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has( $key );

    /**
     * Retrieves a value at the given key location, or the default value if key isn't found.
     * Setting $burnAfterReading to true will remove the key-value pair from the bag after it
     * is retrieved. Call with no arguments to get back a KVP array of contents
     *
     * @param string $key
     * @param mixed  $defaultValue
     * @param bool   $burnAfterReading
     *
     * @return mixed
     */
    public function get( $key = null, $defaultValue = null, $burnAfterReading = false );

    /**
     * @param string $key
     * @param mixed  $value
     * @param bool   $overwrite
     *
     * @return NodeLike
     */
    public function set( $key, $value, $overwrite = true );

    /**
     * @param string $key
     *
     * @return bool True if the key existed and was deleted
     */
    public function remove( $key );

    /**
     * Returns an array of all node entries
     *
     * @param string $format A data format in which to provide the results. Valid options are null and "json"
     *
     * @return array|string
     */
    public function all( $format = null );
}