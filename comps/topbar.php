<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<div class="control-primebar">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 control-custombar">
		<?php
			if(isset($infovisualkey['custombar']) && $infovisualkey['custombar'] && file_exists(APPROOT.$infovisualkey['custombar'])) {
              	include_once APPROOT.$infovisualkey['custombar'];
            }
		?>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 control-toolbar">
		<button class="btn btn-default pull-right action" data-cmd='printPage'>
			<i class="fa fa-print"></i> Print
		</button>
	</div>
</div>