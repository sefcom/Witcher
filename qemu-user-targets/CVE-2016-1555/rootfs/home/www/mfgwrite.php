<?php
	@include('sessionCheck.inc');
	exec("wr_mfg_data -p ".$_REQUEST['product']);								//This will work on Board...
	echo $_REQUEST['product'];
?>