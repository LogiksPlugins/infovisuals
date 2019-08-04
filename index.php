<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/api.php";

$slug=_slug("?/src/tmpl");

$template="grid";

if(isset($slug['src']) && !isset($_REQUEST['src'])) {
	$_REQUEST['src']=$slug['src'];
}

if(isset($_REQUEST['src']) && strlen($_REQUEST['src'])>0) {
	$report=findInfovisual($_REQUEST['src']);

	if($report) {
		$report['template']=$template;
		
// 		$report['template']="kanban";
		
		echo _css("infovisuals");
		echo "<div class='reportholder'>";//style='width:100%;height:100%;overflow-x: hidden;'
		//include_once "sample.php";
		printReport($report,$report['dbkey']);
		echo "</div>";
		echo _js(["filesaver","html2canvas","jquery.cookie","infovisuals"]);
	} else {
// 		trigger_logikserror("Sorry, infovisual '{$_REQUEST['src']}' not found.",E_USER_NOTICE,404);
		echo "<h1 class='errormsg'>Sorry, infovisual '{$_REQUEST['src']}' not found.</h1>";
	}
} else {
	//trigger_logikserror("Sorry, infovisual not defined.");
	echo "<h1 class='errormsg'>Sorry, infovisual not defined.</h1>";
}
?>
