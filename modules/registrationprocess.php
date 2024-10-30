<?php 
$sslWarning = "";
$free_reg = $p->free_reg;
$step = (preg_match("/(step1|step2|step3|step4|additional)/",$p->step))?$p->step:'step1';
$urlObj = new MM_Url();
if(!$urlObj->hasSSL()){
	//$sslWarning = "<b>WARNING: This page is not secure</b>";
	$sslWarning = "";
}
$redirectUrl = (isset($p->redirectUrl))?$p->redirectUrl:"";
$registration = new MM_RegistrationView();

$data = $registration->getData($step);
$sessionData = $registration->getDataInSession();

$isCheckout = false;
if(isset($sessionData["mm_order_product_id"]) && intval($sessionData["mm_order_product_id"])>0){ 
	$isCheckout = true;	
}

$contents =  MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.step1.php", array());
if($step!='step1')
{
	$contents =  MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.{$step}.php", array());
}

$stepsHtml = array(
	1=> array('STEP 1', 'Select Plan'),
	2=> array('STEP 2', 'Account Information'),
	3=> array('STEP 3', 'Billing Information'),
	4=> array('STEP 4', 'Confirmation'),
);
$hasAdditionalFields = MM_CustomField::hasCustomFields(true);
if($hasAdditionalFields){
	$stepsHtml = array(
		1=> array('STEP 1', 'Select Plan'),
		2=> array('STEP 2', 'Account Information'),
		3=> array('STEP 3', 'More Information'),
		4=> array('STEP 4', 'Billing Information'),
		5=> array('STEP 5', 'Confirmation'),
	);
}
?>

<div id='mm-view-container'>
<div id='mm_registration_error' style='color:#D7380A; padding-bottom: 5px;'><?php echo $sslWarning; ?></div>
<div id='mm-steps' style=''>
	<ul class="mm-steps"> 
<?php 
$stepInt = preg_replace("/[^0-9]+/", "", $step);

if(count($data)>0 ){
	if(!$isCheckout){
		foreach($stepsHtml as $key=>$val){
			if($key==1){
				?>
				<li><a id='mm-steps<?php echo $key?>' class="mm-active" ><?php echo $val[0]; ?>: <?php echo $val[1]; ?></a><input type='hidden' id='mm-steps<?php echo $key; ?>-name' value='<?php echo $val[0].": ".$val[1]; ?>' /></li> 
				<?php 
			}
			else if($stepInt>$key){
				?>
				<li><a id='mm-steps<?php echo $key?>' class="mm-mark"><?php echo $val[0]; ?></a><input type='hidden' id='mm-steps<?php echo $key; ?>-name' value='<?php echo $val[0].": ".$val[1]; ?>' /></li> 
				<?php 
			}
			else{ 
				?>
				<li><a id='mm-steps<?php echo $key?>'><?php echo $val[0]; ?></a><input type='hidden' id='mm-steps<?php echo $key; ?>-name' value='<?php echo $val[0].": ".$val[1]; ?>' /></li> 
				<?php 
			}
		}	

?>
<?php 
}else{
?>
		<li><a id='mm-steps3' class="mm-active" >STEP 1: Billing Information</a></li> 
		<li><a id='mm-steps4' >STEP 2</a><input type='hidden' id='mm-steps4-name' value='Confirmation' /></li>
<?php 
}
?>
	</ul>
</div>

<?php 
if(empty($redirectUrl)){
?>
	<table id='mm_registration_table' style='width: 500px;'>
	<?php echo $contents; ?>
		<tr>
			<td colspan='2' align='left'>
				<?php 
				$disabled = "";
				if(isset($sessionData["mm_order_product_id"])){ 
					if($step<=3){
						$disabled=  "style='display:none;'";
					}
					?>
					<input type='hidden' id='mm-is-checkout' value='1' />
					<?php 
				}
					?>
				<input type='button' name='back' id='mm-back' <?php echo $disabled; ?> value='Back' onclick="mmjs.prevStep()"  class='mm-submit-register-back' />
				
			</td>
			<td colspan='1' align='right'>
				<input type='button' name='next' id='mm-next' value='Continue' onclick="mmjs.nextStep()"  class='mm-submit-register'  />
			</td>
		</tr>
	</table>
<?php 
}
else{
?>
Error finding product, redirecting...
<?php 
}?>
<?php }else{ ?>
There are not active member types available for registration.
<?php } 
?>
</div>
<script type="text/javascript">

mmJQuery(document).ready(function() {
	<?php if(!empty($redirectUrl)){ ?>
		document.location.href='<?php echo $redirectUrl; ?>';
	<?php }
	if($isCheckout){
	?>
	mmJQuery(".main-heading h1").html("Checkout");
	<?php }
	if($stepInt==2){
	?>
	mmjs.showStepProgress('step2');
	<?php 	
	}
	
	?>
});
</script>
