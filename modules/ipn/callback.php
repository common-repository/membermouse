<?php
require_once("../../../../../wp-load.php");
require_once("../../includes/mm-constants.php");
require_once("../../includes/init.php");

foreach($_REQUEST as $k=>$v){
	LogMe::write("Clickbank Response: ".$k." : ".$v);	
}

if(isset($_REQUEST["verify_sign"])){
	$paypal = new MM_PayPalService();
	$paypal->handleCallback($_REQUEST);
	if(isset($_REQUEST["show_response"])){
		echo json_encode(array("response"=>"Paypal invoked"));
		exit;
	}
}
else if(isset($_REQUEST["cverify"])){
	$clickBank = new MM_ClickBankService();
	$clickBank->handleCallback($_REQUEST);
	if(isset($_REQUEST["show_response"])){
		echo json_encode(array("response"=>"Click Bank invoked"));
		exit;
	}
}

if(isset($_REQUEST["show_response"])){
	echo json_encode(array("response"=>"No IPN has been invoked"));
	exit;
}

