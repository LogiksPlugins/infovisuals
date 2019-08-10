<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$recordSet = fetchCardData($cardConfig,$dbKey);

if(!$recordSet) {
	return "";
}

//printArray($recordSet);

foreach ($recordSet as $kn => $tempData) {
	if(!isset($source['footer'])) $source['footer'] = [];
	if(!isset($source['hidden'])) $source['hidden'] = [];

	$source = $cardConfig['source'][$kn];

	if(isset($tempData[0])) {
		$columns = array_keys($tempData[0]);
	} else {
		$columns = false;
	}

	echo "<div class='table-responsive' style='height:{$cardConfig['height']}'><table class='table table-striped table-hover'>";
	
	if(isset($source['title'])) {
		echo "<caption>{$source['title']}</caption>";
	}

	if($columns) {
		echo "<thead><tr>";
		foreach ($columns as $key) {
			if(in_array($key, $source['hidden'])) {
				continue;
			}
			echo "<th class='{$key}'>"._ling(toTitle($key))."</th>";
		}
		if(isset($source['buttons']) && count($source['buttons'])>0) {
			echo "<th class='actionCol hidden-print'></th>";
		}
		echo "</tr></thead>";
	} else {
		$source['footer'] = false;
	}
	
	echo "<tbody>";

	$calcFooter = [];
	foreach ($tempData as $record) {
		if(isset($record['id'])) {
			echo "<tr data-hash='".md5($record['id'])."' class='tableRow'>";
		} else {
			echo "<tr data-hash='' class='tableRow'>";
		}
		foreach ($record as $key => $value) {
			if(is_array($source['footer']) && isset($source['footer'][$key])) {
				if(!isset($calcFooter[$key])) $calcFooter[$key] = [];
				$calcFooter[$key][] = $value;
			}
			if(in_array($key, $source['hidden'])) {

			} else {
				echo "<td class='{$key}'>{$value}</td>";
			}
		}

		if(isset($source['buttons'])) {
			echo "<td class='actionCol hidden-print'>";
			if(isset($record['id'])) {
				foreach ($source['buttons'] as $cmd => $button) {
					$button['cmd']=$cmd;
					echo createInfovisualRecordAction($button, $record);
				}
			}
			echo "</td>";
		}

		echo "</tr>";
	}

	echo "</tbody>";
	
	if(isset($source['footer'])) {
		if(is_array($source['footer'])) {
			echo "<tfoot class='calculations'><tr>";
			foreach ($tempData[0] as $key => $value) {
				if(in_array($key, $source['hidden'])) {
					continue;
				}

				if(isset($calcFooter[$key]) && $source['footer'][$key]) {
					$total = 0;
					$calcType = strtolower($source['footer'][$key]);
					
					echo "<th class='{$key}'>";
					if(strpos($calcType, ":")>0) {
						switch ($calcType) {
							case 'math:sum':
								foreach ($calcFooter[$key] as $key => $value) {
									$total+=$value;
								}
								break;
							case 'math:avg':
								foreach ($calcFooter[$key] as $key => $value) {
									$total+=$value;
								}
								$total = round($total/count($calcFooter[$key]),2);
								break;
							case 'math:count':
								$total = count($calcFooter[$key]);
								break;
						}
					} else {
						$total = $source['footer'][$key];
					}
					
					echo "{$total}</th>";
				} else {
					echo "<td></td>";
				}
			}
			if(isset($source['buttons']) && count($source['buttons'])>0) {
				echo "<th class='actionCol hidden-print'></th>";
			}
			echo "</tr></tfoot>";
		} elseif(file_exists(APPROOT.$source['footer'])) {
			echo "<tfoot class='calculations'>";
			include_once APPROOT.$source['footer'];
			echo "</tfoot>";
		}
	}

	echo "</table></div>";
}
?>
