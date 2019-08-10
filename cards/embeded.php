<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($cardConfig['content']) && strlen($cardConfig['content'])>0) {
	echo "<div class='card-embeded nopadding' style='height:{$cardConfig['height']}'>";
	echo $cardConfig['content'];
	echo "</div>";
} elseif(isset($cardConfig['source'])) {
	$recordSet = fetchCardData($cardConfig,$dbKey);

	echo "<div class='card-embeded' style='height:{$cardConfig['height']}'>";
	foreach ($recordSet as $kn => $tempData) {
		foreach ($tempData as $key => $record) {
			if(isset($record['content'])) {
				echo $record['content'];
			} elseif(isset($record['code'])) {
				echo $record['code'];
			}
		}
	}
	echo "</div>";
}
?>