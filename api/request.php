<?php
// bootstrap wp
require_once("../../../../wp-load.php");

require_once("../includes/mm-constants.php");
require_once("../includes/init.php");
require_once("classes/class.response.php");
require_once("classes/class.utils.php");
require_once("include/loadLibrary.php");
require_once('controllers/class.webcontroller.php');
require_once('controllers/class.ordercontroller.php');
require_once('controllers/class.membercontroller.php');
require_once('controllers/class.releasecontroller.php');

$_GET["q"] = (isset($_GET["q"]) && $_GET["q"] != null)?$_GET["q"]:"";

$rest = new RestServer($_GET["q"]);

$ref = (isset($_SERVER["HTTP_REFERER"]))?$_SERVER["HTTP_REFERER"]:"";
$ip = (isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"";
//Utils::logEvent($ip, $ref);

$rest->addMap("GET","/?","WebController");

// General MM Api Requests
$rest->addMap("GET","/createMember","MemberController::createMember"); 
$rest->addMap("POST","/createMember","MemberController::createMember"); 
$rest->addMap("GET","/updateMember","MemberController::updateMember"); 
$rest->addMap("POST","/updateMember","MemberController::updateMember"); 
$rest->addMap("GET","/getMember","MemberController::getMember"); 
$rest->addMap("POST","/getMember","MemberController::getMember"); 

// LL Specific Requests
$rest->addMap("GET","/updateOrder","OrderController::updateOrder"); 
$rest->addMap("POST","/updateOrder","OrderController::updateOrder"); 
$rest->addMap("GET","/updateCampaign","OrderController::updateCampaign"); 
$rest->addMap("POST","/updateCampaign","OrderController::updateCampaign"); 
$rest->addMap("GET","/updateProduct","OrderController::updateProduct"); 
$rest->addMap("POST","/updateProduct","OrderController::updateProduct"); 
$rest->addMap("GET","/newMember","OrderController::newMember"); 
$rest->addMap("POST","/newMember","OrderController::newMember"); 
$rest->addMap("GET","/deleteOrder","OrderController::deleteOrder"); 
$rest->addMap("POST","/deleteOrder","OrderController::deleteOrder"); 
$rest->addMap("GET","/preAuthorizeOrder","OrderController::preAuthorizeOrder"); 
$rest->addMap("POST","/preAuthorizeOrder","OrderController::preAuthorizeOrder"); 
$rest->addMap("POST","/deployRelease","ReleaseController::deployMMVersion"); 

echo $rest->execute();

?>
