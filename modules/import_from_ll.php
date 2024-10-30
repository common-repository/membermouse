<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */ 
?>
<?php if(isset($p->imported) && $p->imported=="1"){?>
<?php if(empty($p->errors)){ ?><?php echo $p->updated_members; ?> existing members updated
<?php echo $p->new_members; ?> new members imported
<?php }else{  ?>
	<?php echo $p->errors; ?>
<?php }  ?>
<?php }else if(isset($p->display) && ($p->display=="1" || $p->display=='3')){?>

<div style='margin-top: 10px;'></div>
<style>
.ui-progressbar-value { background-image: url('<?php echo MM_IMAGES_URL."pbar-animated.gif" ?>'); }
</style>
<div id="mm-progressbar-container" style="display:none; text-align:center" >
	<div id="mm-progressbar" style="width:150px"></div>
	<script>
	mmJQuery(function() {
		mmJQuery("#mm-progressbar").progressbar({
			value: 100
		});
	});
	</script>
</div>
<?php if($p->display=='1'){?>
	<script type='text/javascript'>
		mmJQuery(document).ready(function(){
			mmJQuery("#mm-custom-date").datepicker();
		});
	</script>
<div id='mm-import-wrap' style='width: 100%'>
	<input type='checkbox' id='mm-import-select' value='1' onchange="mmjs.checkAllMembers();" /> Select all orders
	<div style="clear:both; height: 10px;"></div>
<table style='width: 98%;font-size: 14px;'>
<tr valign='top'>
	<td>
		<div id='mm-members-import-list' style='height: 350px; width: 100%; vertical-align: top;overflow: auto; height: 250px'>
				<?php echo $p->list; ?>
		</div>
	</td>
</tr>

<tr><td style='height: 20px;'></td></tr>
<?php } 
	else if($p->display=='3'){ ?>
		
<table style='width: 98%;font-size: 14px;'>
<tr><td style='height: 20px;'><?php echo $p->list; ?></td></tr>
	<?php } ?>
<tr>
	<td><span class='mm-section-header' style='font-size: 16px;'>Import Options</span><div style='margin-bottom: 10px;clear:both;'></div></td>
</tr>

<tr>
	<td>
		<input type='checkbox' id='mm_use_purchase_date'  checked onchange="mmjs.toggleCustomDate()" /> Use <?php if($p->display=='1'){ echo "Product Purchase"; }else{ echo " Imported Registration"; } ?> Date
		<div style="padding-left: 20px; padding-bottom: 5px; font-size: 11px;"><i>If selected, each customer's <?php if($p->display=='1'){ echo "purchase"; }else{ echo "import"; } ?> date will be used as their member registration date otherwise the date below will be used</i></div>
	
	</td>
</tr>
<tr id='mm_use_custom_date' style='display:none;'>
	<td style="padding-left: 20px;">
		<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
		<input type='text' id='mm-custom-date' value='<?php echo Date("m/d/Y"); ?>' />
	</td>
</tr>
<tr>
	<td>
		<input type='checkbox' id='mm_send_instant_notifications' /> Enable Instant Notifications
		<div style="padding-left: 20px; padding-bottom: 5px; font-size: 11px;"><i>If selected, the create member instant notification script will be called for each member imported</i></div>
	</td>
</tr>
<tr>
	<td>
		<input type='checkbox' id='mm_send_welcome_emails' /> Send Welcome Email
		<div style="padding-left: 20px; padding-bottom: 5px; font-size: 11px;"><i>If selected, a welcome email will be sent to each member imported</i></div>
	</td>
</tr>

</table>
</div>
<?php } else if(isset($p->find) && $p->find=="1") { ?>

<div style='margin-top: 10px;'></div>
<style>
.ui-progressbar-value { background-image: url('<?php echo MM_IMAGES_URL."pbar-animated.gif" ?>'); }
</style>
<div id="mm-progressbar-container" style="display:none;" >
	<div id="mm-progressbar" style="width:150px"></div>
	<script>
	mmJQuery(function() {
		mmJQuery("#mm-progressbar").progressbar({
			value: 100
		});
	});
	</script>
</div>
<table style='margin-top: 10px; font-size: 14px;' id='mm-members-find-results'>
	<tr>
		<td  style = 'font-size: 14px;'>
		You are about to request details for <?php echo $p->total_orders; ?> members. This could take up to <?php echo $p->time; ?> minute(s).<br/>
		Please confirm you want to continue.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td align='left'>
<input type='button' onclick="mmjs.getMemberDetails();" value="Get Member Details" class="ui-button ui-widget ui-state-default ui-corner-all" />
		</td>
	</tr>
</table>


<div id='mm-order-ids' style='display:none;'><?php echo $p->import_ids; ?></div>
<?php 
}else{ ?>	
<div style='margin-top: 10px;'></div>
	<script type='text/javascript'>
		mmJQuery(document).ready(function(){
			mmJQuery("#mm-import-from-date").datepicker();
			mmJQuery("#mm-import-to-date").datepicker();
			mmJQuery("#mm-calendar").focus();
			
		});
	</script>
<table width='90%' id='mm-import-table' style = 'font-size: 14px;'>
	<tr>
		<td width='80px'>Campaign</td>
		<td>
			<select id="mm-import-campaign">
			<?php 
				global $mmSite;
				$ids = explode(",",$mmSite->getCampaignIds());
				$idArr = array();
				foreach($ids as $id){
					$tmpCampaign = new MM_Campaign($id);
					$idArr[$id] = $tmpCampaign->getName();
				}
				echo MM_HtmlUtils::generateSelectionsList($idArr); 
			?>
			</select> 
		</td>
	</tr>
	<tr>
		<td>From</td>
		<td>
			<img id='mm-calendar' src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
			<input id="mm-import-from-date" type="text" style="width: 152px" /> 
		</td>
	</tr>
	<tr>
		<td>To</td>
		<td>
			<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
			<input id="mm-import-to-date" type="text" style="width: 152px" />
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td >
			<input id="mm-find-members" type="button" id='mm-find-members' style="width: 152px" value='Find Members' onclick="mmjs.findMembers();"  class="ui-button ui-widget ui-state-default ui-corner-all " />
		</td>
	</tr>
</table>

<input type='hidden' id='mm_campaign_id' value='<?php echo ((isset($p->campaign_id))?$p->campaign_id:""); ?>' />
<div style='margin-top: 10px;'></div>

<div style="width: 100%; margin-top: 10px; margin-bottom: 10px;" class="mm-divider"></div>
<style>
 
.ui-progressbar-value { background-image: url('<?php echo MM_IMAGES_URL."pbar-animated.gif" ?>'); }
</style>
<div id="mm-progressbar-container" style="display:none;" >
	<div id="mm-progressbar" style="width:150px"></div>
	<script>
	mmJQuery(function() {
		mmJQuery("#mm-progressbar").progressbar({
			value: 100
		});
	});
	</script>
</div>

<div id='mm-import-results'  style = 'font-size: 14px;'></div>
<?php } ?>