<?php
require_once("../../../../wp-load.php");
require_once("../includes/mm-constants.php");
require_once("../includes/init.php");

function goToErrorPage($msg=""){
	$corePageEngine = new MM_CorePageEngine();
	$url = $corePageEngine->getUrl(MM_CorePageType::$ERROR);
	$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_MESSAGE_KEY, $msg, true);
	wp_redirect($url);
	exit;
}

function goToConfirmationPage($memberTypeId){
	$corePageEngine = new MM_CorePageEngine();
	$params = array();
	$params["isFree"] = 1;
	$params["refType"] = MM_TYPE_MEMBER_TYPE;
	$params["refId"] = $memberTypeId;
		
	$url = $corePageEngine->getUrl(MM_CorePageType::$PAID_CONFIRMATION, $params);
	wp_redirect($url);
	exit;
}

function buildPostArray($fields, $prefix = "mm_order_"){
	global $request;
	
	$post = array();
	foreach($fields as $k=>$v){
		if(isset($request[$k])){
			if(is_array($v)){
				if(!empty($request[$k])){
					$v = $request[$k];
				}
				else{
					$v = $v["default"];
				}
			}
		}
		else{
			if(is_array($v)){
				$v = $v["default"];
			}
		}
		$post[$prefix.$k] = $v;
	}
	return $post;
}

$optionalCustomFields = array();
$requiredCustomFields = array();
$fields = MM_CustomField::getCustomFieldsList();
foreach($fields as $id=>$val){
	$customField = new MM_CustomField($id);
	if($customField->isValid()){
		$fieldName = $customField->getFieldName();
		if($customField->getShowOnReg()=='1'){
			if($customField->getRequired() == '1'){
				$requiredCustomFields[] = $customField;
			}
			else{
				$optionalCustomFields[] = $customField;
			}
		}
 	}
}
$requiredFields = array('email', 'member_type');
foreach($requiredCustomFields as $cf){
	$requiredFields[] = $cf->getFieldName();	
}

$request = $_REQUEST;

foreach($requiredFields as $field){
	if(!isset($request[$field]) || (isset($request[$field]) && empty($request[$field]))){
		goToErrorPage("Could not find a required field or it is empty : {$field}");
	}
}

$memberType = new MM_MemberType($request["member_type"]);
if($memberType->isValid()){
	$req = array(
		'member_type'=> $memberType->getId(),
		'username' => array('default'=>$request["email"]),
		'password' =>  array('default'=>MM_Utils::createRandomString(7)),
		'email' => $request["email"],
		'first_name' => array('default'=>''),
		'last_name' =>array('default'=>''),
		'phone' =>array('default'=>''),

		'billing_country' =>array('default'=>''),
		'billing_address' =>array('default'=>''),
		'billing_city' =>array('default'=>''),
		'billing_state' =>array('default'=>''),
		'billing_zip' =>array('default'=>''),
	
		'shipping_country' =>array('default'=>''),
		'shipping_address' =>array('default'=>''),
		'shipping_city' =>array('default'=>''),
		'shipping_state' =>array('default'=>''),
		'shipping_zip' =>array('default'=>''),
	);

	foreach($requiredCustomFields as $cf){
		$req[$cf->getFieldName()] = $request[$cf->getFieldName()];	
	}

	foreach($optionalCustomFields as $cf){
		$req[$cf->getFieldName()] = array('default'=>'');	
	}
	
	$post = buildPostArray($req);
	$response = MM_TransactionEngine::placeNewOrder($post);
	if($response->type == MM_Response::$ERROR){
		goToErrorPage($response->message);
	}	
	goToConfirmationPage($memberType->getId());
}
else{
	goToErrorPage("Invalid member type.");	
}