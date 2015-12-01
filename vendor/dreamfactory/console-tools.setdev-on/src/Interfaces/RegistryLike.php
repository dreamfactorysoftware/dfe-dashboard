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

use DreamFactory\Library\Console\Components\Registry;

/**
 * Something that acts like a registry
 */
interface RegistryLike extends NodeLike
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $id           The id of this registry
     * @param string $path         Registry storage path
     * @param string $file         The absolute path to a source JSON file
     * @param array  $replacements Array of replacement KVP for file
     *
     * @return Registry
     */
    public static function createFromFile( $id, $path, $file, array $replacements = array() );

    /**
     * @return array
     */
    public function load();

    /**
     * @param string $comment
     *
     * @return array
     */
    public function save( $comment = null );
}
 