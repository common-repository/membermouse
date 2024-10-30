<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<table id='mm-one-click-table' style='font-size: 14px;'>
<tr>
	<td>Please confirm that you want to purchase this product.</td>
</tr>
<tr>
	<td>You will be charged $<?php echo $p->price; ?>.
	
	<input type='hidden' id='mm-price' value='<?php echo $p->price; ?>' />
	<input type='hidden' id='mm-days' value='<?php echo $p->days; ?>' />
	</td>
</tr>
</table>