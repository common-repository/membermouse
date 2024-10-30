<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 *
 */
$userId = MM_User::getLastCustomer();


$memberTypes = MM_MemberType::getMemberTypesList(true,MM_MemberType::$SUB_TYPE_PAID);
$keys = array_keys($memberTypes);
shuffle($keys);
$selectedType = array_pop($keys);
$mType = new MM_MemberType($selectedType);
$fields = array(
	'payment_type'=>array('chooseRandomOption', array(array('echeck','instant'))),
	'payment_date'=>Date("h:m:s M d, Y")." PDT",
	'payment_status'=>array('chooseRandomOption', array(array('Processed','Refunded','Reversed','Voided','Canceled_Reversal','Completed','Denied','Expired','Failed','In-Progress','Partially_Refunded','Pending'))),
	'payer_status'=>array('chooseRandomOption', array(array('verified','unverified'))),
	'first_name'=>array('chooseRandomOption', array(array('Joe','Matt','Eric','Rick','Abba','John','Jenson','Alex','Meghan','Chelsea','Erin'))),
	'last_name'=>array('chooseRandomOption', array(array('Smith','Jones','Peterson','Zabba','Ghandi','Buddha'))),
	'payer_email'=>MM_Utils::createRandomString(7).'@membermouse.com',
	'payer_id'=>rand(0,99999),
	'business'=>'eric.popfizz@gmail.com',
	'receiver_email'=>'matt.young@gothosting.org',
	'receiver_id'=>rand(0,99999),
	'residence_country'=>'US',
	'item_name1'=>$mType->getName(),
	'item_number1'=>$mType->getRegistrationProduct(),
	'quantity1'=>'1',
	'tax'=>'0',
	'mc_currency'=>'0',
	'mc_fee'=>'0',
	'mc_gross'=>'0',
	'verify_sign'=>1,
	'mc_gross_1'=>'0',
	'mc_handling'=>'0',
	'mc_handling1'=>'0',
	'mc_shipping'=>'0',
	'mc_shipping1'=>'0',
	'txn_type'=>'cart',
	'txn_id'=>'cart',
	'notify_version'=>'2.4',
	'custom'=>'', //user id
	'invoice'=>'',

);

$rows = MM_ApiTestView::generateRows("postCallback", $fields,false);
?>
<div style='clear:both; height: 20px;'></div>
<table>
<?php echo $rows; ?>
</table>