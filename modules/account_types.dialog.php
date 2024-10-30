<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */ 
	$acctType = new MM_AccountType($p->id);
?>
<div id="mm-form-container">
	<div id="mm-messages-container"></div>
	
	<table cellspacing="10">
		<tr>
			<td>Display Name</td>
			<td><input id="mm-display-name" type="text" class="medium-text" value="<?php echo htmlentities($acctType->getName(),ENT_QUOTES); ?>"/></td>
		</tr>
		
		<tr>
			<td>Account Type Status</td>
			<td>
				<div id="mm-status-container">
					<input type="radio" name="status" value="active" onclick="mmjs.processForm()" <?php echo (($acctType->getStatus()=="1")?"checked":""); ?> /> Active
					<input type="radio" name="status" value="inactive" onclick="mmjs.processForm()" <?php echo (($acctType->getStatus()=="0")?"checked":""); ?> /> Inactive
				</div>
				
				<input id="mm-status" type="hidden" />
			</td>
		</tr>
		
		<tr>
			<td># Sites</td>
			<td>
				<input id="mm-num-sites" type="text" class="short-text" value="<?php echo $acctType->getNumSites(); ?>" />
			</td>
		</tr>
		
		<tr>
			<td># Paid Members</td>
			<td>
				<input id="mm-num-paid-members" type="text" class="short-text" value="<?php echo $acctType->getNumPaidMembers(); ?>" <?php echo (($acctType->getUnlimitedPaidMembers()=="1")?"disabled":""); ?> />
				<input id="mm-cb-unlimited-paid-members" type="checkbox" onclick="mmjs.processForm()" <?php echo (($acctType->getUnlimitedPaidMembers()=="1")?"checked":""); ?>  />
				Unlimited
				<input id="mm-unlimited-paid-members" type="hidden" />
			</td>
		</tr>
		
		<tr>
			<td># Total Members</td>
			<td>
				<input id="mm-num-total-members" type="text" class="short-text" value="<?php echo $acctType->getNumTotalMembers(); ?>" <?php echo (($acctType->getUnlimitedTotalMembers()=="1")?"disabled":""); ?> />
				<input id="mm-cb-unlimited-total-members" type="checkbox" onclick="mmjs.processForm()" <?php echo (($acctType->getUnlimitedTotalMembers()=="1")?"checked":""); ?>  />
				Unlimited
				<input id="mm-unlimited-total-members" type="hidden" />
			</td>
		</tr>
	</table>
	
	<input id='id' type='hidden' value='<?php if($acctType->getId() != 0) { echo $p->id; } ?>' />
</div>	