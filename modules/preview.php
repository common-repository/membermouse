<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 
 $openStateVisibility = "display: block;";
 $closedStateVisibility = "display:none;";
 
 $showPreviewBar = MM_OptionUtils::getOption("mm-show_preview");
 if($showPreviewBar === false){
 	 MM_OptionUtils::setOption("mm-show_preview","1");
 }
 if($showPreviewBar != '1')
 {
 	$closedStateVisibility = "display:show;";
 	$openStateVisibility = "display:none;";
 }
 
 $mmControlCtrUrl = get_option("siteurl")."/wp-admin/admin.php?page=".MM_MODULE_DASHBOARD;
 
 $membertype_icon = $p->imageUrl;
 $membertypes_select = $p->memberTypes;
 $accessTagCount = $p->count_tags;
 $appliedTagCount = $p->count_applied;
 $days_select = $p->days;
 $accesstags_radios = $p->accessTags;
 $day = "";
 
 $postPage = MM_Utils::constructPageUrl();
 ?>

<div id='mm-adminpreview-open' style='<?php echo $openStateVisibility; ?> z-index:1999;'>
	<div id="mm-adminpreview-open-top">
		<a href="javascript:mmPreviewJs.closePreview();" style="cursor:pointer"><img src="<?php echo MM_Utils::getImageUrl("orange-cross") ?>" title="Hide Preview Settings" style="vertical-align: top;" /></a>
		<span class="header">MemberMouse Preview Settings</span>
		
		<a href='<?php echo $mmControlCtrUrl; ?>' class="headerLink"><img src="<?php echo MM_Utils::getImageUrl("bullet_go_blue") ?>" /> MemberMouse Control Center</a>
	</div>
	
	<div id="mm-adminpreview-open-bottom">
		<form name='mm_preview' method='post' action="<?php echo $postPage; ?>"> 
			<div style="float:left;">
				<img src="<?php echo MM_Utils::getImageUrl("user") ?>" title="Member Type" style="vertical-align: middle" />
				<select name="mm-preview-member_type" id='mm-preview-member_type' onchange="mmPreviewJs.changeMemberType()">
					<?php echo $membertypes_select; ?>
				</select> 
			</div>
			
			<div id="mm-member-options" style="float:left; margin-left:15px;">
				<?php if($accessTagCount>0){ ?>
					<div style='float:left; vertical-align:middle;'><a id='mm-showhide-preview-link' onclick="mmPreviewJs.showAccessTags()" style='cursor: pointer;'><img src='<?php echo MM_Utils::getImageUrl('tag_edit'); ?>' title='Add/Remove Access Tags' /></a></div>
					<div id='mm-applied-tag-count' style='float:left; padding-left: 5px; padding-right: 5px; vertical-align:middle;'><?php echo $appliedTagCount; ?></div> <div style='float:left;  vertical-align:middle;'>access tags applied</div>
				<?php } ?>
				<span style="margin-left: 10px;">
					<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" title="Days as member" />
					<select name="mm-preview-days" id='mm-preview-days' onchange="mmPreviewJs.enableChangeButton();" >
						<?php echo $days_select; ?>
					</select>
				</span>
				
				<input type='button' name='mm-preview_btn' id='mm-preview_btn' value='Save Settings' onclick="mmPreviewJs.savePreview()" disabled='disabled' style="margin-left: 5px;" />  
			
				<?php if($accessTagCount>0){ ?>
					<div id='mm-preview-access-tags' style='height: 70px;'>
						<div id='mm-preview-access-tag-results'	>
						<select multiple="multiple" rows='6' style='width: 98%;' id='preview_access_tags'  name='preview_access_tags[]'  onchange="mmPreviewJs.changeAccessTags();">
							<?php echo $accesstags_radios; ?>
						</select>
						</div>
					</div>
				<?php } ?>
			</div>	
		</form> 
	</div>
</div>

<div id='mm-adminpreview-closed' style="<?php echo $closedStateVisibility; ?> z-index:1999;">
	<a onclick="mmPreviewJs.openPreview();" style="cursor:pointer"><img src="<?php echo MM_Utils::getImageUrl("open") ?>" title="Show Preview Settings" /></a>
	<span class="header">MemberMouse Preview Settings</span>
</div>

