<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$recordSet = fetchCardData($cardConfig,$dbKey);

if(!$recordSet) {
	return "";
}

//printArray($recordSet);

foreach ($recordSet as $kn => $tempData) {
	$source = $cardConfig['source'][$kn];

	echo "<div class='list-group card-list-group' style='height:{$cardConfig['height']}'>";

	foreach ($tempData as $record) {
		if(isset($record['id'])) {
			echo "<li class='list-group-item tableRow' data-hash='".md5($record['id'])."'>";
		} else {
			echo "<li data-hash='' class='tableRow'>";
		}

		if(isset($source['template']) && strlen($source['template'])>0) {
			$templateData = $source['template'];
			foreach ($record as $col=>$val) {
				$templateData = str_replace("{".$col."}", $val, $templateData);
			}
			echo _replace($templateData);
		} else {
			if(isset($record['value'])) {
				echo "<span class='badge'>{$record['value']}</span>";
			} elseif(isset($record['badge'])) {
				echo "<span class='badge'>{$record['badge']}</span>";
			}

			if(isset($record['title'])) {
				echo "<h5 class='list-group-item-heading'>{$record['title']}</h5>";
			}

			if(isset($record['content'])) {
				echo "<p class='list-group-item-text'>{$record['content']}</p>";
			} elseif(isset($record['text'])) {
				echo "<p class='list-group-item-text'>{$record['text']}</p>";
			} elseif(isset($record['body'])) {
				echo "<p class='list-group-item-text'>{$record['body']}</p>";
			}

			if(isset($record['tags'])) {
				$record['tags'] = explode(",", $record['tags']);

				echo "<div class='tags'>";
				foreach ($record['tags'] as $tag) {
					echo "<span class='label label-info'>".trim($tag)."</span>";
				}
				echo "</div>";
			} elseif(isset($record['category'])) {
				$record['category'] = explode(",", $record['category']);
				echo "<div class='tags'>";
				foreach ($record['category'] as $tag) {
					echo "<span class='label label-primary'>".trim($tag)."</span>";
				}
				echo "</div>";
			}

			if(isset($source['buttons'])) {
				echo "<div class='actionCol list-actionCol hidden-print'>";
				if(isset($record['id'])) {
					foreach ($source['buttons'] as $cmd => $button) {
						$button['cmd']=$cmd;
						echo createInfovisualRecordAction($button, $record);
					}
				}
				echo "</div>";
			}	
		}
		// printArray($record);

		echo "</li>";
	}


	echo "</div>";
}
?>
