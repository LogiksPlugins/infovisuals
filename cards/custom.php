<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($cardConfig['source'])) {
	if(!is_array($cardConfig['source'])) {
		$cardConfig['source'] = [$cardConfig['source']];
	}

	foreach ($cardConfig['source'] as $fpath) {
		$ext = explode(".", $fpath);
		$ext = strtolower(end($ext));

		switch ($ext) {
			case 'php':
				if(file_exists(APPROOT.$fpath)) {
					include APPROOT.$fpath;
				}		
				break;
			case 'html':
				if(file_exists(APPROOT.$fpath)) {
					readfile(APPROOT.$fpath);
				}
				break;
			case "js":
				if(file_exists(APPROOT.$fpath)) {
					echo "<script language='javascript'>";
					readfile(APPROOT.$fpath);
					echo "</script>";
				}
				break;
			case "css":
				if(file_exists(APPROOT.$fpath)) {
					echo "<style>";
					readfile(APPROOT.$fpath);
					echo "</style>";
				}
				break;
		}
		
	}
}
?>