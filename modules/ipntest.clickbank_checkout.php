<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 *
 */
$userId = MM_User::getLastCustomer();


$memberTypes = MM_MemberType::getMemberTypesList(true);
$keys = array_keys($memberTypes);
shuffle($keys);
$selectedType = array_pop($keys);
$mType = new MM_MemberType($selectedType);
/*
		 *  {\"cprodtitle\":\"MM Test\",\"ctranspaymentmethod\":\"CARD\",\"cfuturepayments\":\"\",\"ccustzip\":\"29579\",
		 *  \"ccustshippingzip\":\"29579\",\"ccustemail\":\"matt@somesite.com\",\"crebillstatus\":\"\",\"ctransaffiliate\":\"\",
		 *  \"cupsellreceipt\":\"\",\"corderamount\":\"300\",\"ccustcounty\":\"\",\"ccurrency\":\"USD\",\"ccustfirstname\":\"MATT\",
		 *  \"crebillamnt\":\"\",\"ctransaction\":\"TEST_SALE\",\"ccuststate\":\"SC\",\"caccountamount\":\"178\",
		 *  \"ctranspublisher\":\"dating1280\",\"ctid\":\"\",\"ccustshippingcountry\":\"US\",\"cnextpaymentdate\":\"\",\"cverify\":\"971523B2\",
		 *  \"cprocessedpayments\":\"\",\"cprodtype\":\"STANDARD\",\"ccustcc\":\"US\",\"ccustshippingstate\":\"SC\",
		 *  \"ctransreceipt\":\"WJYWBT3E\",\"ccustfullname\":\"Matt Young\",\"cvendthru\":\"url=http%3A%2F%2F1.dating1280.pay.clickbank.net&\",
		 *  \"ctransrole\":\"VENDOR\",\"ccustaddr2\":\"\",\"ccustaddr1\":\"\",\"ccustcity\":\"\",\"ccustlastname\":\"YOUNG\",
		 *  \"ctranstime\":\"1300648166\",\"cproditem\":\"1\"}"
		 */
		

$fields = array(
	'cprodtitle'=>'Product Title',
	'ctranspaymentmethod'=>'CARD',
	'cfuturepayments'=>'',
	'ccustzip'=>rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9),
	'ccustshippingzip'=>rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9),
	'ccustemail'=>'superduper@superduper.com',
	'crebillstatus'=>'',
	'ctransaffiliate'=>'',
	'cupsellreceipt'=>'',
	'corderamount'=>'12.00',
	'ccustcounty'=>'',
	'ccurrency'=>'USD',
	'ccustfirstname'=>array('chooseRandomOption', array(array('Joe','Matt','Eric','Rick','Abba','John','Jenson','Alex','Meghan','Chelsea','Erin'))),
	'ccustfullname'=>'',
	'crebillamnt'=>'',
	'cverify'=>1,
	'ctransaction'=>'TEST_SALE',
	'ccuststate'=>'SC',
	'caccountamount'=>'178',
	'ctranspublisher'=>'membermous',
	'ccustcc'=>'1444444444444440',
	'ctid'=>'',
	'ccustshippingcountry'=>'US',
	'ccustshippingstate'=>'SC',
	'ctransreceipt'=>'WJYWBT3E',
	'cvendthru'=>'url=http%3A%2F%2F1.membermous.pay.clickbank.net&',
	'ctransrole'=>'VENDOR',
	'ccustaddr2'=>'',
	'ccustaddr1'=>'',
	'ccustcity'=>'',
	'ccustlastname'=>array('chooseRandomOption', array(array('Smith','Jones','Peterson','Zabba','Ghandi','Buddha'))),
	'ctranstime'=>'1300648166',
	'cproditem'=>1,
);

$rows = MM_ApiTestView::generateRows("postCallback", $fields,false);
?>
<div style='clear:both; height: 20px;'></div>
<table>
<?php echo $rows; ?>
</table>