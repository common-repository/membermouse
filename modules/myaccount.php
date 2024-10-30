<?php
$sslNotice = "";
$urlObj = new MM_Url();
if(!$urlObj->isSSL()){
	if(!$urlObj->hasSSL()){
		//$sslNotice = "<b>WARNING: This page is not secure</b>";
		$sslNotice = "";		
	}
}	
$notLoggedInMsg = "";
$userId = $p->user->ID;
$user = new MM_User($userId);
$previewObj = null;

$corePageEngine = new MM_CorePageEngine();
$editLink =  $corePageEngine->getUrl(MM_CorePageType::$MY_ACCOUNT);

if($user->isAdmin()) {
	$previewObj = MM_Preview::getData();
	$user = $previewObj->getUser();
	
	// TEST ONLY
	//$user->setMainOrderId("22044");
//	$user->setCustomerId("381");
}
else if(!$user->isValid()){
	$corePageEngine = new MM_CorePageEngine();
	$url = $corePageEngine->getUrl(MM_CorePageType::$ERROR);
	$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_MESSAGE_KEY, "Please login to access this page.", true);
	$notLoggedInMsg = "document.location.href='".$url."';";
}

if(isset($_GET["membership"])){
	if($_GET["membership"] == "activate"){
		$memberDetails = new MM_MemberDetailsView();
		$userId = $user->getId();
		$post = array(
			'mm_id'=>$userId,
			'mm_order_id'=>$user->getLastOrderId(false),
		);
		$memberDetails->activateMembership($post);
		$user = new MM_User($userId);
	}
}

$emailAccount = MM_EmailAccount::getDefaultAccount();
$context = new MM_Context($user, $emailAccount);
$usingSubModule = false;
if(isset($_GET["module"])){
	$filePath = MM_MODULES."/myaccount_".$_GET["module"].".php";
	if(file_exists($filePath)){
		$usingSubModule = true;
		?>
		<div class='mm-myaccount-back'><a href='<?php echo $editLink; ?>'>Go Back to My Account</a></div>
		<?php if(!empty($sslNotice) && $_GET["module"]=="billing_details"){?>
			<div id='mm-myaccount-ssl'><?php echo $sslNotice; ?></div>
		<?php 
		}
		require_once($filePath);
	}
}
if(!$usingSubModule && empty($notLoggedInMsg)){
	$memberTypeId=$user->getMemberTypeId();
	$accessTags = "<i>No subscriptions</i>";
	$tags = null;
	if($user->isAdmin()) {
		$tags = $previewObj->getAccessTags();
	}
	else{
		$tags = $user->getAccessTags();
	}
	if(count($tags)>0) {
		$accessTags = "";
		
		if(is_array($tags)){
			foreach($tags as $tag) {
				if(isset($tag->access_tag_id)){
					$at = new MM_AccessTag($tag->access_tag_id);
					$orderId = $user->getAccessTagOrderId($at->getId());
					
					$orderView = null;
					
					if(intval($orderId) > 0) {
						$orderView = MM_LimeLightService::getOrder($orderId);
					}
					else{
						continue;
					}
					
					$recurringDate = "";
					
					if(!($orderView instanceof MM_Response) && !is_null($orderView)) {
						if(isset($orderView["recurring_date"]) && !preg_match("/(0000)/", $orderView["recurring_date"])){
		                	$recurringDate = $orderView["recurring_date"];
		                }
					}
					
					if($at->isValid()) {
						$recurringDate = (!empty($recurringDate))?Date("M d, Y", strtotime($recurringDate)):"";
						$billDate = (!empty($recurringDate))?"(Next Bill Date: {$recurringDate})":"";
						if($user->getStatus() == MM_MemberStatus::$PAUSED){
							$recurringDate = MM_NO_DATA;
							$billDate = MM_NO_DATA;
						} 
						if(empty($recurringDate)){
							continue;
						}
						
						//userId, accessTagId, isFree, hasCardOnFile
										
						$accessTagCancel = "";
						if(!$at->isFree()){
							$accessTagCancel= "<a href=\"#\" onclick=\"myAccountJs.confirmCancel('".$user->getId()."','".$at->getId()."','".$at->isFree()."','".$user->hasCardOnFile()."');\">Cancel</a>";
						}
						$accessTags.= $at->getName()." {$billDate} {$accessTagCancel}<br />";
					}
				}
			}
		}
	}
	
	$memberType = new MM_MemberType($memberTypeId);
	$memberTypeImage = "";
	if($memberType->getBadgeUrl()!="") {
		$memberTypeImage = "<img src='".$memberType->getBadgeUrl()."' style='vertical-align: middle;' />";
	}

	$orderInfo = "No order information.";
	if(!$memberType->isFree()){
		$orderId = $user->getMainOrderId();
		if(intval($orderId)>0){
			$orderView = MM_LimeLightService::getOrder($orderId);
			if(!($orderView instanceof MM_Response)){
				$ccType = "";
				switch($orderView["cc_type"]){
					case "visa":
						$ccType = "VISA";
						break;
					case "amex":
						$ccType = "AMEX";
						break;
					case "discover":
						$ccType = "Discover";
						break;
					case "mastercard":
						$ccType = "Master Card";
						break;
					case "master card":
						$ccType = "Master Card";
						break;
					default:
						$ccType = $orderView["cc_type"];
						break;
				}
				
				$orderInfo = "";
				$recurringDate = (!empty($orderView["recurring_date"]))?Date("M d, Y", strtotime($orderView["recurring_date"])):"";
				$orderInfo .= "Card Type: ".$ccType."<br />";
				$orderInfo .= "Card Ending: ".str_pad($orderView["cc_number"], 15, "*", STR_PAD_LEFT)." <br />";
				
				if($user->getStatus() == MM_MemberStatus::$PAUSED){
					$orderInfo .= "Next Bill Date: ".MM_NO_DATA."<br />";
				}
				else{
					$orderInfo .= "Next Bill Date: ".$recurringDate."<br />";
				}
			}	
		}
	}
	
	// CUSTOM FIELDS
	$customFields = "";
	$fields = MM_CustomField::getCustomFieldsList();
	foreach($fields as $id=>$val) {
		$customField = new MM_CustomField($id);
		
		if($customField->isValid()) {
			if($customField->getShowOnMyAccount() == '1') {
				$fieldName = $customField->getFieldLabel();
				$value = $user->getCustomDataByName($customField->getFieldName());
				if(empty($value)){
					$value = MM_NO_DATA;
				}
				$customFields.= $fieldName.": ".stripslashes($value)."<br />";
				
			}
	 	}
	}
	
	$startDate = Date("Y-m-d", strtotime("-5 days",strtotime(Date("Y-m-d"))));
	$history = MM_OrderHistory::getHistory($user->getId(),5,"",$startDate);
	$orderHistoryStr = "<i>No order history available</i>";
	
	if(is_array($history)) {
		$orderHistoryStr = "<table width='95%'>
	<tr>
		<td width='20%'><b>Order Date</b></td>
		<td width='20%'><b>Order #</b></td>
		<td><b>Product Name</b></td>
		<td width='20%'><b>Price</b></td>
	</tr>";
		
		foreach($history as $order) {
			if(is_array($order)){
				$order = MM_Utils::convertArrayToObject($order);
			}
			$orderId = $order->id;
			$orderHistoryStr .= "<tr>
		<td>".Date("m/d/Y", strtotime($order->time_stamp))."</td>	
		<td>{$orderId}</td>	
		<td>{$order->product_name}</td>	
		<td>\${$order->product_price}</td>	
	</tr>";			
		}
		
		$orderHistoryStr .= "</table>";
	}
	$accountLink = MM_Utils::appendUrlParam($editLink, "module", "account_details", true);
	$billingLink = MM_Utils::appendUrlParam($editLink, "module", "billing_details", true);
	$shippingLink = MM_Utils::appendUrlParam($editLink, "module", "shipping_details", true);
	$orderHistoryLink = MM_Utils::appendUrlParam($editLink, "module", "order_history", true);
	$reactivateLink = MM_Utils::appendUrlParam($editLink, "membership", "activate", true);
	?>
	
	<div id='mm-myaccount'>
		<div id='mm-myaccount-error' style='clear:both; color:#D7380A; padding-bottom: 5px;'>
			<?php if($user->getStatus() == MM_MemberStatus::$OVERDUE){ ?>
			Your billing information is out of date. Please update your billing information to activate your account.
			<?php } ?>
		</div>
		<div id='mm-myaccount-membership'>
			<div id='mm-myaccount-membership-title'>
				<img src="<?php echo MM_Utils::getImageUrl("key") ?>" style="vertical-align: middle; padding-right: 4px;" /> 
				My Membership
			</div>
			<div id='mm-myaccount-membership-body'>
				Member Since: <?php echo $user->getRegistrationDate(true); ?><br />
				Membership Level: <?php echo $memberTypeImage; ?> <?php echo $memberType->getName(); ?> 
			<?php if($user->getStatus() == MM_MemberStatus::$PAUSED){ 
				$product = new MM_Product($memberType->getRegistrationProduct());
				$productName = $product->getName();
				?>
			
					<a href='#' onclick="mmMembershipJs.confirmReactivate('<?php echo $reactivateLink; ?>');" style='cursor:pointer;'>Reactivate</a>
			<?php }else{ ?>
					<a href="<?php echo MM_SmartTagEngine::processContent("[MM_Page_Cancellation]", $context); ?>">Cancel</a>
				<?php } ?>
					
					<br /> 
			</div>
		</div>
		<div id='mm-myaccount-subscriptions'>
			<div  id='mm-myaccount-subscriptions-title'>
				<img src="<?php echo MM_Utils::getImageUrl("key") ?>" style="vertical-align: middle; padding-right: 4px;" /> 
				My Subscriptions
			</div>
			<div id='mm-myaccount-subscriptions-body'>
				<?php echo $accessTags; ?>
			</div>
		</div>
		
		<div id='mm-myaccount-details'>
			<div  id='mm-myaccount-details-title'>
				<img src="<?php echo MM_Utils::getImageUrl("vcard") ?>" style="vertical-align: middle; padding-right: 4px;" /> 
				Account Details
				
				<div  class='mm-myaccount-title-right'><a href='<?php echo $accountLink; ?>'>Edit</a></div>
			</div>
			<div id='mm-myaccount-details-body'>
			First Name: <?php echo $user->getFirstName(); ?><br />
			Last Name: <?php echo $user->getLastName(); ?><br />
			Phone: <?php echo $user->getPhone(); ?><br />
			Email: <?php echo $user->getEmail(); ?><br />
			Username: <?php echo $user->getUsername(); ?><br />
			Password: <?php for($i = 0; $i < strlen($user->getPassword()); $i++) { echo "*"; } ?><br />
			<?php echo $customFields; ?>
			</div>
		</div>
		<div id='mm-myaccount-billing'>
			<div  id='mm-myaccount-billing-title'>
				<img src="<?php echo MM_Utils::getImageUrl("creditcards") ?>" style="vertical-align: middle; padding-right: 4px;" /> 
				Billing Information <div  class='mm-myaccount-title-right'>
			<?php if($user->getStatus() == MM_MemberStatus::$ACTIVE || $user->getStatus() == MM_MemberStatus::$OVERDUE){ ?>	
				<a href='<?php echo $billingLink; ?>'>Edit</a>
			<?php } ?></div>
			</div>
			<div id='mm-myaccount-billing-body'>
				Address: <?php echo $user->getBillingAddress(); ?><br />
				City: <?php echo $user->getBillingCity(); ?><br />
				State: <?php echo $user->getBillingState(); ?><br />
				Zip: <?php echo $user->getBillingZipCode(); ?><br />
				Country: <?php echo $user->getBillingCountryName(); ?><br /><br />
			<?php echo $orderInfo; ?>
			</div>
		</div>
		<div id='mm-myaccount-shipping'>
			<div  id='mm-myaccount-shipping-title'>
				<img src="<?php echo MM_Utils::getImageUrl("lorry") ?>" style="vertical-align: middle; padding-right: 4px;" /> 
				Shipping Information
				
				<div  class='mm-myaccount-title-right'>
				
			<?php if($user->getStatus() == MM_MemberStatus::$ACTIVE || $user->getStatus() == MM_MemberStatus::$OVERDUE){ ?>	
					<a href='<?php echo $shippingLink; ?>'>Edit</a>
			<?php } ?></div>
				</div>
			<div id='mm-myaccount-shipping-body'>
				Address: <?php echo $user->getShippingAddress(); ?><br />
				City: <?php echo $user->getShippingCity(); ?><br />
				State: <?php echo $user->getShippingState(); ?><br />
				Zip: <?php echo $user->getShippingZipCode(); ?><br />
				Country: <?php echo $user->getShippingCountryName(); ?><br />
	
			</div>
		</div>
		<div style='clear:both; height: 10px;'></div>
		<div id='mm-myaccount-history'>
			<div  id='mm-myaccount-history-title'>
				<img src="<?php echo MM_Utils::getImageUrl("cart") ?>" style="vertical-align: middle; padding-right: 4px;" /> 
				Order History : Most recent orders <a href='<?php echo $orderHistoryLink; ?>'>View All</a></div>
			<div id='mm-myaccount-history-body'>
			<?php echo $orderHistoryStr; ?>
			</div>
		</div>
	</div>
	<?php } ?>
<script type="text/javascript">
<?php echo $notLoggedInMsg; ?>

myAccountJs.resizeBoxes();
</script>