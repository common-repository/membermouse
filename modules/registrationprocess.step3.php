<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.

http://membermouse.localhost/?
	txn_type=subscr_signup&
	subscr_id=I-NSP0BUUUJW0A&
	last_name=Young&
	residence_country=US&
	mc_currency=USD&
	item_name=Paypal+Membership&
	business=eric.popfizz%40gmail.com&
	amount3=1.45&
	recurring=0&
	verify_sign=AQU0e5vuZCvSg-XJploSa.sGUDlpARVo9Tmfhb.FzXs2PmUcNXbm9LTq&
	payer_status=verified&
	payer_email=paypal%40gothosting.org&
	first_name=Matthew&
	receiver_email=eric.popfizz%40gmail.com&
	payer_id=8FEP4AHFPWSMA&
	reattempt=1&
	item_number=9&
	payer_business_name=Got+Hosting&
	subscr_date=13%3A49%3A15+Apr+07%2C+2011+PDT&custom=0&
	charset=windows-1252&
	notify_version=3.1&
	period3=1+W&
	mc_amount3=1.45&
	auth=aGqyKZKpw6-6D6_ih4bx0y12kRafdIE3JhH-Vr3XH5fbARKemyTHPLo3qbKfWgTNlETluC-tZSXW8sWc2ujhbS_xAQReCQmPgfRc5KnVxbWViRBlO32Tct1HekqQU0NO-S2To0QZklQpRpTrwte4miSRk8I7_6xKt9P_CoJXyXP7jydgB9vvNQ2gmNEDrVNMzzdJR10E1OZ7kKDmtx7qTH_2Jxgcu21NHBDB-0&
	form_charset=UTF-8
 */
global $mmSite;
$registration = new MM_RegistrationView();
$data = $registration->getData('step3');
$misc = $registration->getDataInSession();

//// determine if it is even shippable.
$memberType = new MM_MemberType($misc["mm_order_member_type"]);
$productId = $memberType->getRegistrationProduct();
if(isset($misc["mm_order_product_id"]) && intval($misc["mm_order_product_id"])>0){ 
	$productId = $misc["mm_order_product_id"];
}
$isShippable = false;

$campaignId=0;
$product = new MM_Product($productId);
if($productId>0)
{
	$isShippable = (bool)$product->isShippable();
	$campaignId = $product->getCampaignId();	
}

$campaignListHtml = "";
$campaignListHtml2 = "";
if(MM_Utils::isLimeLightInstall()){
	$campaignListHtml = MM_HtmlUtils::getCampaignCountryList($campaignId, $data->getBillingCountry());
	$campaignListHtml2 = MM_HtmlUtils::getCampaignCountryList($campaignId, $data->getShippingCountry());
}

if(empty($campaignListHtml)){
	$options = MM_CampaignOptions::getOptions("country");
	$campaignListHtml = MM_HtmlUtils::generateSelectionsList($options, $data->getBillingCountry());
	$campaignListHtml2 = MM_HtmlUtils::generateSelectionsList($options, $data->getShippingCountry());
}

$displayShippingStateDropDown = false;
$displayBillingStateDropDown = false;
if($data->getBillingCountry() == MM_LimeLightUtils::$COUNTRY_ID_US){
	$displayBillingStateDropDown = true;	
}
if($data->getShippingCountry() == MM_LimeLightUtils::$COUNTRY_ID_US){
	$displayShippingStateDropDown = true;	
}


$addt = array('mm_order_cc_exp_month','mm_order_shipping_same_as_billing', 'mm_order_shipping_method','mm_order_cc_exp_year', 'mm_order_cc_number', 'mm_order_payment_method','mm_order_cc_security_code');

$sameAsBilling = false;
if(!isset($misc["mm_order_shipping_same_as_billing"]) || (isset($misc["mm_order_shipping_same_as_billing"]) && $misc["mm_order_shipping_same_as_billing"] == "YES"))
{ 
	$sameAsBilling = true;
} 
foreach($addt as $field)
{
	if(!isset($misc[$field]))
	{
		$misc[$field] = "";
	}
}

$selected = "";
if(isset($misc["mm_order_payment_choice"])){
	$selected = $misc["mm_order_payment_choice"];
}

$paymentOption = new MM_CampaignOptions($selected);
$options = MM_CampaignOptions::getOptions("payment", true);
$paymentOptions = MM_HtmlUtils::generateSelectionsList($options, $selected);

if(!isset($misc["mm_order_shipping_method"])){
	$misc["mm_order_shipping_method"]="";
}

?>	
	<tr><td colspan='3'>
<?php echo MM_RegistrationView::getHiddenStepsHtml('step3'); ?>
		</td>
	</tr>
<tr>
<td colspan='3'>
<script type='text/javascript'>
<?php if(MM_Utils::isLimeLightInstall()){ ?>
mmJQuery("#mm-payment-options").show();
<?php } ?>

</script>
	<table cellspacing="10">
		<tr>
			<td width="140">First Name</td>
			<td><input id="mm-order-first-name" type="text" class="medium-text" value="<?php echo $data->getFirstName(); ?>" /></td>
		</tr>
		<tr>
			<td>Last Name</td>
			<td><input id="mm-order-last-name" type="text" class="medium-text"  value="<?php echo $data->getLastName(); ?>"/></td>
		</tr>
		<tr>
			<td>Billing Address</td>
			<td><input id="mm-order-billing-address" type="text" class="medium-text"  value="<?php echo $data->getBillingAddress(); ?>"/></td>
		</tr>
		<tr>
			<td>Billing Country</td>
			<td><select id="mm-order-billing-country"  onchange="mmjs.toggleStateList('<?php echo MM_LimeLightUtils::$COUNTRY_ID_US; ?>','billing'); "><?php echo $campaignListHtml ?></select></td>
		</tr>
		<tr>
			<td>Billing City</td>
			<td><input id="mm-order-billing-city" type="text" class="medium-text"  value="<?php echo $data->getBillingCity(); ?>"/></td>
		</tr>
		<tr>
			<td>Billing State</td>
			<td>
				<input id="mm-order-billing-state-txt" onkeyup="mmjs.updateBillingState('txt')" type="text" class="medium-text"  value="<?php echo $data->getBillingState(); ?>" style="<?php echo (($displayBillingStateDropDown)?"display:none;":""); ?>" />
			
			<input type='hidden' id='mm-order-billing-state' value='<?php echo $data->getBillingState(); ?>' />
				<select id="mm-order-billing-state-sel" onchange="mmjs.updateBillingState('sel')" size="1" style='<?php echo ((!$displayBillingStateDropDown)?"display:none;":""); ?>' >


	<option value="AL" onclick="" >Alabama</option>

	<option value="AK" onclick="" >Alaska</option>

	<option value="AS" onclick="" >American Samoa</option>

	<option value="AZ" onclick="" >Arizona</option>

	<option value="AR" onclick="" >Arkansas</option>

	<option value="AE" onclick="" >Armed Forces Africa</option>

	<option value="AA" onclick="" >Armed Forces Americas</option>

	<option value="AE" onclick="" >Armed Forces Canada</option>

	<option value="AE" onclick="" >Armed Forces Europe</option>

	<option value="AE" onclick="" >Armed Forces Middle East</option>

	<option value="AP" onclick="" >Armed Forces Pacific</option>

	<option value="CA" onclick="" >California</option>

	<option value="CO" onclick="" >Colorado</option>

	<option value="CT" onclick="" >Connecticut</option>

	<option value="DE" onclick="" >Delaware</option>

	<option value="DC" onclick="" >District of Columbia</option>

	<option value="FM" onclick="" >Federated States Of Micronesia</option>

	<option value="FL" onclick="" >Florida</option>

	<option value="GA" onclick="" >Georgia</option>

	<option value="GU" onclick="" >Guam</option>

	<option value="HI" onclick="" >Hawaii</option>

	<option value="ID" onclick="" >Idaho</option>

	<option value="IL" onclick="" >Illinois</option>

	<option value="IN" onclick="" >Indiana</option>

	<option value="IA" onclick="" >Iowa</option>

	<option value="KS" onclick="" >Kansas</option>

	<option value="KY" onclick="" >Kentucky</option>

	<option value="LA" onclick="" >Louisiana</option>

	<option value="ME" onclick="" >Maine</option>

	<option value="MH" onclick="" >Marshall Islands</option>

	<option value="MD" onclick="" >Maryland</option>

	<option value="MA" onclick="" >Massachusetts</option>

	<option value="MI" onclick="" >Michigan</option>

	<option value="MN" onclick="" >Minnesota</option>

	<option value="MS" onclick="" >Mississippi</option>

	<option value="MO" onclick="" >Missouri</option>

	<option value="MT" onclick="" >Montana</option>

	<option value="NE" onclick="" >Nebraska</option>

	<option value="NV" onclick="" >Nevada</option>

	<option value="NH" onclick="" >New Hampshire</option>

	<option value="NJ" onclick="" >New Jersey</option>

	<option value="NM" onclick="" >New Mexico</option>

	<option value="NY" onclick="" >New York</option>

	<option value="NC" onclick="" >North Carolina</option>

	<option value="ND" onclick="" >North Dakota</option>

	<option value="MP" onclick="" >Northern Mariana Islands</option>

	<option value="OH" onclick="" >Ohio</option>

	<option value="OK" onclick="" >Oklahoma</option>

	<option value="OR" onclick="" >Oregon</option>

	<option value="PA" onclick="" >Pennsylvania</option>

	<option value="PR" onclick="" >Puerto Rico</option>

	<option value="RI" onclick="" >Rhode Island</option>

	<option value="SC" onclick="" >South Carolina</option>

	<option value="SD" onclick="" >South Dakota</option>

	<option value="TN" onclick="" >Tennessee</option>

	<option value="TX" onclick="" >Texas</option>

	<option value="UT" onclick="" >Utah</option>

	<option value="VT" onclick="" >Vermont</option>

	<option value="VI" onclick="" >Virgin Islands</option>

	<option value="VA" onclick="" >Virginia</option>

	<option value="WA" onclick="" >Washington</option>

	<option value="WV" onclick="" >West Virginia</option>

	<option value="WI" onclick="" >Wisconsin</option>

	<option value="WY" onclick="" >Wyoming</option></select> 
			
			</td>
		</tr>
		<tr>
			<td>Billing Zip Code</td>
			<td><input id="mm-order-billing-zip" type="text" class="medium-text"  value="<?php echo $data->getBillingZipCode(); ?>"/></td>
		</tr>
		<tr>
			<td>Phone</td>
			<td><input id="mm-order-phone" type="text" class="medium-text"   value="<?php echo $data->getPhone(); ?>"/></td>
		</tr>
		<?php if(!MM_Utils::isLimeLightInstall()){ ?>
		<tr>
			<td>Payment Method</td>
			<td>
				<select id="mm-order-payment-choice" onchange="mmjs.getGateway();">
					<?php echo $paymentOptions; ?>
				</select>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<div style="width: 100%;" class="mm-divider"></div>
	
	<table cellspacing="10" id="mm-payment-options" style='display:none;'>
		<tr>
			<td>Payment Method</td>
			<td>
				<select id="mm-order-payment-method">
					<?php echo MM_HtmlUtils::getCampaignPaymentList($campaignId, $misc["mm_order_payment_method"]); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Card Number</td>
			<td><input id="mm-order-cc-number" type="text" class="medium-text" value="<?php echo $misc["mm_order_cc_number"]; ?>" maxlength="16" /></td>
		</tr>
		<tr>
			<td>Exp. Date</td>
			<td>
				<select id="mm-order-cc-exp-month">
					<?php echo MM_HtmlUtils::getCCExpMonthList($misc["mm_order_cc_exp_month"]); ?>
				</select>
				<select id="mm-order-cc-exp-year">
					<?php echo MM_HtmlUtils::getCCExpYearList($misc["mm_order_cc_exp_year"]); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Security Code</td>
			<td><input id="mm-order-cc-security-code" type="password" class="short-text" value="<?php echo $misc["mm_order_cc_security_code"]; ?>" /></td>
		</tr>
	</table>
	
	<div style="width: 100%;" class="mm-divider"></div>
	
	<table cellspacing="10" id='mm-is-shippable' <?php if(!$isShippable){ ?>style='display:none;' <?php }?>>
		<tr>
			<td width="140">Shipping Method</td>
			<td>
				<select id="mm-order-shipping-method">
					<?php 
						if(!MM_Utils::isLimeLightInstall()){ 
							$shippingOptions = MM_CampaignOptions::getOptions("shipping");
							$shipping = MM_HtmlUtils::generateSelectionsList($shippingOptions, $misc["mm_order_shipping_method"]);
							echo $shipping;
						}
						else{
							$shipping = MM_HtmlUtils::getCampaignShippingList($campaignId, $misc["mm_order_shipping_method"]);
							echo $shipping;
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input id="mm-cb-order-shipping-same-as-billing" type="checkbox" <?php echo (($sameAsBilling)?"checked":""); ?>  onclick="mmjs.processForm()" />
				Shipping is the same as billing
				
				
				<input id="mm-order-shipping-same-as-billing" type="hidden" value='<?php echo (($sameAsBilling)?"YES":"NO"); ?>' />
			</td>
		</tr>
	</table>
	
			<table cellspacing="10" id="mm-order-shipping-address-form" <?php if(!$isShippable){ ?>style='display:none;' <?php }else{ ?> style="display:none;"<?php } ?>>
				<tr>
					<td width="140">Shipping Address</td>
					<td><input id="mm-order-shipping-address" type="text" class="medium-text"   value="<?php echo $data->getShippingAddress(); ?>"/></td>
				</tr>
				<tr>
					<td>Shipping City</td>
					<td><input id="mm-order-shipping-city" type="text" class="medium-text"   value="<?php echo $data->getShippingCity(); ?>"/></td>
				</tr>
				<tr>
					<td>Shipping State</td>
					<td>
					
					<input id="mm-order-shipping-state-txt" onkeyup="mmjs.updateShippingState('txt')" type="text" class="medium-text"   value="<?php echo $data->getShippingState(); ?>" style='<?php echo (($displayShippingStateDropDown)?"display:none;":""); ?>'/>
					
			<input type='hidden' id='mm-order-shipping-state' value='<?php echo $data->getShippingState(); ?>'  />
				<select id="mm-order-shipping-state-sel" onchange="mmjs.updateShippingState('sel')" size="1" style='<?php echo ((!$displayShippingStateDropDown)?"display:none;":""); ?>' >


	<option value="AL" onclick="" >Alabama</option>

	<option value="AK" onclick="" >Alaska</option>

	<option value="AS" onclick="" >American Samoa</option>

	<option value="AZ" onclick="" >Arizona</option>

	<option value="AR" onclick="" >Arkansas</option>

	<option value="AE" onclick="" >Armed Forces Africa</option>

	<option value="AA" onclick="" >Armed Forces Americas</option>

	<option value="AE" onclick="" >Armed Forces Canada</option>

	<option value="AE" onclick="" >Armed Forces Europe</option>

	<option value="AE" onclick="" >Armed Forces Middle East</option>

	<option value="AP" onclick="" >Armed Forces Pacific</option>

	<option value="CA" onclick="" >California</option>

	<option value="CO" onclick="" >Colorado</option>

	<option value="CT" onclick="" >Connecticut</option>

	<option value="DE" onclick="" >Delaware</option>

	<option value="DC" onclick="" >District of Columbia</option>

	<option value="FM" onclick="" >Federated States Of Micronesia</option>

	<option value="FL" onclick="" >Florida</option>

	<option value="GA" onclick="" >Georgia</option>

	<option value="GU" onclick="" >Guam</option>

	<option value="HI" onclick="" >Hawaii</option>

	<option value="ID" onclick="" >Idaho</option>

	<option value="IL" onclick="" >Illinois</option>

	<option value="IN" onclick="" >Indiana</option>

	<option value="IA" onclick="" >Iowa</option>

	<option value="KS" onclick="" >Kansas</option>

	<option value="KY" onclick="" >Kentucky</option>

	<option value="LA" onclick="" >Louisiana</option>

	<option value="ME" onclick="" >Maine</option>

	<option value="MH" onclick="" >Marshall Islands</option>

	<option value="MD" onclick="" >Maryland</option>

	<option value="MA" onclick="" >Massachusetts</option>

	<option value="MI" onclick="" >Michigan</option>

	<option value="MN" onclick="" >Minnesota</option>

	<option value="MS" onclick="" >Mississippi</option>

	<option value="MO" onclick="" >Missouri</option>

	<option value="MT" onclick="" >Montana</option>

	<option value="NE" onclick="" >Nebraska</option>

	<option value="NV" onclick="" >Nevada</option>

	<option value="NH" onclick="" >New Hampshire</option>

	<option value="NJ" onclick="" >New Jersey</option>

	<option value="NM" onclick="" >New Mexico</option>

	<option value="NY" onclick="" >New York</option>

	<option value="NC" onclick="" >North Carolina</option>

	<option value="ND" onclick="" >North Dakota</option>

	<option value="MP" onclick="" >Northern Mariana Islands</option>

	<option value="OH" onclick="" >Ohio</option>

	<option value="OK" onclick="" >Oklahoma</option>

	<option value="OR" onclick="" >Oregon</option>

	<option value="PA" onclick="" >Pennsylvania</option>

	<option value="PR" onclick="" >Puerto Rico</option>

	<option value="RI" onclick="" >Rhode Island</option>

	<option value="SC" onclick="" >South Carolina</option>

	<option value="SD" onclick="" >South Dakota</option>

	<option value="TN" onclick="" >Tennessee</option>

	<option value="TX" onclick="" >Texas</option>

	<option value="UT" onclick="" >Utah</option>

	<option value="VT" onclick="" >Vermont</option>

	<option value="VI" onclick="" >Virgin Islands</option>

	<option value="VA" onclick="" >Virginia</option>

	<option value="WA" onclick="" >Washington</option>

	<option value="WV" onclick="" >West Virginia</option>

	<option value="WI" onclick="" >Wisconsin</option>

	<option value="WY" onclick="" >Wyoming</option></select> 
			
					
					</td>
				</tr>
				<tr>
					<td>Shipping Zip Code</td>
					<td><input id="mm-order-shipping-zip" type="text" class="medium-text"   value="<?php echo $data->getShippingZipCode(); ?>"/></td>
				</tr>
				<tr>
					<td>Shipping Country</td>
					<td><select id="mm-order-shipping-country" onchange="mmjs.toggleStateList('<?php echo MM_LimeLightUtils::$COUNTRY_ID_US; ?>','shipping'); "><?php echo $campaignListHtml2 ?></select></td>
				</tr>
			</table>
	<script type='text/javascript'>
	mmjs.toggleStateList('<?php echo MM_LimeLightUtils::$COUNTRY_ID_US; ?>','billing','<?php echo $data->getBillingState(); ?>')
	mmjs.toggleStateList('<?php echo MM_LimeLightUtils::$COUNTRY_ID_US; ?>','shipping','<?php echo $data->getShippingState(); ?>');
	
	</script>
	</td>
	</tr>
	
	