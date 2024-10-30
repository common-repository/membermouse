<?php
require_once("../../../../../wp-load.php");
require_once("../../includes/mm-constants.php");
require_once("../../includes/init.php");

$paypal = new MM_PayPalService();
$paypal->handleCallback($_REQUEST);


