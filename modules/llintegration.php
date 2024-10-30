<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

if(preg_match("/(dev.membermouse.com)/", $_SERVER["SERVER_NAME"])){
	define("TESTAPI_KEY", "afdgwttg");
	define("TESTAPI_SECRET", "fanepu6k");
}
else{
	define("TESTAPI_KEY", "m4x38ofgvgy");
	define("TESTAPI_SECRET", "irpfkpiniuh");	
}
function updateCampaignTest() 
{
	echo "Start updateCampaignTest()<br/>";
	$data = new stdClass();
	
	$data->action_type = "add";
	$data->campaign_id = "6";
	$data->campaign_name = "MemberMouse Test Campaign";
	$data->campaign_description = "This is the MemberMouse test campaign";
	$data->product_ids = "6,7,8,9,11,12,13,14,15,16,60,61,62";
	$data->countries = "AU,CA,GB,US";
	$data->payment_methods = "amex,discover,master,visa";
	
	$shippingMethods = array();
	
	$sm = new stdClass();
	$sm->id = "1";
	$sm->name = "Verification";
	
	array_push($shippingMethods, $sm);
	
	$sm = new stdClass();
	$sm->id = "2";
	$sm->name = "Instant Access";
	
	array_push($shippingMethods, $sm);
	
	$data->shipping_methods = $shippingMethods;
	
	$result = MM_APIService::updateCampaign($data);
	
	echo $result->message."<br/>";
	echo "End updateCampaignTest()<br/>";
}

function llUpdateCampaignTest() 
{
	echo "Start llUpdateCampaignTest()<br/>";
	$data = new stdClass();
	
	$data->action_type = "edit";
	$data->campaign_id = "6";
	$data->campaign_name = "MemberMouse Second Campaign";
	$data->campaign_description = "This is the MemberMouse test campaign again";
	$data->product_ids = "12,13,14,60";
	$data->countries = "US";
	$data->payment_methods = "amex,discover";
	
	$shippingMethods = array();
	
	$sm = new stdClass();
	$sm->id = "1";
	$sm->name = "Verification";
	
	array_push($shippingMethods, $sm);
	
	$sm = new stdClass();
	$sm->id = "2";
	$sm->name = "Instant Access";
	
	array_push($shippingMethods, $sm);
	
	$data->shipping_methods = json_encode($shippingMethods);
	
	$url = MM_API_URL."?q=/updateCampaign";
	$postvars = "apikey=".TESTAPI_KEY."&apisecret=".TESTAPI_SECRET."&";
	foreach($data as $k=>$v){
		$postvars.=$k."=".$v."&";
	}
	 echo "POST VARS: ".$postvars."<br /><br />";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "RAW Response: ".$result."<br />";
	$obj = json_decode($result);
	echo $obj->response_message."<br/>";
	echo "End llUpdateCampaignTest()<br/>";
}

function llUpdateProductTest() 
{
	
	echo "Start llUpdateProductTest()<br/>";
	$data = new stdClass();
	
	$data->campaign_id = "5";
	$data->product_id = "14";
	$data->product_name = "15 Day Subscription";
	$data->product_sku = "1008";
	$data->product_price = "15.00";
	$data->product_description = "This is a 15 day subscription product";
	$data->product_category = "MemberMouse Test";
	$data->is_free_trial = "0";
	$data->is_shippable = "0";
	$data->rebill_product_id = "14";
	$data->rebill_period = "15";
	
	$url = MM_API_URL."?q=/updateProduct";
	$postvars = "apikey=".TESTAPI_KEY."&apisecret=".TESTAPI_SECRET."&";
	foreach($data as $k=>$v){
		$postvars.=$k."=".$v."&";
	}
	 
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "RAW Response: ".$result."<br />";
	$obj = json_decode($result);
	echo $obj->response_message."<br/>";
	echo "End llUpdateProductTest()<br/>";
}


function updateProductTest() 
{
	echo "Start updateProductTest()<br/>";
	$data = new stdClass();
	
	$data->product_id = "14";
	$data->product_name = "15 Day Subscription";
	$data->product_sku = "1008";
	$data->product_price = "15.00";
	$data->product_description = "This is a 15 day subscription product";
	$data->product_category = "MemberMouse Test";
	$data->is_free_trial = "0";
	$data->is_shippable = "0";
	$data->rebill_product_id = "14";
	$data->rebill_period = "15";
	
	$result = MM_APIService::updateProduct($data);
	
	echo $result->message."<br/>";
	echo "End updateProductTest()<br/>";
}

function llUpdateOrderTest() 
{
	
	echo "Start llUpdateOrderTest()<br/>";
	$data = new stdClass();
	
	$data->campaign_id = "5";
	$data->main_order_id = "35289";
	$data->order_status = MM_Order::$STATUS_DECLINED;
	$data->is_recurring = "0";
	$data->new_order_id = "35290";
	$data->first_name = "Josh";
	$data->last_name = "Dude";
	$data->shipping_address = "64 Beaver St. #408";
	$data->shipping_city = "New York";
	$data->shipping_state = "NY";
	$data->shipping_zip = "10004";
	$data->shipping_country = "US";
	$data->billing_address = "64 Beaver St. #408";
	$data->billing_city = "New York";
	$data->billing_state = "NY";
	$data->billing_zip = "10004";
	$data->billing_country = "US";
	$data->phone = "(617) 650-9824";
	
	$url = MM_API_URL."?q=/updateOrder";
	$postvars = "apikey=".TESTAPI_KEY."&apisecret=".TESTAPI_SECRET."&";
	foreach($data as $k=>$v){
		$postvars.=$k."=".$v."&";
	}
	 
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "RAW Response: ".$result."<br />";
	$obj = json_decode($result);
	echo $obj->response_message."<br/>";
	echo "End llUpdateOrderTest()<br/>";
}


function updateOrderTest() 
{
	echo "Start llUdpateOrderTest()<br/>";
	$data = new stdClass();
	
	$data->main_order_id = "14223";
	$data->order_status = MM_Order::$STATUS_APPROVED;
	$data->is_recurring = "1";
	$data->new_order_id = "0";
	$data->first_name = "Eric";
	$data->last_name = "Turnnessen";
	$data->shipping_address = "64 Beaver St. #428";
	$data->shipping_city = "New York";
	$data->shipping_state = "NY";
	$data->shipping_zip = "10004";
	$data->shipping_country = "US";
	$data->billing_address = "64 Beaver St. #428";
	$data->billing_city = "New York";
	$data->billing_state = "NY";
	$data->billing_zip = "10004";
	$data->billing_country = "US";
	$data->phone = "(617) 650-9824";
	
	$result = MM_APIService::updateOrder($data);
	
	echo $result->message."<br/>";
	echo "End llUdpateOrderTest()<br/>";
}

function newMemberTest()
{
	echo "Start newMemberTest()<br/>";
	$data = new stdClass();
	
	$data->customer_id = "245";
	$data->first_name = "Eric";
	$data->last_name = "Turnnessen";
	$data->email = "eric.turnnessen20@gmail.com";
	$data->phone = "(617) 650-9824";
	$data->shipping_address = "64 Beaver St. #428";
	$data->shipping_city = "New York";
	$data->shipping_state = "NY";
	$data->shipping_zip = "10004";
	$data->shipping_country = "US";
	$data->billing_address = "64 Beaver St. #428";
	$data->billing_city = "New York";
	$data->billing_state = "NY";
	$data->billing_zip = "10004";
	$data->billing_country = "US";
	$data->order_id = "14223";
	$data->order_total = "15.95";
	$data->product_ids = "7";
	
	$result = MM_APIService::newMember($data);
	
	echo $result->message."<br/>";
	echo "End newMember()<br/>";
}

function llPreAuthorizeOrder(){
	
	
	echo "Start llPreAuthorizeOrder()<br/>";
	$data = new stdClass();
	$data->email = "gold33@gold33.com";
	$data->product_ids = "2,7";
	$data->campaign_id = "5";
	
	$url = MM_API_URL."?q=/preAuthorizeOrder";
	$postvars = "apikey=".TESTAPI_KEY."&apisecret=".TESTAPI_SECRET."&";
	foreach($data as $k=>$v){
		$postvars.=$k."=".$v."&";
	}
	 echo $postvars;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "<br /><br />RAW Response: ".$result."<br />";
	$obj = json_decode($result);
	echo "Message: ".$obj->response_message."<br/>";
	return $obj;
	
}

function lldeleteOrderTest(){
	
	
	echo "Start lldeleteOrderTest()<br/>";
	$data = new stdClass();
	$data->order_id = "14248";
	$data->product_ids = "60";
	$data->campaign_id = "5";
	
	$url = MM_API_URL."?q=/deleteOrder";
	$postvars = "apikey=".TESTAPI_KEY."&apisecret=".TESTAPI_SECRET."&";
	foreach($data as $k=>$v){
		$postvars.=$k."=".$v."&";
	}
	 
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "RAW Response: ".$result."<br />";
	$obj = json_decode($result);
	echo "Message: ".$obj->response_message."<br/>";
}


function issueUpdateCampaign(){
	echo "starting issueUpdateCampaign()<br />";
	$url = MM_API_URL."?q=/updateCampaign";
//	$postvars = "apikey=phpngatvtqj&apisecret=0ucbxyk60yb&action_type=add&campaign_id=21&campaign_name=sdfasdfasdf&campaign_description=asdf&product_ids=1%2C&countries=US&payment_methods=amex%2Cdiscover%2Cmaster%2Cvisa&shipping_methods=%5B%7B%22id%22%3A%221%22%2C%22name%22%3A%22First+Class+Mail%22%7D%2C%7B%22id%22%3A%222%22%2C%22name%22%3A%22Priority+USPS%22%7D%5D";
	$postvars = "apikey=".TESTAPI_KEY."&apisecret=".TESTAPI_SECRET."&action_type=add&campaign_id=21&campaign_name=sdfasdfasdf&campaign_description=asdf&product_ids=1%2C&countries=US&payment_methods=amex%2Cdiscover%2Cmaster%2Cvisa&shipping_methods=%5B%7B%22id%22%3A%221%22%2C%22name%22%3A%22First+Class+Mail%22%7D%2C%7B%22id%22%3A%222%22%2C%22name%22%3A%22Priority+USPS%22%7D%5D";
	echo $url." : ".$postvars."<br />";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "RAW Response: ".$result."<br />";
	$obj = json_decode($result);
	var_dump($obj);
	echo "End issueUpdateCampaign()<br/>";
	
}

function issueNewMember(){
	echo "Start issueNewMember()<br/>";
	$url = MM_API_URL."?q=/newMember";
	$postvars = "apikey=phpngatvtqj&apisecret=0ucbxyk60yb&product_ids=1%2C3%2C4&customer_id=54&order_id=10101&order_total=5.00&email=asdfsdafasd%40asefasdfasdf.com&campaign_id=1&first_name=dfdfdfdf&last_name=fdsdffsd&shipping_address=sdfsdf&phone=32423423423423&shipping_state=fsdsdf&shipping_city=sdfdfs&shipping_zip=23433&shipping_country=AL&billing_address=sdfsdf&billing_city=sdfdfs&billing_state=fsdsdf&billing_zip=23433&billing_country=AL";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "RAW Response: ".$result."<br />";
	$obj = json_decode($result);
	if(isset($obj->response_data->url)){
		echo "URL: ".$obj->response_data->url."<br/>";
		echo "MEMBER ID: ".$obj->response_data->member_id."<br/>";
	}
	echo "End issueNewMember()<br/>";
}

function llNewMemberTest()
{
	$response = llPreAuthorizeOrder();
	if($response->response_code!="200"){
		return false;
	}
	
	echo "Start llNewMemberTest()<br/>";
	$data = new stdClass();
	
	$data->campaign_id = "5";
	$data->customer_id = "245";
	$data->first_name = "Eric";
	$data->last_name = "Test";
	$data->email = MM_Utils::createRandomString(7)."@membermouse.com";
	$data->phone = "(617) 650-9824";
	$data->shipping_address = "64 Beaver St. #428";
	$data->shipping_city = "New York";
	$data->shipping_state = "NY";
	$data->shipping_zip = "10004";
	$data->shipping_country = "US";
	$data->billing_address = "64 Beaver St. #428";
	$data->billing_city = "New York";
	$data->billing_state = "NY";
	$data->billing_zip = "10004";
	$data->billing_country = "US";
	$data->order_id = "14223";	
	$data->order_total = "15.95";
	$data->product_ids = "7";
	
	$url = MM_API_URL."?q=/newMember";
	$postvars = "apikey=".TESTAPI_KEY."&apisecret=".TESTAPI_SECRET."&";
	foreach($data as $k=>$v){
		$postvars.=$k."=".$v."&";
	}
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "RAW Response: ".$result."<br />";
	$obj = json_decode($result);
	if(isset($obj->response_data->url)){
		echo "URL: ".$obj->response_data->url."<br/>";
		echo "MEMBER ID: ".$obj->response_data->member_id."<br/>";
	}
	
	echo "End llNewMemberTest()<br/>";
}

function makeRequest($method, $data){
	
	$url = MM_API_URL."?q=/".$method;
	$postvars = "apikey=".TESTAPI_KEY."&apisecret=".TESTAPI_SECRET."&";
	foreach($data as $k=>$v){
		$postvars.=$k."=".$v."&";
	}
	echo $url." : ".$postvars;
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST      ,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "Response: ".$result;
}

function apiUpdateMember(){
	$data = new stdClass();
	$data->first_name = "Matts";
	$data->last_name = "Youngs";
	$data->email = "mattyoung@mattyoung.com"; 
	$data->member_type_id = "19";
	$data->phone = "123-123-1234";
	$data->shipping_address = "64 Beaver St. #428";
	$data->shipping_city = "New York";
	$data->status = MM_MemberStatus::$ACTIVE;
	$data->shipping_state = "NY";
	$data->shipping_zip = "10004";
	$data->shipping_country = "US";
	$data->billing_address = "64 Beaver St. #428";
	$data->billing_city = "New York";
	$data->billing_state = "NY";
	$data->billing_zip = "10004";
	$data->billing_country = "US";
	$data->payment_method = "visa";
	$data->credit_number = "12383477238748234787487239847";
	$data->exp_month = "12";
	$data->exp_year = "11";
	$data->security_code = "222";
	$data->shipping_method = "1";
	
	makeRequest("updateMember", $data);
}

function apiCreateMember(){
	$data = new stdClass();
	$data->first_name = "Matt";
	$data->last_name = "Young";
	$data->email = "mattyoung332@mattyoung.com"; 
	$data->member_type_id = "19";
	$data->phone = "123-123-1234";
	$data->shipping_address = "64 Beaver St. #428";
	$data->shipping_city = "New York";
	$data->shipping_state = "NY";
	$data->shipping_zip = "10004";
	$data->shipping_country = "US";
	$data->billing_address = "64 Beaver St. #428";
	$data->billing_city = "New York";
	$data->billing_state = "NY";
	$data->billing_zip = "10004";
	$data->billing_country = "US";
	$data->payment_method = "visa";
	$data->credit_number = "12383477238748234787487239847";
	$data->exp_month = "12";
	$data->second_field = "Yo Yo Yo";
	$data->exp_year = "11";
	$data->security_code = "222";
	$data->shipping_method = "1";
	
	makeRequest("createMember", $data);
}

if(isset($_GET["method"]))
{
	$func = $_GET["method"];
	if(function_exists($func))
	{
		call_user_func($func);
	}
}

//updateCampaignTest();
//updateProductTest();
//updateOrderTest();
//newMemberTest();
//llUpdateOrderTest();
//llUpdateCampaignTest();
//llUpdateProductTest();
?>
<div style='margin-top: 20px; clear:both'>
API Methods: <select id='mm-ll-method' name='method' onchange="document.location.href='admin.php?page=mm_admintools&module=llintegration&method='+mmJQuery('#mm-ll-method').val();">
<option>Choose</option>
<option value='llUpdateCampaignTest'>Update Campaign</option>
<option value='llUpdateProductTest'>Update Product</option>
<option value='llUpdateOrderTest'>Update Order</option>
<option value='llPreAuthorizeOrder'>Pre Authorization</option>
<option value='lldeleteOrderTest'>Delete Order</option>
<option value='llNewMemberTest'>New Member</option>
</select>
<script type='text/javascript'>
var method = '<?php echo ((isset($_GET["method"]))?$_GET["method"]:""); ?>';
mmJQuery("#mm-ll-method").val(method);
</script>
</div>