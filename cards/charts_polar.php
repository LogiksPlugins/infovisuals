<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$chartData = [];
$finalData = [];
$labels = [];

$recordSet = fetchCardData($cardConfig,$dbKey);

if(!$recordSet) {
	return "";
}

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

$chartID = md5(rand().time());
?>
<div id='reportChart-<?=$chartID?>' class='col-xs-12 col-sm-12 col-md-12 col-lg-12 chartArea nopadding' style='height:<?=$cardConfig['height']?>;padding: 10px;'>
	<canvas id="canvas-<?=$chartID?>" class='reportChartCanvas' style="width: 100%;height: 100%;"></canvas>
</div>
<script>
$(function() {
	jsonData = <?=json_encode($chartData)?>;

	config = {
		type: "polararea",
		
		data: {
			labels: [],
			datasets: []
		},

		options: $.extend({
			responsive: true,
			legend: {
				position: 'right',
			},
			scale: {
				ticks: {
					beginAtZero: true
				},
				reverse: false
			},
			animation: {
				animateRotate: false,
				animateScale: true
			}
		}, jsonData.options)
	};

	config.data.labels = jsonData.labels;
	config.data.datasets = jsonData.datasets;

	var ctx = document.getElementById('canvas-<?=$chartID?>');
	Chart.PolarArea(ctx, config);
});
</script>