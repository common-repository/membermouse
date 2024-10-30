<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$registration = new MM_RegistrationView();
$data = $registration->getData('step2');
$misc = $registration->getDataInSession();
$mt = new MM_MemberType($misc["mm_order_member_type"]);
$isFree = $mt->isFree();
$email = $data->getEmail();
$password = $data->getPassword();
$username = $data->getUsername();

$terms = MM_OptionUtils::getOption(MM_OPTION_TERMS_CONTENT);
$status = MM_OptionUtils::getOption(MM_OPTION_TERMS_STATUS);

$showNotice = false;
$options = MM_CampaignOptions::getOptions("payment", true);
if(is_array($options) && (array_search("PayPal", $options)!==false || array_search("ClickBank", $options)!==false)){
	$showNotice = true;	
}

$requireTermsAndConditions = (bool)$status;
?>
<tr>
			<td width='180px'>Username</td>
			<td colspan='2'><input id="mm-order-username" type="text" class="medium-text" value='<?php echo $username; ?>' /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td colspan='2'><input id="mm-order-password" type="password" class="medium-text"  value='<?php echo $password; ?>' /></td>
		</tr>
		<tr>
			<td>Confirm Password</td>
			<td colspan='2'><input id="mm-order-password-confirm" type="password" class="medium-text" value='<?php echo $password; ?>'  /></td>
		</tr>
		<tr>
			<td>Email</td>
			<td colspan='2'>
				<input id="mm-order-email" type="text" class="medium-text"  value='<?php echo $email; ?>' />
				<div style='clear:both; height: 10px;'></div>
				<?php if($showNotice){ ?>
				<span class='mm-email-notice'>Your email must match your PayPal or ClickBank email</span>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>Confirm Email</td>
			<td colspan='2'><input id="mm-order-email-confirm" type="text" class="medium-text"  value='<?php echo $email; ?>' />
			<?php if($isFree=='1'){ ?>

				<input type='hidden' id='mm_order_first_name' value='' />
				<input type='hidden' id='mm_order_last_name' value='' />
				<input type='hidden' id='mm_order_billing_address' value='' />
				<input type='hidden' id='mm_order_billing_city' value='' />
				<input type='hidden' id='mm_order_billing_state' value='' />
				<input type='hidden' id='mm_order_billing_zip' value='' />
				<input type='hidden' id='mm_order_billing_country' value='' />
				<input type='hidden' id='mm_order_phone' value='' />
				<input type='hidden' id='mm_order_payment_method' value='' />
				<input type='hidden' id='mm_order_cc_number' value='' />
				<input type='hidden' id='mm_order_cc_exp_month' value='' />
				<input type='hidden' id='mm_order_cc_exp_year' value='' />
				<input type='hidden' id='mm_order_cc_security_code' value='' />
				<input type='hidden' id='mm_order_shipping_method' value='' />
				<input type='hidden' id='mm_cb_order_shipping_same_as_billing' value='' />
				<input type='hidden' id='mm_order_shipping_same_as_billing' value='' />
				<input type='hidden' id='mm_order_shipping_address' value='' />
				<input type='hidden' id='mm_order_shipping_city' value='' />
				<input type='hidden' id='mm_order_shipping_state' value='' />
				<input type='hidden' id='mm_order_shipping_zip' value='' />
				<input type='hidden' id='mm_order_shipping_country' value='' />
				<input type='hidden' id='mm_order_member_type' value='<?php echo $mt->getId(); ?>' />
		<?php } ?>
<?php echo MM_RegistrationView::getHiddenStepsHtml('step2'); ?>
<input type='hidden' id='mm_has_terms' value='<?php echo (($requireTermsAndConditions)?'1':'0'); ?>' />
<input type='hidden' id='is_free' value='<?php echo $isFree; ?>' />
			</td>
		</tr>
		<?php if($requireTermsAndConditions){ ?>
		<tr>
			<td colspan='3'>
				<div style='background-color: white; border: 1px solid #999999; height: 200px; margin-top: 10px; overflow: auto; padding: 10px; width: 600px; '>
					<?php echo nl2br($terms); ?>
				</div>
				<input type='checkbox' id='mm-agree' name='mm-agree' /> I accept the Terms and Conditions
				<div style='margin-top: 10px;'></div>
			</td>
		</tr>
		<?php } ?>
		<?php if(isset($_GET["member_type_id"])){?>
		<script type='text/javascript'>
		mmjs.showStepProgress(2);
		mmjs.setFree('<?php echo $isFree; ?>');
		</script>
		<?php } ?>
		