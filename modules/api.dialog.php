<?php 
$api = new MM_Api($p->id);
$statusList = $api->getStatusList();
?>
<div id="mm-form-container">
<input type='hidden' id='mm-id' value='<?php echo $api->getId(); ?>' />
<table width='95%'>
<tr>
	<td>
		Name
	</td>
	<td>
		<input type='text' id='mm-name' value='<?php echo $api->getName(); ?>' />
	</td>
</tr>
<tr>
	<td>
		API Key
	</td>
	<td>
		<input type='text' id='mm-api-key' value='<?php echo $api->getApiKey(); ?>' /> <a href="#" onclick="mmjs.generateKey('mm-api-key')">Generate</a>
	</td>
</tr>
<tr>
	<td>
		API Password
	</td>
	<td>
		<input type='text' id='mm-api-secret' value='<?php echo $api->getApiSecret(); ?>' /> <a href="#" onclick="mmjs.generateKey('mm-api-secret')">Generate</a>
	</td>
</tr>
<tr>
	<td>
		Status
	</td>
	<td>
		<div id="mm-status-container">
			<input type="radio" id="mm-status-field"   name="mm-status-field"  value="active" onclick="mmjs.processForm()" <?php echo (($api->getStatus()=="1" || $api->getStatus() == "")?"checked":""); ?> onchange="mmjs.setStatusField()"  /> Active
			<input type="radio" id="mm-status-field"   name="mm-status-field" value="inactive" onclick="mmjs.processForm()" <?php echo (($api->getStatus()=="0")?"checked":""); ?> onchange="mmjs.setStatusField()" /> Inactive
			<input type='hidden' name='mm-status' id='mm-status' value='' />
		</div>
	</td>
</tr>

</table>
</div>