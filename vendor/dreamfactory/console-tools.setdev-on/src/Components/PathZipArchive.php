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

use DreamFactory\Library\Utility\Exceptions\FileSystemException;

/**
 * Manipulates a zip of a path
 */
class PathZipArchive extends \ZipArchive
{
    //*************************************************************************
    //	Members
    //*************************************************************************

    /**
     * @var string The name of the zip file
     */
    protected $_zipFileName;

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /**
     * @param string $zipFileName The name of the zip file
     */
    public function __construct( $zipFileName )
    {
        $this->_zipFileName = $zipFileName;
    }

    /**
     * @param string $sourcePath
     * @param string $localName
     * @param bool   $checksum If true, an MD5 checksum of the file will be returned
     *
     * @return string|null If $checksum is true, the MD5 checksum of the created zip is returned, otherwise null.
     */
    public function backup( $sourcePath, $localName = null, $checksum = false )
    {
        $_path = $this->_validatePath( $sourcePath );

        //  Make a temp file name...
        $_zipName = $this->_buildZipFile( $_path, $localName );

        if ( false === @rename( $_zipName, $this->_zipFileName ) || !file_exists( $this->_zipFileName ) )
        {
            throw new FileSystemException( 'Error move zip file "' . $_zipName . '" to "' . $this->_zipFileName . '"' );
        }

        return $checksum ? md5_file( $this->_zipFileName ) : null;
    }

    /**
     * Restores a zip file to the specified path
     *
     * @param string $targetPath The path in which to unzip the archive
     * @param string $localName  The local name of the directory
     * @param string $checksum   An MD5 checksum to verify the backup file as valid
     *
     * @throws FileSystemException
     * @throws \Exception
     * @return bool Only returns false if no backup exists, otherwise TRUE
     */
    public function restore( $targetPath, $localName = null, $checksum = null )
    {
        $_path = $this->_validatePath( $targetPath, true );

        //  Checksum
        if ( $checksum && $checksum != ( $_md5 = md5_file( $this->_zipFileName ) ) )
        {
            throw new \InvalidArgumentException( 'The provided checksum does not match the file checksum of "' . $_md5 . '".' );
        }

        if ( true !== ( $_result = $this->open( $this->_zipFileName ) ) )
        {
            throw new FileSystemException( 'Unable to open zip file "' . $this->_zipFileName . '". Error code: ' . $_result );
        }

        $this->extractTo( $_path );
        $this->close();

        return true;
    }

    /**
     * Recursively add a path to myself
     *
     * @param string $path
     * @param string $localName
     * @param array  $excludedDirs Array of directories to exclude, relative to source path. "." and ".." are automatically excluded.
     *
     * @return bool
     */
    protected function _addPath( $path, $localName = null, array $excludedDirs = array() )
    {
        $_excludedDirs = array_merge(
            $excludedDirs,
            array(
                '.',
                '..',
            )
        );

        $_dd = \opendir( $path );

        while ( false !== ( $_file = \readdir( $_dd ) ) )
        {
            if ( in_array( $_file, $_excludedDirs ) )
            {
                continue;
            }

            $_filePath = $path . DIRECTORY_SEPARATOR . $_file;
            $_localFilePath = ( $localName ? $localName . DIRECTORY_SEPARATOR . $_file : null );

            if ( is_file( $_filePath ) )
            {
                $this->addFile( $_filePath, $_localFilePath );
            }
            else if ( is_dir( $_filePath ) )
            {
                $this->addEmptyDir( $_file );
                $this->_addPath( $_filePath, $_localFilePath, $excludedDirs );
            }
        }

        \closedir( $_dd );

        return true;
    }

    /**
     * @param string $path
     * @param bool   $restoring If true, the directory will be created if it does not exist
     *
     * @return string
     */
    protected function _validatePath( $path, $restoring = false )
    {
        if ( empty( $path ) )
        {
            throw new \InvalidArgumentException( 'Invalid path specified.' );
        }

        if ( !is_dir( $path ) )
        {
            //  Try and make the directory if wanted
            if ( !$restoring || false === ( $_result = mkdir( $path, 0777, true ) ) )
            {
                throw new \InvalidArgumentException(
                    'The path "' . $path . '" does not exist and/or cannot be created. Please validate installation.'
                );
            }
        }

        //  Make sure we can read/write there...
        if ( !is_readable( $path ) || !is_writable( $path ) )
        {
            throw new \InvalidArgumentException(
                'The path "' . $path . '" exists but cannot be accessed. Please validate installation.'
            );
        }

        return rtrim( $path, '/' );
    }

    /**
     * Given a path, build a zip file and return the name
     *
     * @param string $path      The path to zip up
     * @param string $localName The local name of the path
     * @param string $data      If provided, write to zip file instead of building from path
     *
     * @return string
     */
    protected function _buildZipFile( $path, $localName = null, $data = null )
    {
        $_zipName = tempnam( sys_get_temp_dir(), sha1( uniqid() ) ) . '.zip';

        if ( !$this->open( $_zipName, static::CREATE ) )
        {
            throw new \RuntimeException( 'Unable to create temporary zip file.' );
        }

        //  Restore prior zipped content?
        if ( null !== $data )
        {
            if ( false === ( $_bytes = file_put_contents( $_zipName, $data ) ) )
            {
                $this->close();
                @\unlink( $_zipName );

                throw new \RuntimeException( 'Error creating temporary zip file for restoration.' );
            }

            return $_zipName;
        }

        //  Build from $path
        if ( $localName )
        {
            $this->addEmptyDir( $localName );
        }

        $this->_addPath( $path, $localName );
        $this->close();

        return $_zipName;
    }
}
