<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	global $mmSite;
	$url = new MM_Url();
	
					$affiliateId = MM_RetentionReport::getAffiliateCookie(MM_OPTION_TERMS_AFFILIATE);
					$subAffiliateId = MM_RetentionReport::getAffiliateCookie(MM_OPTION_TERMS_SUB_AFFILIATE);
?>
<div id="mm-order-form-container">
	
	<table cellspacing="10">
		<tr>
			<td width="140">Member Type</td>
			<td>
				<select id="mm-order-member-type" onchange="mmjs.updateMemberType()">
					<?php echo MM_HtmlUtils::getMemberTypesList(null, true); ?>
				</select>
				
				<input id="mm-member-type-is-free" type="hidden" />
			</td>
		</tr>
		<tr>
			<td width="140">First Name</td>
			<td><input id="mm-order-first-name" type="text" class="medium-text" /></td>
		</tr>
		<tr>
			<td>Last Name</td>
			<td><input id="mm-order-last-name" type="text" class="medium-text" /></td>
		</tr>
		<tr>
			<td>Username</td>
			<td><input id="mm-order-username" type="text" class="medium-text" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input id="mm-order-password" type="password" class="medium-text" /></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input id="mm-order-email" type="text" class="medium-text" /></td>
		</tr>
		<tr>
			<td width="140">Affiliate ID</td>
			<td><input id="mm_affiliate_id_add" type="text" class="medium-text" value='<?php echo $affiliateId; ?>' /></td>
		</tr>
		<tr>
			<td width="140">Sub-Affiliate ID</td>
			<td><input id="mm_sub_affiliate_id_add" type="text" class="medium-text" value='<?php echo $subAffiliateId; ?>' /></td>
		</tr>
	</table>
		
	<div id="mm-order-paid-membership-form" style="display:none;">
	<div style="width: 100%;" class="mm-divider"></div>
	<?php 
	
		if(!$url->isSSL()){ ?>
		<table cellspacing="10">
			<tr>
				<td colspan="2">
					<div style="color: #D8544A">
						<img src="<?php echo MM_Utils::getImageUrl('exclamation'); ?>" style="vertical-align:middle;" /> 
						THIS FORM IS NOT SECURE AND SHOULD ONLY BE USED FOR TEST PURPOSES
					</div>
				</td>
			</tr>
		</table>
		<?php } 
		
		?>
		
		<div style="width: 100%;" class="mm-divider"></div>
		
		<table cellspacing="10">
			<tr>
				<td>Billing Address</td>
				<td><input id="mm-order-billing-address" type="text" class="medium-text" /></td>
			</tr>
			<tr>
				<td>Billing City</td>
				<td><input id="mm-order-billing-city" type="text" class="medium-text" /></td>
			</tr>
			<tr>
				<td>Billing State</td>
				<td><input id="mm-order-billing-state" type="text" class="medium-text" /></td>
			</tr>
			<tr>
				<td>Billing Zip Code</td>
				<td><input id="mm-order-billing-zip" type="text" class="medium-text" /></td>
			</tr>
			<tr>
				<td>Billing Country</td>
				<td><select id="mm-order-billing-country"></select></td>
			</tr>
			<tr>
				<td>Phone</td>
				<td><input id="mm-order-phone" type="text" class="medium-text" /></td>
			</tr>
		</table>
		
		<div style="width: 100%;" class="mm-divider"></div>
		
		<table cellspacing="10">
			<tr>
				<td>Payment Method</td>
				<td>
					<select id="mm-order-payment-method">
						
					</select>
				</td>
			</tr>
			<tr>
				<td>Card Number</td>
				<td><input id="mm-order-cc-number" type="text" class="medium-text" maxlength="16" /></td>
			</tr>
			<tr>
				<td>Exp. Date</td>
				<td>
					<select id="mm-order-cc-exp-month">
						<?php echo MM_HtmlUtils::getCCExpMonthList(); ?>
					</select>
					<select id="mm-order-cc-exp-year">
						<?php echo MM_HtmlUtils::getCCExpYearList(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Security Code</td>
				<td><input id="mm-order-cc-security-code" type="password" class="short-text" /></td>
			</tr>
		</table>
		
		<div style="width: 100%;" class="mm-divider"></div>
		
		<table cellspacing="10">
			<tr>
				<td width="140">Shipping Method</td>
				<td>
					<select id="mm-order-shipping-method">
	
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input id="mm-cb-order-shipping-same-as-billing" type="checkbox" checked onclick="mmjs.processForm()" />
					Shipping is the same as billing
					
					
					<input id="mm-order-shipping-same-as-billing" type="hidden" />
				</td>
			</tr>
		</table>
		
		<div id="mm-order-shipping-address-form" style="display:none;">
			<table cellspacing="10">
				<tr>
					<td width="140">Shipping Address</td>
					<td><input id="mm-order-shipping-address" type="text" class="medium-text" /></td>
				</tr>
				<tr>
					<td>Shipping City</td>
					<td><input id="mm-order-shipping-city" type="text" class="medium-text" /></td>
				</tr>
				<tr>
					<td>Shipping State</td>
					<td><input id="mm-order-shipping-state" type="text" class="medium-text" /></td>
				</tr>
				<tr>
					<td>Shipping Zip Code</td>
					<td><input id="mm-order-shipping-zip" type="text" class="medium-text" /></td>
				</tr>
				<tr>
					<td>Shipping Country</td>
					<td><select id="mm-order-shipping-country"></select></td>
				</tr>
			</table>
		</div>
		</div>
		
</div>

<script>mmjs.updateMemberType();</script>