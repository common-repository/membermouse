<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
global $current_user;
if(!isset($_SESSION)){
	session_start();
}
$readonly = true;
$user = new MM_User($current_user->ID);
if(is_admin() && isset($userId)){
	$user = new MM_User($userId);
}
if($user->getStatus() == MM_MemberStatus::$ACTIVE || $user->getStatus() == MM_MemberStatus::$OVERDUE){ 
	$readonly = false;
}	
$message = "";
$errors = "";
if(isset($_POST["mm_myaccount_billing_address"])){
	$user->setBillingAddress($_POST["mm_myaccount_billing_address"]);
	$user->setBillingCity($_POST["mm_myaccount_billing_city"]);
	$user->setBillingState($_POST["mm_myaccount_billing_state"]);
	$user->setBillingZipCode($_POST["mm_myaccount_billing_zip"]);
	/*
	 * mm_expiration_month,mm_expiration_year,mm_credit_card_type,mm_credit_card_number
	 */
	$creditCard = "";
	
	$response = new MM_Response();
	$memberType = new MM_MemberType($user->getMemberTypeId());
	if(!$memberType->isFree()){
		if(isset($_POST["mm_myaccount_billing_country"])){
			$user->setBillingCountry($_POST["mm_myaccount_billing_country"]);
			
			if(!preg_match("/(\*)/", $_POST["mm_credit_card_number"])){
				$creditCard = $_POST["mm_credit_card_number"];
			}
		}
		if(MM_OptionUtils::getOption("mm-install_type") == MM_Site::$INSTALL_TYPE_LIMELIGHT){
			$response = MM_LimeLightService::updateBillingInfo($user, $creditCard, $_POST["mm_credit_card_type"], $_POST["mm_expiration_month"], $_POST["mm_expiration_year"]);
			
			if($user->getStatus() == MM_MemberStatus::$OVERDUE){
				if(!($response instanceof MM_Response) || ($response instanceof MM_Response && $response->type != MM_Response::$ERROR)){
					$memberDetails = new MM_MemberDetailsView();
					
					$user->doUpdateLL = false;
					$post = array(
						'mm_id'=>$user->getId(),
						'mm_order_id'=> $user->getLastOrderId(false),
					);
					$response = $memberDetails->activateMembership($post);
					
					if($response instanceof MM_Response){
						if($response->type == MM_Response::$SUCCESS){
							$message = "Thank you for updating your billing information. Your account is now active.";
						}
					}
				}
			}
		}
		else{
			$user->doUpdateLL = false;
		}
	}
	
	if($response instanceof MM_Response){
		if($response->type == MM_Response::$ERROR){
			$errors .= $response->message;
		}
		else{
			if(strlen($creditCard)>4){
 				$user->setLastFour(substr($creditCard,-4));
				$user->commitData();
			}
			$response = $user->commitData();
 			
			if($response->type == MM_Response::$ERROR){
				$errors .= $response->message;
			}
		}
	}
	
}

if(MM_Utils::isAdmin()){
	$readonly = true;	
}

$showPayment=false;
$campaignId = 0;
$creditCardType = "";
$creditCardNumber = "";
$creditExpirationMonth = "";
$creditExpirationYear = "";
if(!$user->isValid()){
	$readonly = true;	
}
else{
	$memberTypeId = $user->getMemberTypeId();
	$memberType = new MM_MemberType($memberTypeId);
	if(!$memberType->isFree()){
		$productId = $memberType->getRegistrationProduct();
		$product = new MM_Product($productId);
		$campaignId = $product->getCampaignId();
		
		if(MM_OptionUtils::getOption("mm-install_type") == MM_Site::$INSTALL_TYPE_LIMELIGHT){
			$orderView = MM_LimeLightService::getOrder($user->getLastOrderId(false));
			if(!($orderView instanceof MM_Response)){
				$creditCardType = $orderView["cc_type"];
				$creditCardNumber = str_pad($orderView["cc_number"], 15, "*", STR_PAD_LEFT);
				$creditExpiration = $orderView["cc_expires"];
				$creditExpirationMonth = substr($creditExpiration, 0,2);
				$creditExpirationYear = substr($creditExpiration, 2);
			}	
		}
		$showPayment=true;
	}
}
$campaignListHtml = MM_HtmlUtils::getCampaignCountryList($campaignId, $user->getBillingCountry());
$creditCardTypes  = "";
if(empty($campaignListHtml)){
	$options = MM_CampaignOptions::getOptions("country");
	$campaignListHtml = MM_HtmlUtils::generateSelectionsList($options, $user->getBillingCountry());
$showPayment = false;
	
}
else{
	$creditCardTypes = MM_HtmlUtils::getCampaignPaymentList($campaignId, $creditCardType);
}

$allMessages = "";
if(!empty($message)){
	$allMessages .= $message." ";
}
if(!empty($errors)){
	$allMessages .= $errors;
}

if(MM_Utils::isAdmin() && is_admin()){
	$readonly= false;
}
?>
<form method='post'>

<div class='mm-myaccount-error'><?php echo $errors; ?></div>
<table cellspacing="8" id='mm-subpage-billing-details'  class='mm-myaccount-details-table'>
		<tr>
			<td width='140'><span class='mm-subpage-labels'>Address</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_billing_address" type="text" class="medium-text"  value="<?php echo $user->getBillingAddress(); ?>"/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>City</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_billing_city" type="text" class="medium-text"  value="<?php echo $user->getBillingCity(); ?>"/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>State</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_billing_state" type="text" class="medium-text"  value="<?php echo $user->getBillingState(); ?>"/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>Zip Code</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_billing_zip" type="text" class="medium-text"  value="<?php echo $user->getBillingZipCode(); ?>"/></td>
		</tr>
		<?php if($showPayment){ ?>
		<tr>
			<td><span class='mm-subpage-labels'>Country</span></td>
			<td><select <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_billing_country"><?php echo $campaignListHtml ?></select></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan='2'>
				<span class='mm-subpage-title'>Credit Card Information</span>
			</td>
		</tr>
		<?php if(!empty($creditCardTypes)){ ?>
		<tr> 
			<td><span class='mm-subpage-labels'>Card Type</span></td>
			<td>
				<select <?php echo (($readonly)?"disabled='disabled'":""); ?> name='mm_credit_card_type'><?php echo $creditCardTypes; ?></select>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td><span class='mm-subpage-labels'>Card Number</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> type='text' name='mm_credit_card_number' maxlength='16' value='<?php echo $creditCardNumber; ?>' /></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>Exp. Date</span></td>
			<td>
				<select <?php echo (($readonly)?"disabled='disabled'":""); ?> name='mm_expiration_month'>
					<?php echo MM_HtmlUtils::getCCExpMonthList($creditExpirationMonth); ?>
				</select>
				<select <?php echo (($readonly)?"disabled='disabled'":""); ?> name='mm_expiration_year'>
					<?php echo MM_HtmlUtils::getCCExpYearList($creditExpirationYear); ?>
				</select>
			</td>
		</tr>
		<?php } ?>
		<?php if(!$readonly){?>
		<tr>
			<td colspan='2'>
				
				<?php if(!is_admin()){?>
					<input type='submit' <?php echo (($readonly)?"disabled='disabled'":""); ?> class="button-secondary"  name='mm-myaccount-submit' name='mm-myaccount-submit' value='Save'     />
	<?php }else{ 
				?>
				<input type='submit' <?php echo (($readonly)?"disabled='disabled'":""); ?> class="button-primary"  name='mm-myaccount-submit' name='mm-myaccount-submit' value='Save Billing Information'  style='width: 150px;'   />
				<?php 
				}?>
			</td>
		</tr>
		<?php } ?>
</table></form>
<script type='text/javascript'>
<?php 
if(!empty($allMessages)){
 ?>
 alert("<?php echo $allMessages; ?>");
<?php } ?>
</script>