<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$users = MM_User::getAllMembers(true);
$userKeys = array_keys($users);
shuffle($userKeys);
$userId = array_pop($userKeys);
$user = new MM_User($userId);
if(!$user->isValid()){
	echo "Not a valid user id {$userId}";
	exit;
}
$fields = array(
	'first_name'=>$user->getFirstName(),
	'last_name'=>$user->getFirstName(),
	'phone'=>$user->getPhone(), 
	'email'=>$user->getEmail(),
	'password'=>$user->getPassword(),
	'billing_address'=>$user->getBillingAddress(),
	'billing_city'=>$user->getBillingCity(),
	'billing_state'=>$user->getBillingState(),
	'billing_zip'=>$user->getBillingZipCode(),
	'billing_country'=>$user->getBillingCountryName(),
 	'shipping_address'=>$user->getShippingAddress(),
	'shipping_city'=>$user->getShippingCity(),
	'shipping_state'=>$user->getShippingState(),
	'shipping_zip'=>$user->getShippingZipCode(),
	'shipping_country'=>$user->getShippingCountryName(),
	'status'=>$user->getStatus(),
);

$customFields = MM_CustomField::getCustomFieldsList();
foreach($customFields as $id=>$val){
	$customField = new MM_CustomField($id);
	if($customField->isValid()){
		$fieldName = $customField->getFieldName();
		if($customField->getRequired() == '1'){
			$fields[$fieldName] = $user->getCustomDataByName($fieldName);
		}
		else{
			$fields[$fieldName] = $user->getCustomDataByName($fieldName);
		}
 	}
}
$rows = MM_ApiTestView::generateRows("updateMember", $fields);
?>
<div style='clear:both; height: 20px;'></div>
<table>
<?php echo $rows; ?>
</table>