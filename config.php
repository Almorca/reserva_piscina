<?php

define("MAX_PERSONAS_RESERVA", 4); /** Máximo número de personas en una reserva */

$server_name = $_SERVER['SERVER_NAME'];

$globals['db_server'] = '127.0.0.1';
$globals['db_name'] = 'test'; 
$globals['db_user'] = 'root';
$globals['db_password'] = '';

// language site
$globals['language'] = 'es';

// Specify you base directory without "/"
$globals['base_directory'] = 'D:\00 Programas\xampp\htdocs';
// Specify you base URL, "/" if is the root document
$globals['base_url'] = 'reserva';


// Don't touch behind this
$globals['mysql_persistent'] = true;
$globals['mysql_master_persistent'] = false;

mb_internal_encoding("UTF-8");

include_once 'ez_sql_loader.php';

$db = new ezSQL_mysqli($globals['db_user'], $globals['db_password'], $globals['db_name'], $globals['db_server']);

$db->connect($globals['db_user'], $globals['db_password'], $globals['db_server']);

session_start();


/** Funciones comunes */
/** Devuelve el nombre de una franja */
function Franja_toString ($franja) {
	if ($franja == 1) {
		return 'Mañana';
	} else {
		return 'Tarde';
	}
}

#$db->debug_all = true;

?>
