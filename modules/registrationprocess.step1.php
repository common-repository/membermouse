<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$registration = new MM_RegistrationView();
$data = $registration->getData('step1');
$session = $registration->getDataInSession();

$selected_type = 0;
if(isset($session["mm_order_member_type"]))
{
	$selected_type = $session["mm_order_member_type"];
}
else if(isset($_REQUEST["member_type"]))
{
	$selected_type = $_REQUEST["member_type"];
}

$contents = "";
foreach($data as $info){
	if($selected_type == $info->id){
		$info->checked = "checked";	
	}	
	?>
<tr>
	<td>
		<input type='radio' id='mm-order-member-type' name='mm-order-member-type' value='<?php echo $info->id; ?>' <?php echo $info->checked; ?> />
	<?php echo MM_RegistrationView::getHiddenStepsHtml('step1'); ?>
	</td>
	<td>
		<?php echo $info->name; ?>
	</td>
	<td>
	<?php echo $info->description; ?>
	</td>
</tr>
<?php 
}
?>
<script type='text/javascript'>
mmJQuery(document).ready(function(){
	mmJQuery("#mm-back").hide();	
});
</script>