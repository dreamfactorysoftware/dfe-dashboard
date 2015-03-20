<?php
/**
 * database.dfdsp001.fabric.config.php
 * This file contains the database configurations for the dfdsp001.fabric master hosted database
 *
 * @link   http://www.dreamfactory.com DreamFactory Software, Inc.
 * @author Jerry Ablan <jerryablan@dreamfactory.com>
 * @filesource
 */

return array(
	'class'              => 'CDbConnection',
	'autoConnect'        => true,
	'connectionString'   => 'mysql:host=cumulus.fabric.dreamfactory.com;port=3306;',
	'username'           => 'cerberus',
	'password'           => 'KlL8ZF-E-rBFw_h9ygQZh3ZF',
	'emulatePrepare'     => true,
	'charset'            => 'utf8',
	'enableParamLogging' => true,
);
