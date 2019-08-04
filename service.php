<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["action"])) {
	$_REQUEST["action"]="";
}
if(!isset($_REQUEST['infovisualid'])) {
	trigger_error("Infovisual Not Found");
}

include_once __DIR__."/api.php";

switch($_REQUEST["action"]) {
	case "fetchCard":
		$infovisualKey=$_REQUEST['infovisualid'];
		if(!isset($_SESSION['INFOVISUAL'][$infovisualKey])) {
			trigger_error("Sorry, Infovisual key not found.");
		}


		
	break;
}
?>