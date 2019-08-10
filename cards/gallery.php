<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$chartID = md5(rand().time());

if(isset($cardConfig['content']) && $cardConfig['content']) {
	if(is_array($cardConfig['content'])) {
		$imageList = $cardConfig['content'];


		echo "<div class='card-images nopadding' style='height:{$cardConfig['height']};overflow: auto;'>";
		foreach ($imageList as $kn => $imgURL) {
			echo "<div class='col-md-4 nopadding'>
				    <div class='thumbnail'>
				      <img src='{$imgURL}' alt='Image {$kn}'>
				    </div>
				  </div>";
		}
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

	echo "<div class='card-images nopadding' style='height:{$cardConfig['height']};overflow: auto;'>";
	foreach ($imageList as $kn => $imgURL) {
		echo "<div class='col-md-4 nopadding'>
				    <div class='thumbnail'>
				      <img src='{$imgURL}' alt='Image {$kn}'>
				    </div>
				  </div>";
	}
	echo "</div>";
}
?>
