<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	// get default selections
	$selectedMemberType = "";
	$selectedAccessTag = "";
	
	if(isset($_REQUEST["memberTypeId"]))
	{ 
		$selectedMemberType = $_REQUEST["memberTypeId"];
		$_REQUEST["mm_member_types"] = array($selectedMemberType);
	}
	
	if(isset($_REQUEST["accessTagId"]))
	{ 
		$selectedAccessTag = $_REQUEST["accessTagId"];
		$_REQUEST["mm_access_tags"] = array($selectedAccessTag);
	}
	
?>
<div id="mm-form-container">
	<script type='text/javascript'>
		mmJQuery(document).ready(function(){
			mmJQuery("#mm-from-date").datepicker();
			mmJQuery("#mm-to-date").datepicker();
		});
	</script>
	<table>
		<tr>
			<!-- LEFT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td>From</td>
					<td>
						<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
						<input id="mm-from-date" type="text" style="width: 152px" /> 
					</td>
				</tr>
				<tr>
					<td>To</td>
					<td>
						<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
						<input id="mm-to-date" type="text" style="width: 152px" />
					</td>
				</tr>
				<tr>
					<td>Member ID</td>
					<td><input id="mm-member-id" type="text" /></td>
				</tr>
				<tr>
					<td>First Name</td>
					<td><input id="mm-first-name" type="text" /></td>
				</tr>
				<tr>
					<td>Last Name</td>
					<td><input id="mm-last-name" type="text" /></td>
				</tr>
				<tr>
					<td>Username</td>
					<td><input id="mm-username" type="text" /></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><input id="mm-email" type="text" /></td>
				</tr>
			</table>
			</td>
			
			<!-- CENTER COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td>Member Type</td>
					<td>
						<select id="mm-member-types[]" style="height:98px" multiple="multiple">
						<?php echo MM_HtmlUtils::getMemberTypesList($selectedMemberType); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Access Tags</td>
					<td>
						<select id="mm-access-tags[]" style="height:98px" multiple="multiple">
						<?php echo MM_HtmlUtils::getAccessTagsList($selectedAccessTag); ?>
						</select>
					</td>
				</tr>
			</table>
			</td>
			
			<!-- RIGHT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td>Credit Card</td>
					<td>
						<input type='text' id="mm-last-four"  value='' />
					</td>
				</tr>
				<tr>
					<td>Member Status</td>
					<td>
						<select id="mm-member-status-types[]" style="height:110px" size='5' multiple="multiple">
						<?php echo MM_HtmlUtils::getMemberStatusList(); ?>
						</select>
					</td>
				</tr>
				<?php if(MM_CustomField::hasCustomFields()){ ?>
				<tr>
					<td>Custom Field 1</td>
					<td>
						<select id="mm-member-custom-field"  onchange="mmjs.changeCustomField('mm-member-custom-field');">
							<option value=''>None</option>
							<?php echo MM_HtmlUtils::getCustomFieldsList(); ?>
						</select>
						<br />
						<input type='text' id='mm-member-custom-field-value' value='' style='width: 200px;display:none' />
					</td>
				</tr>
				<?php
						$list = MM_CustomField::getCustomFieldsList();
					 	if(count($list)>1){
						?>
				<tr>
					<td>Custom Field 2</td>
					<td>
						<select id="mm-member-custom-field2"  onchange="mmjs.changeCustomField('mm-member-custom-field2');">
							<option value=''>None</option>
							<?php echo MM_HtmlUtils::getCustomFieldsList(); ?>
						</select>
						<br />
						<input type='text' id='mm-member-custom-field2-value' value='' style='width: 200px;display:none' />
					</td>
				</tr>
					
					<?php }
					} ?>
				<tr>
					<td>Affiliate ID</td>
					<td>
						<input type='text' id='mm-affiliate-id' value='' style='width: 200px;' />
					</td>
				</tr>
					
			</table>
			</td>
		</tr>
	</table>
	
	<input type="button" class="button-primary" value="Show Members" onclick="mmjs.search(0);">
	<input type="button" class="button-secondary" value="Reset Form" onclick="mmjs.resetForm();">
</div>
