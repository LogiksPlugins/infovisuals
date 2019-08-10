<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$chartData = [];
$finalData = [];
$labels = [];

$recordSet = fetchCardData($cardConfig,$dbKey);

if($recordSet) {
	foreach ($recordSet as $kn => $tempData) {
		$source = $cardConfig['source'][$kn];

		if(!isset($source['fill'])) $source['fill'] = false;

		if(!isset($source['title'])) {
			$source['title'] = "Dataset {$kn}";
			if(!is_numeric($kn)) $source['title'] = $kn;
		}

		$finalData[$kn] = [
					"data"=>[],
					"fill"=>$source['fill'],
					"label"=>$source['title'],
					// "type" => 'bar',
					"backgroundColor"=>$colorArr[$colorKeys[$kn]],
					"borderColor"=>$colorArr[$colorKeys[$kn]],
				];
		if(isset($source['charttype']) && strlen($source['charttype'])>0) {
			$finalData[$kn]['type'] = $source['charttype'];
		}

		foreach ($tempData as $record) {
			if(isset($record['title']) && isset($record['value'])) {
				$labels[] = $record['title'];
				$finalData[$kn]['data'][] = $record['value'];
			}
		}
	}

	foreach ($finalData as $kn => $chartData) {
		$colorList = [];
		foreach ($labels as $kn1=>$lbl) {
			$clrIndex = ($kn+$kn1)%count($colorKeys);
			$colorList[] = $colorArr[$colorKeys[$clrIndex]];
		}

		$finalData[$kn]['backgroundColor'] = $colorList;
		$finalData[$kn]['borderColor'] = $colorList;
	}
	
	if(isset($cardConfig['title']) && strlen($cardConfig['title'])>0) {
		$cardConfig['options']['title'] = [
				"display"=>true,
				"text"=>$cardConfig['title']
			];
	}

	if(!isset($cardConfig['options']['scales'])) {
		$cardConfig['options']['scales'] = [];

		if(isset($cardConfig['x-axis-text']) && strlen($cardConfig['x-axis-text'])>0) {
			$cardConfig['options']['scales']['xAxes'] = [
				[
					"display"=>true,
					"scaleLabel"=> [
						"display"=> true,
						"labelString"=> $cardConfig['x-axis-text']
					]
				]
			];
		}

		if(isset($cardConfig['y-axis-text']) && strlen($cardConfig['y-axis-text'])>0) {
			$cardConfig['options']['scales']['yAxes'] = [
				[
					"display"=>true,
					"scaleLabel"=> [
						"display"=> true,
						"labelString"=> $cardConfig['y-axis-text']
					]
				]
			];
		}

		if(count($cardConfig['options']['scales'])<=0) {
			unset($cardConfig['options']['scales']);
		}
	}
	

	if(!isset($cardConfig['options']['legend']) && isset($cardConfig['source'])) {
		if(count($cardConfig['source'])>1) {
			$cardConfig['options']['legend'] = [
					"display"=>true,
					"position"=>"right"
				];
		}
	}

	$chartData = [
				"type"=>"pie",
				"options"=>$cardConfig['options'],
				"labels"=>array_unique($labels),
				"datasets"=>$finalData
			];
	//printArray($chartData);
} else {
	return "";
}

$chartID = md5(rand().time());
if(!isset($cardConfig['height'])) $cardConfig['height'] = "300px";
?>
<div id='reportChart-<?=$chartID?>' class='col-xs-12 col-sm-12 col-md-12 col-lg-12 chartArea nopadding' style='height:<?=$cardConfig['height']?>'>
	<canvas id="canvas-<?=$chartID?>" class='reportChartCanvas' style="width: 100%;height: 100%;"></canvas>
</div>
<script>
$(function() {
	jsonData = <?=json_encode($chartData)?>;
	config = {
		type: "pie",

		data: {
			labels: [],
			datasets: []
		},

		options: $.extend({
			responsive: true,
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest',
				intersect: true
			},
			legend: {
				display: true,
				position: "right"
			}
		}, jsonData.options)
	};

	config.data.labels = jsonData.labels;
	config.data.datasets = jsonData.datasets;
	
	// console.log(config,jsonData);
	ctx = document.getElementById("canvas-<?=$chartID?>").getContext('2d');
	new Chart(ctx, config);
});
</script>