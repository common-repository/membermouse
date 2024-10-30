<?php
$accounts = MM_EmailAccount::getEmailAccountsList(true);
$prefix = MM_ContentDeliveryView::$PREFIX;
$send = (get_post_meta($p->post_id, $prefix.MM_ContentDeliveryView::$FORM_FIELD_SEND, true) =='1')?'checked':'';
$selectedAccount = get_post_meta($p->post_id, $prefix.MM_ContentDeliveryView::$FORM_FIELD_FROM, true);
$subject = get_post_meta($p->post_id, $prefix.MM_ContentDeliveryView::$FORM_FIELD_SUBJECT, true);
$body = get_post_meta($p->post_id, $prefix.MM_ContentDeliveryView::$FORM_FIELD_BODY, true);

if(intval($selectedAccount)<=0){
	$emailAccount  = new MM_EmailAccount();
	$emailAccount->getDefault();
	$selectedAccount = $emailAccount->getId();
}

$accountsSelect=  MM_HtmlUtils::generateSelectionsList($accounts->list, $selectedAccount);

//check for access rights
global $post;
$pc = new MM_ProtectedContentEngine();
$rows = $pc->getPostAccessRights($post->ID);
$shouldHideNotification = false;
if(count($rows)<=0){
	$shouldHideNotification = true;
}
wp_nonce_field(MM_ContentDeliveryView::$NONCE,MM_ContentDeliveryView::$NONCE.'-nonce');
$installedCron = (MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_CRON_INSTALLED)=="1")?true:false; 

?>
<div style='<?php echo ((!$installedCron)?"display:none":""); ?>'>
<table id='mm-content-delivery-table'  <?php echo ((!$installedCron)?"style='display:none'":""); ?>>
<tr>
	<td colspan='2'>
		<input type='checkbox' id='mm-content-delivery-send' name='<?php echo MM_ContentDeliveryView::$NONCE; ?>[<?php echo MM_ContentDeliveryView::$FORM_FIELD_SEND; ?>]' value='1' <?php echo $send; ?> onchange="contentDelivery.toggleContentArea()" /> 
		Send a notification email notification when this content becomes available. This won't be sent to members who have access to this content on day 0.
	</td>
</tr>
<tr>
	<td colspan='2'>&nbsp;</td>
</tr>
<tr id='mm-content-delivery-fields'>
	<td>
		<table width='100%'>
			<tr>
				<td>
					From:
				</td>
				<td>
					<select name='<?php echo MM_ContentDeliveryView::$NONCE; ?>[<?php echo MM_ContentDeliveryView::$FORM_FIELD_FROM; ?>]'>
					<?php echo $accountsSelect; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'>&nbsp;</td>
			</tr>
			<tr>
				<td>
					Subject:
				</td>
				<td>
					<input type='text' name='<?php echo MM_ContentDeliveryView::$NONCE; ?>[<?php echo MM_ContentDeliveryView::$FORM_FIELD_SUBJECT; ?>]' value='<?php echo $subject; ?>' style="width: 375px" />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
							<div style="margin-top:5px">
							 <?php echo MM_SmartTagLibraryView::smartTagLibraryButtons(MM_ContentDeliveryView::$NONCE); ?>
							</div>
							
							<div style="margin-top:5px">
								<textarea id='<?echo MM_ContentDeliveryView::$NONCE; ?>' name='<?php echo MM_ContentDeliveryView::$NONCE; ?>[<?php echo MM_ContentDeliveryView::$FORM_FIELD_BODY; ?>]' class='long-text' rows="6" style="width: 95%; font-size: 11px;"><?php echo $body; ?></textarea>
							</div>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table></div>
<?php if($installedCron){ ?>
<script type='text/javascript'>
mmJQuery(document).ready(function() {
	<?php if($shouldHideNotification){ ?>
	contentDelivery.hideNotification();
	<?php }else{ ?>
	contentDelivery.toggleContentArea();
	<?php } ?>
	
});
</script>
<?php } ?>