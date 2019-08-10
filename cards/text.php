<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($cardConfig['content'])) {
	echo "<div class='card-content' style='height:{$cardConfig['height']}'>";
	echo $cardConfig['content'];
	echo "</div>";
} elseif(isset($cardConfig['source'])) {
	$recordSet = fetchCardData($cardConfig,$dbKey);

	echo "<div class='card-content' style='height:{$cardConfig['height']}'>";
	foreach ($recordSet as $kn => $tempData) {
		foreach ($tempData as $key => $record) {
			if(isset($record['text'])) {
				echo "<div class='card-content-box'>";
				echo $record['text'];
				echo "</div>";
			}
		}
	}
	echo "</div>";
}
?>