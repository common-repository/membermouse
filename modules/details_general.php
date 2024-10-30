<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$canChangeDaysCalc = true;
$affiliateData = "";
$affiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE);
$subAffiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_SUB_AFFILIATE);
if(isset($_REQUEST[MM_Session::$PARAM_USER_ID])) {
	$user = new MM_User($_REQUEST[MM_Session::$PARAM_USER_ID]);
	$memberTypeId = $user->getMemberTypeId();
	$memberType = new MM_MemberType($memberTypeId);
	$productId = $memberType->getRegistrationProduct();
	$assoc = $user->getAffiliateAssociations($productId);

	if(count($assoc)>0){
		$obj = $assoc[0];
		
		$affiliateData = "<table>";
		if(isset($obj["affiliate_id"])){
			$affiliateData .= "<tr><td width='125px'>".$affiliateId."</td><td><input type='text' id='mm-affiliate-id-edit' value='".$obj["affiliate_id"]."' /></td></tr>";
		}
		if(isset($obj["sub_affiliate_id"])){
			$affiliateData .= "<tr><td>".$subAffiliateId."</td><td><input type='text'id='mm-sub-affiliate-id-edit'  value='".$obj["sub_affiliate_id"]."' /></td></tr>";
		}
		$affiliateData .= "</table>";
	}
	else{
		$affiliateData = "<table>";
		$affiliateData .= "<tr><td width='125px'>".$affiliateId."</td><td><input type='text' id='mm-affiliate-id-edit' value='' /></td></tr>";
		$affiliateData .= "<tr><td>".$subAffiliateId."</td><td><input type='text'id='mm-sub-affiliate-id-edit'  value='' /></td></tr>";
		$affiliateData .= "</table>";
	}
	
	if($user->isValid()) {
		include_once MM_MODULES."/details.header.php";
		
		if($user->getStatus() == MM_MemberStatus::$PAUSED){
			$canChangeDaysCalc = false;
		}
		
		$customDateSelected = "";
		$fixedSelected = "";
		$joinDateSelected = "";	
		$customDateValue = "";
		$fixedValue = "";
		$calcMethod = MM_DaysCalculationTypes::$JOIN;
		switch($user->getDaysCalcMethod()){
			case MM_DaysCalculationTypes::$CUSTOM:
				$calcMethod = MM_DaysCalculationTypes::$CUSTOM;
				$customDateValue  = $user->getDaysCalcValue();
				$customDateSelected = "checked";
				break;
			case MM_DaysCalculationTypes::$FIXED:
				$calcMethod = MM_DaysCalculationTypes::$FIXED;
				$fixedValue = $user->getDaysCalcValue();
				$fixedSelected = "checked";
				break;
			default:
				$joinDateSelected = "checked";
				break;
		}
		 
?>
	<script type='text/javascript'>
		mmJQuery(document).ready(function(){
			mmJQuery("#mm-custom-date").datepicker();
		});
	</script>
<div id="mm-form-container">
	<table cellspacing="8">
		<tr>
			<td width="120">Member ID</td>
			<td>
				<?php echo $user->getId() ?> 
				<input id="mm_id" type="hidden" value="<?php echo $user->getId(); ?>" />
			</td>
		</tr>
		<?php if(!empty($affiliateData)){ ?>
		<tr><td colspan="2"></td></tr>
		<tr>
			<td width="120">Affiliate Info</td>
			<td>
			<?php echo $affiliateData; ?>
				
			</td>
		</tr>
		<?php } ?>
		<tr><td colspan="2"></td></tr>
		<tr>
			<td>Member Status</td>
			<td>
			<?php 
				$statusDesc = MM_MemberStatus::getName($user->getStatus());
				
				echo MM_MemberStatus::getImage($user->getStatus()); 
				
				if($user->isActive()) 
				{
					if(!$user->isFree())
					{
						echo ' <img src="'.MM_Utils::getImageUrl("money").'" style="vertical-align:middle" title="Paid Member" />';
						$statusDesc .= " / Paying Member";
					}
					else {
						echo ' <img src="'.MM_Utils::getImageUrl("no_money").'" style="vertical-align:middle" title="Free Member" />';
						$statusDesc .= " / Free Member";
					}
				}
				
				if($user->hasCardOnFile())
				{
					echo ' <img src="'.MM_Utils::getImageUrl("creditcards").'" style="vertical-align:middle" title="Card on File" />';
					$statusDesc .= " / Has Card on File";
				}
				else {
					$statusDesc .= " / No Card on File";
				}
				
				echo '<span style="margin-left: 10px; font-size: 10px">'.$statusDesc.'</span>';
			?>
			</td>
		</tr>
		<tr><td colspan="2"></td></tr>
		<?php if(!$user->isFree() && MM_Utils::isLimeLightInstall()) { ?>
		<tr>
			<td>Lime Light Utilities</td>
			<td>
				<span style="font-size: 11px">
					<a href="<?php echo MM_LimeLightUtils::getLLOrderUrl($user->getLastOrderId()) ?>" target="_blank" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('cart'); ?>" /> View Order</a>
					| <a href="<?php echo MM_LimeLightUtils::getLLCustomerUrl($user->getCustomerId()) ?>" target="_blank" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('user'); ?>" /> View Customer</a>
				</span>
			</td>
		</tr>
		<tr><td colspan="2"></td></tr>
		<?php } ?>
		<tr>
			<td>First Name</td>
			<td><input id="mm-first-name" type="text" style="width:200px;" value="<?php echo $user->getFirstName() ?>"></td>
		</tr>
		<tr>
			<td>Last Name</td>
			<td><input id="mm-last-name" type="text" style="width:200px;" value="<?php echo $user->getLastName() ?>"></td>
		</tr>
		<tr>
			<td>Username</td>
			<td><input id="mm-username" type="text" style="width:200px;" value="<?php echo $user->getUsername() ?>"></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input id="mm-email" type="text" style="width:200px;" value="<?php echo $user->getEmail() ?>"></td>
		</tr>
		<tr>
			<td>Phone</td>
			<td><input id="mm-phone" type="text" style="width:200px;" value="<?php echo $user->getPhone() ?>"></td>
		</tr>
		<tr>
			<td>IP Address</td>
			<td><a href="http://www.infosniper.net/index.php?ip_address=<?php echo $user->getIpAddress() ?>&map_source=1&two_maps=1&overview_map=1" target="_blank"><?php echo $user->getIpAddress() ?></a></td>
		</tr>
		<tr>
			<td>Notes</td>
			<td>
				<textarea id="mm-notes" class="long-text" rows="4" style="font-size:11px;"><?php echo $user->getNotes() ?></textarea>
			</td>
		</tr>
		
	</table>
	
	<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 
	
	<div style="margin-top:8px">
		<table cellspacing="8" width='600px' >
				 <?php if(!$canChangeDaysCalc){ ?>
				<tr>
					<td colspan='2'>
						<div style='width: 600px;'><span style='color:red;'><img src='<?php echo MM_Utils::getImageUrl("exclamation"); ?>' style='vertical-align: middle; '/> You can modify the number of days this member is fixed at, but to change the calculation method you must change the member's status to Active.</span></div>
					</td>
				</tr>
				 <?php } ?>
		<tr>
			<td width='125px'>'Days as Member' Calculation Method</td>
			<td>
				<input type='radio' <?php echo ((!$canChangeDaysCalc)?"disabled='disabled'":""); ?> onchange="mmjs.setCalcMethod('join_date');" id='mm-calc-method-reg-date' <?php echo $joinDateSelected; ?> name='mm-calc-method' /> By join date<br />
				<input type='radio' <?php echo ((!$canChangeDaysCalc)?"disabled='disabled'":""); ?> onchange="mmjs.setCalcMethod('custom_date');" id='mm-calc-method-custom-date'  <?php echo $customDateSelected; ?> name='mm-calc-method' /> By custom date 
				
						<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
						<input <?php echo ((!$canChangeDaysCalc)?"disabled='disabled'":""); ?> id="mm-custom-date" type="text" style="width: 152px" value="<?php echo $customDateValue; ?>" /> 
				<br />
				<input type='radio' onchange="mmjs.setCalcMethod('fixed');" id='mm-calc-method-fixed'  <?php echo $fixedSelected; ?>  name='mm-calc-method' /> Fixed at <input id="mm-fixed" type="text" value="<?php echo $fixedValue; ?>"  style="width: 52px" /> days <br />
				 <input type='hidden' id='mm-calc-method' value="<?php echo $calcMethod; ?>" />
				 
			</td>
		</tr>
	</table>
	
	
	<div style="width: 600px; margin-top: 8px; margin-bottom: 8px;" class="mm-divider"></div> 
		<table>
			<tr>
				<td width="110">Reset Password</td>
				<td>
					<table cellspacing="0">
						<tr>
							<td width="114">New Password</td>
							<td><input id="mm-new-password" type="password"></td>
						</tr>
						<tr>
							<td>Confirm Password</td>
							<td><input id="mm-confirm-password" type="password"></td>
						</tr>
						<tr>
							<td colspan='2'>
								<img src="<?php echo MM_Utils::getImageUrl('email_go'); ?>" style='vertical-align:middle;' /> <a style='cursor: pointer;' onclick="mmjs.sendPasswordEmail('<?php echo $user->getId(); ?>');">Send Password Email</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>

<input type="button" class="button-primary" value="Update Member" onclick="mmjs.updateMember(<?php echo $user->getId(); ?>);">
<?php 
	}
	else {
		echo "<div style=\"margin-top:10px;\"><i>Invalid user id.</i></div>";
	}
}
