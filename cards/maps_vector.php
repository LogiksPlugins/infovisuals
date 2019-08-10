<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$chartData = [];
$finalData = [];
$labels = [];

$recordSet = fetchCardData($cardConfig,$dbKey);

if(checkVendor("jqvmap")) {
	loadVendor("jqvmap");
} else {
?>
<!-- CSS -->
<link href="https://germini.info/js/jqvmap/jqvmap/jqvmap.css" media="screen" rel="stylesheet" type="text/css" />


<!-- JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="https://germini.info/js/jqvmap/jqvmap/jquery.vmap.js" type="text/javascript"></script>
<script src="https://germini.info/js/jqvmap/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>
<script src="https://germini.info/js/jqvmap/jqvmap/jquery.vmap.continents.js" type="text/javascript"></script>
<?php
}

$chartID = md5(rand().time());
?>

<div id="map-holder" style="width: 100%; height: 300px;">
	<div id="vmap"></div>
</div>

<style>
#vmap {
	width: 100%;
	height: 100%;
}
</style>
<script>
$(function() {
	clock1 = setInterval(function() {
		if(typeof $.fn.vectorMap == "function") {
			clearInterval(clock1);
			//countryMap
			$('#vmap').vectorMap({
			    map: 'world_en',
			    backgroundColor: '#a5bfdd',
			    borderColor: '#818181',
			    borderOpacity: 0.25,
			    borderWidth: 1,
			    color: '#f4f3f0',
			    enableZoom: true,
			    hoverColor: '#c9dfaf',
			    hoverOpacity: null,
			    normalizeFunction: 'linear',
			    scaleColors: ['#b6d6ff', '#005ace'],
			    selectedColor: '#c9dfaf',
			    selectedRegions: null,
			    showTooltip: true,
			    onRegionOver : function (element, code, region) {
					
				},
				onRegionOut : function (element, code, region) {
					
				},
			    onRegionClick: function(element, code, region) {
			        // var message = 'You clicked "'
			        //     + region
			        //     + '" which has the code: '
			        //     + code.toUpperCase();
					// console.log(region);
			        // alert(message);
			    }
			});
		}
	},500);
});
</script>