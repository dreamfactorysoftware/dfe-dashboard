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
	'connectionString'   => 'mysql:host=' . $_host . ';dbname=fabric_deploy;port=3306;',
	'username'           => 'deploy_user',
	'password'           => '3hgc9nKuhh658_MJ-_D-PDqpkVEyta',
	'emulatePrepare'     => true,
	'charset'            => 'utf8',
//	'schemaCachingDuration' => 3600,
	'enableParamLogging' => true,
);
