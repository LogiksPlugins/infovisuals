<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$chartID = md5(rand().time());

if(isset($cardConfig['content']) && $cardConfig['content']) {
	if(is_array($cardConfig['content'])) {
		echo "<div class='card-images nopadding' style='height:{$cardConfig['height']};overflow: auto;'>";

		echo "<div id='myCarousel{$chartID}' class='carousel slide' data-ride='carousel'>";

		echo "<ol class='carousel-indicators'>";
		foreach ($cardConfig['content'] as $kn => $imgValue) {
			if($kn==0) {
				echo "<li data-target='#myCarousel{$chartID}' data-slide-to='{$kn}' class='active'></li>";
			} else {
				echo "<li data-target='#myCarousel{$chartID}' data-slide-to='{$kn}'></li>";
			}
		}
		echo "</ol>";

		echo "<div class='carousel-inner'>";
		foreach ($cardConfig['content'] as $kn => $imgValue) {
			if($kn==0) {
				echo "<div class='item active'><img src='{$imgValue}' alt='Image {$kn}'></div>";
			} else {
				echo "<div class='item'><img src='{$imgValue}' alt='Image {$kn}'></div>";
			}
		}
		echo "</div>";

		echo "<a class='left carousel-control' href='#myCarousel{$chartID}' data-slide='prev'><span class='glyphicon glyphicon-chevron-left'></span><span class='sr-only'>Previous</span></a>";
		echo "<a class='right carousel-control' href='#myCarousel{$chartID}' data-slide='next'><span class='glyphicon glyphicon-chevron-right'></span><span class='sr-only'>Next</span></a>";

		echo "</div>";

		echo "</div>";
	} else {
		echo "<div class='card-images nopadding' style='height:{$cardConfig['height']}'>";
		echo "<image class='card-images-box' width='100%' height='100%' src='{$cardConfig['content']}' />";
		echo "</div>";
	}
} elseif(isset($cardConfig['source'])) {
	$recordSet = fetchCardData($cardConfig,$dbKey);

	$imageList = [];
	foreach ($recordSet as $kn => $tempData) {
		foreach ($tempData as $key => $record) {
			if(isset($record['url'])) {
				$imageList[] = $record['url'];
			}
		}
	}

	echo "<div class='card-images' style='height:{$cardConfig['height']}'>";

	echo "<div class='card-images nopadding' style='height:{$cardConfig['height']};overflow: auto;'>";

	echo "<div id='myCarousel{$chartID}' class='carousel slide' data-ride='carousel'>";

	echo "<ol class='carousel-indicators'>";
	foreach ($imageList as $kn => $imgValue) {
		if($kn==0) {
			echo "<li data-target='#myCarousel{$chartID}' data-slide-to='{$kn}' class='active'></li>";
		} else {
			echo "<li data-target='#myCarousel{$chartID}' data-slide-to='{$kn}'></li>";
		}
	}
	echo "</ol>";

	echo "<div class='carousel-inner'>";
	foreach ($imageList as $kn => $imgValue) {
		if($kn==0) {
			echo "<div class='item active'><img src='{$imgValue}' alt='Image {$kn}'></div>";
		} else {
			echo "<div class='item'><img src='{$imgValue}' alt='Image {$kn}'></div>";
		}
	}
	echo "</div>";

	echo "<a class='left carousel-control' href='#myCarousel{$chartID}' data-slide='prev'><span class='glyphicon glyphicon-chevron-left'></span><span class='sr-only'>Previous</span></a>";
	echo "<a class='right carousel-control' href='#myCarousel{$chartID}' data-slide='next'><span class='glyphicon glyphicon-chevron-right'></span><span class='sr-only'>Next</span></a>";

	echo "</div>";

	echo "</div>";

	echo "</div>";
}
?>