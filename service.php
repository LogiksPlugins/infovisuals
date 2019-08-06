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
		if(!isset($_SESSION['INFOVISUAL'][$infovisualKey]) || !isset($_REQUEST['ivcardkey'])) {
			trigger_error("Sorry, Infovisual key or IVCardKey not found.");
		}
		$infovisualConfig=$_SESSION['INFOVISUAL'][$infovisualKey];

		$ivcardkey=$_REQUEST['ivcardkey'];
		if(!isset($infovisualConfig['cards'][$ivcardkey])) {
			echo "";
			return;
		}

		$cardKey = $ivcardkey;
		$cardConfig = $infovisualConfig['cards'][$ivcardkey];
		
		$dbKey = $infovisualConfig['dbkey'];
		if(isset($cardConfig['dbkey'])) $dbKey = $cardConfig['dbkey'];

		printInfovisualCard($cardKey, $cardConfig, $dbKey);
	break;
}
?>