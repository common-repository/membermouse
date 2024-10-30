<?php

if(isset($_REQUEST[MM_Session::$PARAM_USER_ID])) {
	global $mmSite;
	$userId = $_REQUEST[MM_Session::$PARAM_USER_ID];
	MM_Session::value(MM_Session::$KEY_LAST_USER_ID, $userId);
}
	$userId = MM_Session::value(MM_Session::$KEY_LAST_USER_ID);
	$user = new MM_User($userId);
	
	if($user->isValid()) {
		$error = "";
		$success = "";
		include_once MM_MODULES."/details.header.php";
	}
	$userId = $user->getId();
	?>
	<div style='clear:both; height:10px;'></div>
	<?php 
	require_once(MM_MODULES."/myaccount_order_history.php");

?>
