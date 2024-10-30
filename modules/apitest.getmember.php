<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
//api keys
$keys = MM_Api::getKeyList(true);
$keyObj = array_pop($keys);
$apiKey = new MM_Api($keyObj->id);

// user
$users = MM_User::getAllMembers(true);
$userKeys = array_keys($users);
shuffle($userKeys);
$userId = array_pop($userKeys);
$user = new MM_User($userId);
if(!$user->isValid()){
	echo "Not a valid user id {$userId}";
	exit;
}

//customers
$customers = MM_User::getAllMembers(true,true);
$customerKeys = array_keys($customers);
shuffle($customerKeys);
$customerId = array_pop($customerKeys);
$customer = new MM_User();
$customer->setCustomerId($customerId);
$customer->getDataByCustomerId();
if(!$customer->isValid()){
	echo "Not a valid user id {$customerId}";
	exit;
}

$userSelect = MM_HtmlUtils::generateSelectionsList($users, $userId);
$customerSelect = MM_HtmlUtils::generateSelectionsList($customers, $customerId);
?>
<div style='clear:both; height: 20px;'></div>
<table>
<tr>
	<td>API Key</td>
	<td><input type='text' name='api_key' id='api_key' value='<?php echo $apiKey->getApiKey(); ?>' style='width: 220px;' /></td>
</tr>
<tr>
	<td>API Secret</td>
	<td><input type='text' id='api_secret' value='<?php echo $apiKey->getApiSecret(); ?>' style='width: 220px;' /></td>
</tr>
		
<tr>
	<td>User Type</td>
	<td>
		<select id='user_type' onchange='mmjs.changeToLL()'>
			<option value='membermouse'>MemberMouse</option>
			<option value='limelight'>Lime Light</option>
		</select>
	</td>
</tr>
<tr id='membermouse_list'>
	<td>
		ID
	</td>
	<td>
		<select id='user_id' onchange="mmjs.setMemberId();">
			<?php echo $userSelect; ?>
		</select>
	</td>
</tr>
<tr id='limelight_list' style='display:none;'>
	<td>
		Customer ID
	</td>
	<td>
		<select id='customer_id' onchange="mmjs.setMemberId();">
			<?php echo $customerSelect; ?>
		</select>
	</td>
</tr>
<tr>
	<td colspan='2'>
			<input type='hidden' id='member_id' value='<?php echo $userId; ?>' />
			<input type='button' name='apitest' value="Submit"  class='button-secondary' onclick="mmjs.callApiFunction('getMember');" />
	</td>
</tr>
</table>