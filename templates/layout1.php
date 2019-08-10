<?php
if(!defined('ROOT')) exit('No direct script access allowed');


?>
<div id='INFOVISUALS-<?=$infovisualkey?>' data-rptkey='<?=$infovisualkey?>' class='col-xs-12 col-sm-12 col-md-12 col-lg-12 infovisuals-main-container'>

    <div class="row infovisuals-tools noprint">
      <?php
			 include_once dirname(__DIR__)."/comps/topbar.php";
		  ?>
    </div>

    <div class='cardsContainer infovisualsContainer'>
      <div class='cardsBoard infovisualsBoard'>
      	<?php
      		foreach ($infovisualConfig['cards'] as $cardKey => $cardConfig) {
      			echo printCardBox($infovisualkey, $cardKey, $cardConfig, $infovisualConfig['params']);
      		}
      	?>
      </div>
    </div>

    <div class="row infovisuals-footer noprint">
      	<?php
			include_once dirname(__DIR__)."/comps/bottombar.php";
		?>
    </div>
</div>
<script>
$(function() {
	$(".infovisualsContainer .infovisualBox[data-ivcardkey]","#INFOVISUALS-<?=$infovisualkey?>").each(function() {
		$(this).find(".panel-body").html("<div class='ajaxloading ajaxloading8'></div>");
		$(this).find(".panel-body").load(_service("infovisuals","fetchCard","html")+"&infovisualid="+$(this).data("ivkey")+"&ivcardkey="+$(this).data("ivcardkey"));
	});
});
</script>