<?php
/**
 * database.fabric_auth.config.php
 * This file contains the database configurations for the fabric_auth database
 *
 * @link   http://www.dreamfactory.com DreamFactory Software, Inc.
 * @author Jerry Ablan <jerryablan@dreamfactory.com>
 * @filesource
 */

$_host = 'cerberus.fabric.dreamfactory.com';

return array(
	'class'              => 'CDbConnection',
	'autoConnect'        => true,
	'connectionString'   => 'mysql:host=' . $_host . ';dbname=fabric_auth;port=3306;',
	'username'           => 'auth_user',
	'password'           => 'yu-qZQGie_JAzqT0VkU7qt8C',
	'emulatePrepare'     => true,
	'charset'            => 'utf8',
//	'schemaCachingDuration' => 3600,
	'enableParamLogging' => true,
);
