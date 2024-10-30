<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$registration = new MM_RegistrationView();
$session = $registration->getDataInSession();
$data = $registration->getData('step2');
$mt = new MM_MemberType($session["mm_order_member_type"]);
$isFree = $mt->isFree();

$email = $data->getEmail();
$password = $data->getPassword();
$username = $data->getUsername();

$terms = MM_OptionUtils::getOption(MM_OPTION_TERMS_CONTENT);
$status = MM_OptionUtils::getOption(MM_OPTION_TERMS_STATUS);

$requireTermsAndConditions = (bool)$status;
//echo "<pre>";
//var_dump($session);
//echo "</pre>";
?>
<tr>
<td colspan='3'>
<input type='hidden' id='is_free' value='<?php echo $isFree; ?>' />
<?php if($isFree=='1'){ ?>

				<input type='hidden' id='mm_order_username' value='<?php echo $username; ?>' />
				<input type='hidden' id='mm_order_password' value='<?php echo $password; ?>' />
				<input type='hidden' id='mm_order_email' value='<?php echo $email; ?>' />
				
				
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
<?php echo MM_RegistrationView::getHiddenStepsHtml('additional'); ?>
<table width='100%'>
<?php 
$jsVars = "";
$fields = MM_CustomField::getCustomFields(0, true);
?>
	<?php 
		foreach($fields as $field){
			$field->value = "";
			$id = $field->id;
			if(isset($session["mm_custom_".$id])){
				$field->value = $session["mm_custom_".$id];
			}
			if($field->is_required=='1'){
				$jsVars .= "requiredFields[\"mm_custom_".$id."\"] = '".$field->field_label."';\n";
			}
		?>
			<tr>
				<td width='220px'>
					<?php echo $field->field_label; ?>
				</td>
				<td>
					<input type='text' style='width: 250px;' name='mm_custom_<?php echo $field->id; ?>' id='mm_custom_<?php echo $field->id; ?>' value="<?php echo $field->value; ?>" />
				</td>
			</tr>
		<?php 	
		}
	?></table>
	<script type='text/javascript'>
	var requiredFields = new Array();
	<?php echo $jsVars; ?>
	</script>
	</td>
	</tr>
