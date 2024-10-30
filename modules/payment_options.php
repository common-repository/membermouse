<?php
$campaignOption = new MM_CampaignOptions();

if(isset($_POST["submit"])){
	if(isset($_POST["authorizenet"])){
		
		$gateway = MM_Utils::getGatewayMethodObj(new MM_AuthorizeService());
		if(isset($gateway->attr->hidden_paymentObject)){
			$settings = new MM_CampaignSettingsView();
			$post['mm_name'] = "Authorize.Net";
			$post['mm_id'] = $_POST["authorizenet_payment_id"];
			$post['mm_setting_type'] = "payment";
			$post['mm_gateways'] = $gateway->id;
			$post['mm_show_on_reg'] = (isset($_POST["authorizenet_show_on_reg"]))?"1":"0";
			$post['transkey'] = $_POST["authorizenet_transkey"];
			$post['login'] = $_POST["authorizenet_login"];
			$response = $settings->savePayment($post);
		}
	}
	else{
		MM_CampaignOptions::removePaymentByService(new MM_AuthorizeService());
	}
	
	if(isset($_POST["paypal"])){
		
		$gateway = MM_Utils::getGatewayMethodObj(new MM_PaypalService());
		if(isset($gateway->attr->hidden_paymentObject)){
			$settings = new MM_CampaignSettingsView();
			$post['mm_name'] = "PayPal";
			$post['mm_id'] = $_POST["paypal_payment_id"];
			$post['mm_setting_type'] = "payment";
			$post['mm_gateways'] = $gateway->id;
			$post['mm_show_on_reg'] = (isset($_POST["paypal_show_on_reg"]))?"1":"0";
			$post['email'] = $_POST["paypal_email"];
			$response = $settings->savePayment($post);
			
		}
	}
	else{
		MM_CampaignOptions::removePaymentByService(new MM_PaypalService());
	}
	
	if(isset($_POST["clickbank"])){
		
		$gateway = MM_Utils::getGatewayMethodObj(new MM_ClickBankService());
		if(isset($gateway->attr->hidden_paymentObject)){
			$settings = new MM_CampaignSettingsView();
			$post['mm_name'] = "ClickBank";
			$post['mm_id'] = $_POST["clickbank_payment_id"];
			$post['mm_setting_type'] = "payment";
			$post['mm_gateways'] = $gateway->id;
			$post['mm_show_on_reg'] = (isset($_POST["clickbank_show_on_reg"]))?"1":"0";
			$post['vendor'] = $_POST["clickbank_vendor"];
			$post['developer_key'] = $_POST["clickbank_developer_key"];
			$post['api_key'] = $_POST["clickbank_api_key"];
			$response = $settings->savePayment($post);
			
		}
	}
	else{
		MM_CampaignOptions::removePaymentByService(new MM_ClickBankService());
	}
}

$paypalOption = MM_Utils::getPaymentMethodObj(new MM_PaypalService());

$shouldShowPaypal = "";
if(!isset($paypalOption->attr->email) || (isset($paypalOption->attr->email) && empty($paypalOption->attr->email) )){
	$shouldShowPaypal = "display:none;";
	$paypalOption->attr->email = "";
	$paypalOption->show_on_reg = "";
	$paypalOption->id = "";
}

$clickbankOption = MM_Utils::getPaymentMethodObj(new MM_ClickBankService());

$shouldShowClickbank = "";
if(!isset($clickbankOption->attr->vendor) || (isset($clickbankOption->attr->vendor) && empty($clickbankOption->attr->vendor))){
	$clickbankOption->attr->vendor = "";
	$clickbankOption->attr->developer_key = "";
	$clickbankOption->attr->api_key = "";
	$shouldShowClickbank = "display:none;";
	$clickbankOption->show_on_reg = "";
	$clickbankOption->id = "";
}

$authNetOption = MM_Utils::getPaymentMethodObj(new MM_AuthorizeService());
$shouldShowAuthNet = "";
if(!isset($authNetOption->attr->transkey) || (isset($authNetOption->attr->transkey) && empty($authNetOption->attr->transkey))){
	$authNetOption->attr->transkey = "";
	$authNetOption->attr->login = "";
	$shouldShowAuthNet = "display:none;";
	$authNetOption->show_on_reg = "";
	$authNetOption->id = "";
}
?>
	<form method='post'>
<div class="wrap">
	<div style='padding-left: 10px;margin-top:10px;'>
	    <h2 class="mm-header-text">Payment Methods</h2>
	    <div style='clear:both; height: 10px;'></div>
	    <div style='width:650px'>
Select your payment methods below:
<br /><br />
<table width='750px'>
	<tr>
		<td>
			<input type='checkbox' id='authorizenet' name='authorizenet'  value='1' onchange="mmjs.showPaymentOption('authorizenet')" <?php echo ((empty($shouldShowAuthNet))?"checked":""); ?> />
		</td>
		<td>
			Authorize.Net 
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			
<div id='payment_option_authorizenet' style='<?php echo $shouldShowAuthNet; ?> margin-left: 10px; border: 1px solid #eee; background-color: #eee'>
	<table>
		<tr>
			<td colspan='2'>
				<img src='<?php echo MM_Utils::getImageUrl("authorizenet"); ?>' />
			</td>
		</tr>
		<tr>
			<td>
				Show on registration
			</td>
			<td>
				<input type='checkbox' value='<?php echo $authNetOption->show_on_reg; ?>' <?php echo (($authNetOption->show_on_reg=="1")?"checked":""); ?> id='authorizenet_show_on_reg' name='authorizenet_show_on_reg'  />
			</td>
		</tr>
		<tr>
			<td>
				Login
			</td>
			<td>
				<input type='text' value='<?php echo $authNetOption->attr->login; ?>' id='authorizenet_login' name='authorizenet_login' style='width: 275px;' />
				<input type='hidden' value='<?php echo $authNetOption->id; ?>' name='authorizenet_payment_id' />
			</td>
		</tr>
		<tr>
			<td>
				Transaction Key
			</td>
			<td>
				<input type='text' value='<?php echo $authNetOption->attr->transkey; ?>' id='authorizenet_transkey' name='authorizenet_transkey' style='width: 275px;' />
				<input type='hidden' value='<?php echo $authNetOption->id; ?>' name='authorizenet_payment_id' />
			</td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>
		</table>
</div>
		</td>
	</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>
	<tr>
		<td width='25px'>
			<input type='checkbox' id='paypal' name='paypal' value='1' onchange="mmjs.showPaymentOption('paypal')" <?php echo ((empty($shouldShowPaypal))?"checked":""); ?> />
		</td>
		<td>
			PayPal 
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			
<div id='payment_option_paypal' style='<?php echo $shouldShowPaypal; ?> margin-left: 10px; border: 1px solid #eee; background-color: #eee'>
	<table>
		<tr>
			<td colspan='2'>
				<img src='<?php echo MM_Utils::getImageUrl("paypal-logo"); ?>' />
			</td>
		</tr>
		<tr>
			<td>
				Show on registration
			</td>
			<td>
				<input type='checkbox' value='<?php echo $paypalOption->show_on_reg; ?>' <?php echo (($paypalOption->show_on_reg=="1")?"checked":""); ?> id='paypal_show_on_reg' name='paypal_show_on_reg'  />
			</td>
		</tr>
		<tr>
			<td>
				Email
			</td>
			<td>
				<input type='text' value='<?php echo $paypalOption->attr->email; ?>' id='paypal_email' name='paypal_email' style='width: 275px;' />
				<input type='hidden' value='<?php echo $paypalOption->id; ?>' name='paypal_payment_id' />
			</td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>
		<tr>
			<td colspan='2'>
				To complete full integration, copy the link below and follow the instructions in this <a href='http://support.membermouse.com/entries/20105811-paypal-integration' target='_blank'>article</a>:<br />
				<span style='background-color: #ADDFFF; '><a href="<?php echo MM_MODULES_URL."/ipn/callback.php"; ?>" /><?php echo MM_MODULES_URL."/ipn/callback.php"; ?></a></span>
			</td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>
		</table>
</div>
		</td>
	</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>
	<tr>
		<td>
			<input type='checkbox' id='clickbank' name='clickbank'  value='1' onchange="mmjs.showPaymentOption('clickbank')" <?php echo ((empty($shouldShowClickbank))?"checked":""); ?> />
		</td>
		<td>
			ClickBank 
		</td>
	</tr>
	<tr>
		<td colspan='2'>
		
<div id='payment_option_clickbank' style='<?php echo $shouldShowClickbank; ?> margin-left: 10px; border: 1px solid #eee; background-color: #eee'>
	<table>
		<tr>
			<td colspan='2'>
				<img src='<?php echo MM_Utils::getImageUrl("clickbank-logo"); ?>' />
			</td>
		</tr>
		<tr>
			<td>
				Show on registration
			</td>
			<td>
				<input type='checkbox' value='<?php echo $clickbankOption->show_on_reg; ?>' <?php echo (($clickbankOption->show_on_reg=="1")?"checked":""); ?> id='clickbank_show_on_reg' name='clickbank_show_on_reg'  />
			</td>
		</tr>
		<tr>
			<td>
				Vendor
			</td>
			<td>
				<input type='text' value='<?php echo $clickbankOption->attr->vendor; ?>' id='clickbank_vendor' name='clickbank_vendor' style='width: 275px;' />
				<input type='hidden' value='<?php echo $clickbankOption->id; ?>' name='clickbank_payment_id' />
			</td>
		</tr>
		<tr>
			<td>
				Developer Key
			</td>
			<td>
				<input type='text' value='<?php echo $clickbankOption->attr->developer_key; ?>' id='clickbank_developer_key' name='clickbank_developer_key' style='width: 275px;' />
			</td>
		</tr>
		<tr>
			<td>
				API Clerk Key
			</td>
			<td>
				<input type='text' value='<?php echo $clickbankOption->attr->api_key; ?>' id='clickbank_api_key' name='clickbank_api_key' style='width: 275px;' />
			</td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>
		<tr>
			<td colspan='2' >
				To complete full integration, copy the link below and follow the instructions in this <a href='http://support.membermouse.com/entries/20102462-clickbank-integration' target='_blank'>article</a>:<br />
				<span style='background-color: #ADDFFF; '><a href="<?php echo MM_MODULES_URL."/ipn/callback.php"; ?>" /><?php echo MM_MODULES_URL."/ipn/callback.php"; ?></a></span>
			</td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>
		</table>
</div>
		</td>
	</tr>
</table>

	    <div style='clear:both; height: 10px;'></div>
</div>
</div>
</div>
	<input type='submit' name='submit' value='Save Payment Methods' class="button-primary" />
</form>

<script type='text/javascript'>

</script>