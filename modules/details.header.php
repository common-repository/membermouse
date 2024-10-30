<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	if(isset($_REQUEST["mm_page"])) {
		$crntPage = $_REQUEST["mm_page"];
	}
	else {
		$crntPage = MM_ModuleUtils::getPage();
	}
	
	if(isset($_REQUEST["mm_module"])) {
		$module = $_REQUEST["mm_module"];
	}
	else {
		$module = MM_ModuleUtils::getModule();
	}
	
	if($user->getFullName() != "") {
		$displayName = $user->getFullName();
	}
	else {
		$displayName = $user->getUsername();
	}
	
	$showCustomFieldsTab =false;
	if(MM_CustomField::hasCustomFields()){
		$showCustomFieldsTab =true;	
	}
	
	
	$shouldShowAccessRights = false;
	if(isset($_REQUEST[MM_Session::$PARAM_USER_ID])) {
		$userId = $_REQUEST[MM_Session::$PARAM_USER_ID];
		MM_Session::value(MM_Session::$KEY_LAST_USER_ID, $userId);
	}
	
	$tmpUser = new MM_User(MM_Session::value(MM_Session::$KEY_LAST_USER_ID));
	if($tmpUser->isValid()){
		
		if($tmpUser->getStatus() != MM_MemberStatus::$PENDING){
			$shouldShowAccessRights = true;
		}
	}
?>
<div class="mm-header-tabs" style="padding-top: 0px;">
	<div class="wrap" style="padding-bottom: 5px;">
   		<h2 style="padding: 10px 0px 0px 0px">Member Details for <?php echo $displayName; ?></h2>
	</div>
	
	<div style="font-size:14px">
		Access Rights: 
		<img src="<?php echo MM_Utils::getImageUrl('user'); ?>" style="vertical-align: middle" /> <?php echo $user->getMemberTypeName(); ?>
		
		<?php 
			$tags = $user->getAccessTagNames();
			if($tags != "") { 
		?>
			&nbsp;&nbsp;<img src="<?php echo MM_Utils::getImageUrl('tag'); ?>" style="vertical-align: middle" /> <?php echo $tags; ?>
		<?php } ?>
	</div>
	<div style="font-size:11px; color:#a1a1a1; margin-top: 8px;">
		Registered <?php echo $user->getRegistrationDate(true); ?>
	</div>
	
	<ul>
		<li  class='<?php echo ($module == MM_MODULE_DETAILS_GENERAL ? "selected":""); ?>'>
			<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DETAILS_GENERAL); ?>&user_id=<?php echo $user->getId(); ?>">General</a>
		</li>
		<?php if($showCustomFieldsTab){ ?>
		<li  class='<?php echo ($module == MM_MODULE_DETAILS_CUSTOM_FIELDS ? "selected":""); ?>'>
			<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DETAILS_CUSTOM_FIELDS); ?>&user_id=<?php echo $user->getId(); ?>">Custom Fields</a>
		</li>
		<?php } ?>
		<?php if($shouldShowAccessRights){ ?>
		<li class='<?php echo ($module == MM_MODULE_DETAILS_ACCESS_RIGHTS ? "selected":""); ?>'>
			<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DETAILS_ACCESS_RIGHTS); ?>&user_id=<?php echo $user->getId(); ?>">Manage Access Rights</a>
		</li>
		<?php } ?>
		<li class='<?php echo ($module == MM_MODULE_DETAILS_ORDER_HISTORY ? "selected":""); ?>'>
			<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DETAILS_ORDER_HISTORY); ?>&user_id=<?php echo $user->getId(); ?>">Order History</a>
		</li>
		<li class='<?php echo ($module == MM_MODULE_DETAILS_BILLING_INFO ? "selected":""); ?>'>
			<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DETAILS_BILLING_INFO); ?>&user_id=<?php echo $user->getId(); ?>">Billing Info</a>
		</li>
		<li class='<?php echo ($module == MM_MODULE_DETAILS_SHIPPING_INFO ? "selected":""); ?>'>
			<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DETAILS_SHIPPING_INFO); ?>&user_id=<?php echo $user->getId(); ?>">Shipping Info</a>
		</li>
	</ul>
</div>

<div style="clear: both"></div>