<table id='mm-one-click-table' style='font-size: 14px;'>
<tr>
	<td>Please confirm that you want to purchase this product.</td>
</tr>
<tr>
	<td>You will be charged $<?php echo $p->price; ?>.</td>
</tr>
</table>

<style>
.ui-progressbar-value { background-image: url('<?php echo MM_IMAGES_URL."pbar-animated.gif" ?>'); }
</style>
<input type='hidden' id='product_id' value='<?php echo $p->product_id; ?>' />	
<input type='hidden' id='payment_method' value='<?php echo $p->payment_method; ?>' />		
<div id="mm-progressbar-container" style="display:none; margin-top: 10px;" >
	<div id="mm-progressbar" style="width:300px"></div>
	<script>
	mmJQuery(function() {
		mmJQuery("#mm-progressbar").progressbar({
			value: 100
		});
	});
	</script>
</div>