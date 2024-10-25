<?php
define("TESTMODUS",true);

define("DB",[
	"host" => "localhost",
	"user" => "root",
	"pwd" => "",
	"name" => "db_wandertaxi"
]);

if(TESTMODUS) {
	error_reporting(E_ALL);
	ini_set("display_errors",1);
}
else {
	error_reporting(0); //es werden keine Fehler in einer Log-Datei gespeichert
	ini_set("display_errors",0); //eventuell auftretende Fehler werden nicht am Bildschirm des Users dargestellt
}
?>