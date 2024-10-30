<?php

if(isset($_REQUEST[MM_Session::$PARAM_USER_ID])) {
	global $mmSite;
	
	$user = new MM_User($_REQUEST[MM_Session::$PARAM_USER_ID]);
	
	if($user->isValid()) {
		$error = "";
		$success = "";
		include_once MM_MODULES."/details.header.php";
	}
	$userId = $user->getId();
	require_once(MM_MODULES."/myaccount_shipping_details.php");
}

?>
