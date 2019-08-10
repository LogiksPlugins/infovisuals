<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$clz = "";$icon = "";
if(isset($cardConfig['bodyclass'])) $clz = $cardConfig['bodyclass'];
if(isset($cardConfig['iconclass'])) $icon = "<i class='{$cardConfig['iconclass']}'></i>";

//{$cardConfig['height']}
if(isset($cardConfig['content'])) {
	echo "<div class='card-content card-number' style='height:auto'>";
	echo "<div class='well text-center {$clz}'>{$icon} {$cardConfig['content']}</div>";
	echo "</div>";
} elseif(isset($cardConfig['source'])) {
	$recordSet = fetchCardData($cardConfig,$dbKey);

	echo "<div class='card-content card-number' style='height:auto'>";
	foreach ($recordSet as $kn => $tempData) {
		$source = $cardConfig['source'][$kn];

		foreach ($tempData as $key => $record) {
			if(isset($record['value'])) {
				$title = "";
				if(isset($record['title'])) $title = "<p>{$record['title']}</p>";

				echo "<div class='well text-center {$clz}'>{$icon} <h2>{$record['value']}</h2> {$title}</div>";
			}
		}
	}
	echo "</div>";
}
?>
