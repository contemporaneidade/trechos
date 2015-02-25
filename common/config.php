<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

ini_set('display_errors','On');
error_reporting(E_ALL ^ E_NOTICE);

ini_set("memory_limit", "-1");

session_start();

/**************************/
/* Parâmetros do sistema */
/*************************/
$path_entrada = 'entrada/';
$path_saida = 'saida/';
$path_temp = 'temp/';
$apagar_arq_temp = true;

/**************************/
/* Parâmetros do Excel */
/*************************/
$creator = 'GEMAA';
$lastModifiedBy = 'GEMAA';
$title = 'Trechos';
$subject = 'Trechos';
$description = 'Trechos';
$keywords = 'Trechos';
$category = 'Trechos';
/*************************/

?>