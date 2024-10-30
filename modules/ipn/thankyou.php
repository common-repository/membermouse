<?php
require_once("../../../../../wp-load.php");
require_once("../../includes/mm-constants.php");
require_once("../../includes/init.php");

$userId = MM_Session::value(MM_Session::$KEY_LAST_USER_ID);
$user= new MM_User($userId);
if(intval($userId)<=0){
	if(isset($_REQUEST["cemail"])){
		$user->setEmail($_GET["cemail"]);
		$user->getDataByEmail();
		
		MM_Session::value(MM_Session::$KEY_LAST_USER_ID, $user->getId());
	}
}
$memberType = new MM_MemberType($user->getMemberTypeId());
$product = new MM_Product($memberType->getRegistrationProduct());

$clickBank = new MM_ClickBankService();
$confirmationPage = $clickBank->getReturnUrl($product, $memberType);
header("Location: ".$confirmationPage);