<?php 
$ini = new MM_InstantNotification($p->id);
?>
<div id="mm-form-container">
<input type='hidden' id='mm-id' value='<?php echo $ini->getId(); ?>' />
<table width='95%' cellspacing="10">
<tr>
	<td>
		Event Name
	</td>
	<td>
		<?php echo $ini->getEventName(); ?>
	</td>
</tr>
<tr>
	<td>
		Script URL
	</td>
	<td>
		<input type='text' id='mm-script-url' value='<?php echo $ini->getScriptUrl(); ?>'  style='width: 275px' />
	</td>
</tr>
<tr>
	<td>
		Status
	</td>
	<td>
		<div id="mm-status-container">
			<input type="radio" id="mm-status-field"   name="mm-status-field"  value="active" <?php echo (($ini->getStatus()=="1" || $ini->getStatus() == "")?"checked":""); ?> onchange="mmjs.setStatusField()"  /> Active
			<input type="radio" id="mm-status-field"   name="mm-status-field" value="inactive" <?php echo (($ini->getStatus()=="0")?"checked":""); ?> onchange="mmjs.setStatusField()" /> Inactive
			<input type='hidden' name='mm-status' id='mm-status' value='' />
		</div>
	</td>
</tr>

</table>
	<script type='text/javascript'>
	mmjs.setStatusField();
	</script>
</div>