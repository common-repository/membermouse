<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$memberTypes = MM_MemberType::getMemberTypesList(true);
$keys = array_keys($memberTypes);
shuffle($keys);
$fields = array(
	'first_name'=>array('createRandomString', array(7, true)),
	'last_name'=>array('createRandomString', array(7, true)),
	'member_type_id'=>array_pop($keys),
	'phone'=>array('createRandomString', array(10, false, true)), 
	'email'=>MM_Utils::createRandomString(7).'@membermouse.com',
	'billing_address'=>array('createRandomString', array(10,true)),
	'billing_city'=>array('createRandomString', array(10,true)),
	'billing_state'=>array('createRandomString', array(2,true)),
	'billing_zip'=>array('createRandomString', array(8,false,true)),
	'billing_country'=>array('createRandomString', array(10,true)),
 /*	'shipping_address'=>array('createRandomString', array(10,true)),
	'shipping_city'=>array('createRandomString', array(10,true)),
	'shipping_state'=>array('createRandomString', array(2,true)),
	'shipping_zip'=>array('createRandomString', array(8,false)),
	'shipping_country'=>array('createRandomString', array(10,true)),*/
	'payment_method'=>array('chooseRandomOption', array(array('visa','master card','discover','amex'))),
	'credit_number'=>'324623467824672647',
	'order_payment_choice'=>'0',
	'exp_month'=>Date("m"),
	'exp_year'=>substr(Date("Y"),2),
	'security_code'=>array('createRandomString', array(3,true)),
	'shipping_method'=>'1',
	'shipping_same_as_billing'=>'YES',
);

$customFields = MM_CustomField::getCustomFieldsList();
foreach($customFields as $id=>$val){
	$customField = new MM_CustomField($id);
	if($customField->isValid()){
		$fieldName = $customField->getFieldName();
		if($customField->getRequired() == '1'){
			$fields[$fieldName] = array('createRandomString', array(7));
		}
		else{
			$fields[$fieldName] = array('createRandomString', array(7));
		}
 	}
}
$rows = MM_ApiTestView::generateRows("createMember", $fields);
?>
<div style='clear:both; height: 20px;'></div>
<table>
<?php echo $rows; ?>
</table>