<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
global $mmSite;
$registration = new MM_RegistrationView();
$data = $registration->getDataInSession();
$sameAsBilling = false;

if(!isset($data["mm_order_shipping_same_as_billing"]) || (isset($data["mm_order_shipping_same_as_billing"]) && $data["mm_order_shipping_same_as_billing"] == "YES"))
{ 
	$sameAsBilling = true;
} 
$hiddenfields = "";
foreach($data as $k=>$v)
{
	$key = preg_replace("/(_)/", "-", $k);
	LogMe::write("FIELD", $key);
	$hiddenfields .= "<input type='hidden' id='{$key}' value='{$v}' />";
}

//// determine if it is even shippable.
$memberType = new MM_MemberType($data["mm_order_member_type"]);
$productId = $memberType->getRegistrationProduct();
if(isset($data["mm_order_product_id"]) && intval($data["mm_order_product_id"])>0){ 
	$productId = $data["mm_order_product_id"];
}
$isShippable = false;

$productName = "";
$productPrice = "";
if($productId>0)
{
	$product = new MM_Product($productId);
	$isShippable = (bool)$product->isShippable();
	$productName = $product->getName();
	$productPrice = $product->getPrice(true);
}

$shippingMethod="";
if($isShippable)
{
	$campaign = new MM_Campaign($mmSite->getCampaignIds(), false);
	$list = $campaign->getSettingsList(MM_Campaign::$SETTING_TYPE_SHIPPING);
	
	if(!MM_Utils::isLimeLightInstall()){
		$list = MM_CampaignOptions::getOptions("shipping");
	}
	
	$shippingMethod = "";
	if(isset($list[$data["mm_order_shipping_method"]]))
		$shippingMethod = $list[$data["mm_order_shipping_method"]];
}


$selected = "";
if(isset($data["mm_order_payment_choice"])){
	$selected = $data["mm_order_payment_choice"];
}
$options = $registration->getGatewayInformation($selected);
$paymentOption = new MM_CampaignOptions($selected);
$paymentMethodName = $paymentOption->getName();
?>	
	<tr><td colspan='3'>
<?php echo MM_RegistrationView::getHiddenStepsHtml('step4'); ?>
	</td>

	</tr>

		<tr>
			<td width="140">Name</td>
			<td><?php echo $data["mm_order_first_name"]." ".$data["mm_order_last_name"]; ?></td>
		</tr>
		<tr>
			<td width="140">Email</td>
			<td><?php echo $data["mm_order_email"]; ?></td>
		</tr>
		<?php if(!empty($productName)){ ?>
		<tr>
			<td width="140">Product</td>
			<td><?php echo $productName; ?></td>
		</tr>

		<tr>
			<td width="140">Product Price</td>
			<td>$<?php echo $productPrice; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td>Billing Address</td>
			<td><?php echo $data["mm_order_billing_address"]; ?></td>
		</tr>
		<tr>
			<td>Billing City</td>
			<td><?php echo $data["mm_order_billing_city"]; ?></td>
		</tr>
		<tr>
			<td>Billing State</td>
			<td><?php echo $data["mm_order_billing_state"]; ?></td>
		</tr>
		<tr>
			<td>Billing Zip Code</td>
			<td><?php echo $data["mm_order_billing_zip"]; ?></td>
		</tr>
		<tr>
			<td>Billing Country</td>
			<td><?php echo MM_LimeLightUtils::getCountryName($data["mm_order_billing_country"]); ?></td>
		</tr>
		<tr>
			<td>Phone</td>
			<td><?php echo $data["mm_order_phone"]; ?></td>
		</tr>
		<?php if($paymentMethodName !=""){ ?>
		<tr>
			<td>
				Payment Method
			</td>
			<td>
				<?php echo $paymentMethodName; ?>
			</td>
		</tr>
		<?php } ?>
		<?php 
		if((isset($options->hidden_onsite) && $options->hidden_onsite=='1') || !isset($options->hidden_onsite)){ ?>
		<tr>
			<td>Payment Method</td>
			<td>
					<?php echo $data["mm_order_payment_method"]; ?>
				
			</td>
		</tr>
		<tr>
			<td>Card Number</td>
			<td><?php echo str_pad(substr($data["mm_order_cc_number"],-4), 16, "*", STR_PAD_LEFT); ?></td>
		</tr>
		<tr>
			<td>Exp. Date</td>
			<td>
					<?php echo $data["mm_order_cc_exp_month"]; ?>
				-	<?php echo $data["mm_order_cc_exp_year"]; ?>
			</td>
		</tr>
		<tr>
			<td>Security Code</td>
			<td>***</td>
		</tr>
	<tr><td colspan='2'>&nbsp;</td></tr>
	<?php } ?>
	<?php if($isShippable){?>
		<tr>
			<td width="140">Shipping Method</td>
			<td>
					<?php echo $shippingMethod; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			
				<?php if($sameAsBilling){ ?>
				Shipping is the same as billing
				<?php } ?>
				
			</td>
		</tr>
	
				<?php if(!$sameAsBilling){ ?>
				<tr>
					<td width="140">Shipping Address</td>
					<td><?php echo $data["mm_order_shipping_address"]; ?></td>
				</tr>
				<tr>
					<td>Shipping City</td>
					<td><?php echo $data["mm_order_shipping_city"]; ?></td>
				</tr>
				<tr>
					<td>Shipping State</td>
					<td><?php echo $data["mm_order_shipping_state"]; ?></td>
				</tr>
				<tr>
					<td>Shipping Zip Code</td>
					<td><?php echo $data["mm_order_shipping_zip"]; ?></td>
				</tr>
				<tr>
					<td>Shipping Country</td>
					<td><?php echo MM_LimeLightUtils::getCountryName($data["mm_order_shipping_country"]); ?></td>
				</tr>
				<?php } ?>
				<?php } ?>
				<tr><td colspan='2'><?php echo $hiddenfields; ?></td></tr>
	
	