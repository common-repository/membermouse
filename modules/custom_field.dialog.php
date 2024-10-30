<?php 
$customField = new MM_CustomField($p->id);
$isRequired = ($customField->getRequired()!='0')?"checked":"";
$showOnReg = ($customField->getShowOnReg()!='0')?"checked":"";
$disabled = "";
if($customField->isValid()){
	if($customField->hasAssociation()){
		$disabled = "disabled='disabled'";
	}
}
?>
<div id="mm-form-container">
<input type='hidden' id='mm-id' value='<?php echo $customField->getId(); ?>' />
<table width='95%'>
<tr>
	<td width='175'>
		Name
	</td>
	<td>
		<input type='text' id='mm-field-name' value='<?php echo $customField->getFieldName(); ?>' <?php echo $disabled; ?>  style='width: 225px;' /><br />
		<i>i.e. cell_phone</i>
	</td>
</tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr>
	<td>
		Label
	</td>
	<td>
		<input type='text' id='mm-field-label' value='<?php echo $customField->getFieldLabel(); ?>'  style='width: 225px;'  /><br />
		<i>i.e. Cell Phone</i>
	</td>
</tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr>
	<td>
		Is Required
	</td>
	<td>
		<input type='radio' id='mm-is-required-field' name='mm-is-required-field' value='1' <?php echo (($customField->getRequired()!='0')?"checked":""); ?> onchange="mmjs.setIsRequired()"  /> Yes
		<input type='radio' id='mm-is-required-field' name='mm-is-required-field'  value='0' <?php echo (($customField->getRequired()=='0')?"checked":""); ?> onchange="mmjs.setIsRequired()"  /> No
		<input type='hidden' id='mm-is-required' value='<?php echo $customField->getRequired(); ?>' /> 
	</td>
</tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr>
	<td>
		Show On Registration
	</td>
	<td>
		<input type='radio' id='mm-show-on-reg-field' name='mm-show-on-reg-field' value='1' <?php echo (($customField->getShowOnReg()!='0')?"checked":""); ?> onchange="mmjs.setShowOnReg()" /> Yes
		<input type='radio' id='mm-show-on-reg-field' name='mm-show-on-reg-field' value='0' <?php echo (($customField->getShowOnReg()=='0')?"checked":""); ?> onchange="mmjs.setShowOnReg()" /> No
		<input type='hidden' id='mm-show-on-reg' value='<?php echo $customField->getShowOnReg(); ?>' />
	</td>
</tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr>
	<td>
		Show On My Account
	</td>
	<td>
		<input type='radio' id='mm-show-on-myaccount-field' name='mm-show-on-myaccount-field' value='1' <?php echo (($customField->getShowOnMyAccount()!='0')?"checked":""); ?> onchange="mmjs.setShowOnMyAccount()" /> Yes
		<input type='radio' id='mm-show-on-myaccount-field' name='mm-show-on-myaccount-field' value='0' <?php echo (($customField->getShowOnMyAccount()=='0')?"checked":""); ?> onchange="mmjs.setShowOnMyAccount()" /> No
		<input type='hidden' id='mm-show-on-myaccount' value='<?php echo $customField->getShowOnMyAccount(); ?>' />
	</td>
</tr>

</table>
</div>
<script type='text/javascript'>
mmjs.setIsRequired();
mmjs.setShowOnReg();
mmjs.setShowOnMyAccount();
</script>