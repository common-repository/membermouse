<?php
require_once("../../../../../wp-load.php");
require_once("../../includes/mm-constants.php");
require_once("../../includes/init.php");

foreach($_REQUEST as $k=>$v){
	MM_LogMe::write("Clickbank Response: ".$k." : ".$v);	
}
