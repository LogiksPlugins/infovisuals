<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($cardConfig['content']) && strlen($cardConfig['content'])>0) {
	echo "<div class='card-frame nopadding' style='height:{$cardConfig['height']}'>";
	echo "<iframe class='card-frame-box' frameborder=0 width='100%' height='100%' src='{$cardConfig['content']}'></iframe>";
	echo "</div>";
} elseif(isset($cardConfig['url']) && strlen($cardConfig['url'])>0) {
	echo "<div class='card-frame nopadding' style='height:{$cardConfig['height']}'>";
	echo "<iframe class='card-frame-box' frameborder=0 width='100%' height='100%' src='{$cardConfig['url']}'></iframe>";
	echo "</div>";
} elseif(isset($cardConfig['source'])) {
	$recordSet = fetchCardData($cardConfig,$dbKey);

	echo "<div class='card-frame' style='height:{$cardConfig['height']}'>";
	foreach ($recordSet as $kn => $tempData) {
		foreach ($tempData as $key => $record) {
			if(isset($record['url'])) {
				echo "<iframe class='card-frame-box' frameborder=0 width='100%' height='100%' src='{$record['url']}'></iframe>";
			}
		}
	}
	echo "</div>";
}
?>