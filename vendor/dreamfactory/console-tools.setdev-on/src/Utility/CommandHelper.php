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

/**
 * Command helpers
 */
class CommandHelper
{
    //******************************************************************************
    //* Constant
    //******************************************************************************

    /**
     * @type string Default date() format
     */
    const DEFAULT_TIMESTAMP_FORMAT = 'c';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Generates a timestamp in a consistent format.
     *
     * @param string $format Valid date() format to override configured or default of 'Y-m-d H:i:s' will be used
     *
     * @return bool|string
     */
    public static function timestamp( $format = null )
    {
        $_format = $format ?: static::DEFAULT_TIMESTAMP_FORMAT;

        return date( $_format );
    }

    /**
     * Wraps a string in a console tag (i.e. <comment>, <info>, etc.)
     *
     * @param string $tag
     * @param string $content
     * @param bool   $quoted If true, quotes will be prefixed and suffixed.
     *
     * @return string
     */
    public static function wrap( $tag, $content, $quoted = true )
    {
        return ( $quoted ? '"' : null ) . '<' . $tag . '>' . $content . '</' . $tag . '>' . ( $quoted ? '"' : null );
    }
}