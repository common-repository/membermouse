<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$product = new MM_Product($p->id);
$p->name = $product->getName();
$p->sku = $product->getSku();
$p->price = $product->getPrice();	
$p->status = $product->getStatus();
$p->is_shippable = $product->isShippable();
$p->is_trial = $product->isTrial();
$p->trial_amount = $product->getTrialAmount();
$p->trial_duration = $product->getTrialDuration();
$p->is_recurring = $product->isRecurring();
$p->rebill_period = $product->getRebillPeriod();
$p->description = $product->getDescription();
$p->product_id = $product->getProductId();
$p->payment_id = $product->getPaymentId();

$periodsArr = array(
	'months'=>'months',
	'days'=>'days',
	'weeks'=>'weeks',
	'years'=>'years',
);

$options = MM_CampaignOptions::getOptions("payment");
foreach($options as $id=>$val){
	$obj = new MM_CampaignOptions($id);
	if($obj->isValid()){
		$attr = $obj->getAttr();
		$json = json_decode($attr);
		if(isset($json->hidden_paymentObject)){
			if($json->hidden_paymentObject!="MM_ClickBankService"){
				unset($options[$id]);
			}
		}
	}
}

$paymentOptions = MM_HtmlUtils::generateSelectionsList($options, $p->payment_id);
$trialFreqSelect = MM_HtmlUtils::generateSelectionsList($periodsArr, $product->getTrialFrequency());
$rebillFreqSelect = MM_HtmlUtils::generateSelectionsList($periodsArr, $product->getRebillFrequency());

?>


<div id="mm-form-container">
<input type='hidden' id='id' value='<?php echo $p->id; ?>' />
<table>
<tr>
	<td>Name</td>
	<td><input type='text' id='name' value='<?php echo ((isset($p->name))?$p->name:''); ?>' style='width: 225px;'/></td>
</tr>
<tr>
	<td>SKU</td>
	<td><input type='text' id='sku' value='<?php echo ((isset($p->sku))?$p->sku:''); ?>'  style='width: 225px;'/></td>
</tr>
<tr>
	<td>Price</td>
	<td><input type='text' id='price' value='<?php echo ((isset($p->price))?$p->price:''); ?>'  style='width: 125px;'/></td>
</tr>
<tr>
	<td>Has Trial</td>
	<td>
		<input type='checkbox' id='is_trial' onchange="mmjs.toggleTrial();" <?php echo ((isset($p->is_trial) && $p->is_trial=='1')?'checked':''); ?>   />
		<input type='hidden' id='is_trial_val' value='<?php echo ((isset($p->is_trial) && $p->is_trial=='1')?'1':'0'); ?>' />
	</td>
</tr>
<tr>
	<td colspan='2'>
		<table id='mm_is_trial_row' style='display:none;padding-left: 50px;'>
			<tr>
				<td width='120px'>Trial Price</td>
				<td><input type='text' id='trial_amount' value='<?php echo ((isset($p->trial_amount))?$p->trial_amount:''); ?>'  style='width: 125px;'/></td>
			</tr>
			<tr>
				<td width='120px'>Trial Period</td>
				<td>
					<input type='text' id='trial_duration' value='<?php echo ((isset($p->trial_duration))?$p->trial_duration:''); ?>'  style='width: 50px;'/> 
					<select id='trial_frequency'>
					<?php echo $trialFreqSelect; ?>
					</select>
					
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>Is Recurring</td>
	<td>
		<input type='checkbox' onchange="mmjs.toggleRecurring();" id='is_recurring' <?php echo (($product->isRecurring(false))?'checked':''); ?> />
		<input type='hidden' id='is_recurring_val' value='<?php echo (($product->isRecurring(false))?'1':'0'); ?>' />
	</td>
</tr>
<tr>
	<td colspan='2'>
		<table  id='mm_rebill_row' style='display:none;padding-left: 50px;'>
			<tr>
				<td width='80px'>Rebill Period</td>
				<td width='100px'>
					<input type='text' id='rebill_period' value='<?php echo ((isset($p->rebill_period))?$p->rebill_period:''); ?>'  style='width: 50px'/> 
					<select id='rebill_frequency'>
					<?php echo $rebillFreqSelect; ?>
					</select>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>Is Shippable</td>
	<td>
		<input type='checkbox' id='is_shippable' <?php echo ((isset($p->is_shippable) && $p->is_shippable=='1')?'checked':''); ?> onchange="mmjs.changeOption('is_shippable');" />
		<input type='hidden' id='is_shippable_val' value='<?php echo ((isset($p->is_shippable) && $p->is_shippable=='1')?'1':'0'); ?>' />
	</td>
</tr>
<tr>
	<td>Description</td> 
	<td><textarea id='description' cols='55' rows='5'><?php echo ((isset($p->description))?$p->description:''); ?></textarea></td>
</tr>
</table>
<?php if(count($options)>0){ ?>
<div style="margin-top: 10px; margin-bottom: 10px; width: 100%;" class="mm-divider"></div>

<table>
<tr>
	<td width='185px'>Is ClickBank Product</td>
	<td>
		<input type='checkbox' id='mm_is_clickbank' onchange="mmjs.showClickBankInfo()" <?php echo ((isset($p->product_id) && $p->product_id>0)?'checked':''); ?> />
		<input type='hidden'   id='mm_is_clickbank_val' value='<?php echo ((isset($p->product_id) && $p->product_id>0)?'1':'0'); ?>' />
	</td>
</tr>
<tr id='mm-clickbank-info' style='display:none;'>
	<td colspan='2'>
		<table>	
			<tr>
				<td>Product ID</td>
				<td><input type='text' id='product_id' value='<?php echo ((isset($p->product_id))?$p->product_id:''); ?>'  style='width: 50px;'/></td>
			</tr>
		</table>
		
	</td>
</tr>
</table>
<?php } ?>
</div>
<script type='text/javascript'>
mmjs.toggleTrial();
mmjs.toggleRecurring();
mmjs.showClickBankInfo();
</script>