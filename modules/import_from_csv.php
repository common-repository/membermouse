<?php
$memberTypes = MM_MemberType::getMemberTypesList(true);
$memberTypesSelect = MM_HtmlUtils::generateSelectionsList($memberTypes);
?>
<table>
	<tr>
		<td valign='top'>
		Import Members As
		</td>
		<td valign='top'>
		
		<select id='mm-member-type'>
			<?php echo $memberTypesSelect; ?>
		</select><br /><br />
		<img src='<?php echo MM_Utils::getImageUrl('information'); ?>' style='vertical-align: middle' /> Importing to a paid member type will not initiate billing
		</td>
	</tr>
	<tr>
		<td colspan='2'>&nbsp;</td>
	</tr>
	<tr>
		<td width='155px;'>	
		Line Delimiter 
		</td>
		<td>
			<select id='mm-delim'>
				<option value='newline'>New Line Character (\n)</option>
				<option value='creturn'>Carriage Return (\r)</option>
				<option value='mix'>Both (\n\r)</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan='2'>&nbsp;</td>
	</tr>
	<tr>
		<td>	
		First Row is Header
		</td>
		<td>
			<input type='checkbox' id='mm-first-row-header' />
		</td>
	</tr>
	<tr>
		<td colspan='2'>&nbsp;</td>
	</tr>
	<tr>
		<td colspan='2'>
		<img src='<?php echo MM_Utils::getImageUrl("page_white_excel"); ?>' style='vertical-align: middle;' /> <a style='cursor:pointer; text-decoration: underline;' onclick="mmjs.downloadTemplate()">Download CSV Template</a>
		</td>
	</tr>
	<tr>
		<td colspan='2'>&nbsp;</td>
	</tr>
	<tr>
		<td colspan='2'>
		<div id="mm-badge-container" style='display:none;'>
				
					<div id="mm-badge" style='float:left;'></div>
					<div id="mm-badge-hidden" style='display:none;float:left;'></div>
					  <a onclick="mmjs.clearBadge()" class="button-secondary" style='margin-left: 10px; float:left;'>Clear</a>
					  <div style='clear:both;'></div>
				</div>
				<div id="mm-file-upload-container" >
		<form action="admin-ajax.php" name='badge-upload' method="post" enctype="multipart/form-data" target="upload_target" onsubmit="mmjs.startUpload();" >
	                  	<input id="fileToUpload" name="fileToUpload" type="file" size="30" />
	                  	<input type="submit" name="submitBtn" class="button-secondary" value="Upload" />
	
	                    <input type='hidden' name='method' value='uploadBadge' />
	                    <input type='hidden' name='module' value='MM_MembersView' />
	                    <input type='hidden' name='action' value='module-handle' />
	                    <iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>
	                </form>
	                </div>
		</td>
	</tr>
	<tr>
		<td colspan='2'>&nbsp;</td>
	</tr>
	<tr>
		<td colspan='2'>
		<input type='button' name='file' value="Process CSV" class="ui-button ui-widget ui-state-default ui-corner-all " onclick="mmjs.saveCsvImport();" />
		</td>
	</tr>
	
</table>

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

<div id='mm-import-results-csv'  style = 'font-size: 14px;'></div>
