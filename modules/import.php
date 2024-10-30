<script>
	mmJQuery(function() {
		mmJQuery( "#mm-tabs" ).tabs({
			ajaxOptions: {
				error: function( xhr, status, index, anchor ) {
					mmJQuery( "#test" ).html(
						"Couldn't load this tab. We'll try to fix this as soon as possible. " +
						"If this wouldn't be a demo." );
				}
			}
		});
	});
	</script>

<style>
.mm-subtabs{
	background: #eee;
}
</style>
<div id='test'></div>

<div id="mm-tabs">
	<ul style="background:transparent; border: 0px">
	<?php if(MM_Utils::isLimeLightInstall()){ ?>
		<li><a href="#mm-tabs-1">Import from Lime Light</a></li>
	<?php } ?>
		<li><a href="#mm-tabs-2">Import from CSV</a></li>
	</ul>
	<?php if(MM_Utils::isLimeLightInstall()){ ?>
	<div id="mm-tabs-1">
		<?php require_once(MM_MODULES."/import_from_ll.php"); ?>
	</div>
	<?php } ?>
	<div id="mm-tabs-2">
		<?php require_once(MM_MODULES."/import_from_csv.php"); ?>
	</div>
</div>




<div class="demo-description" style="display: none; ">
<p>Click tabs to swap between content that is broken into logical sections.</p>
</div><!-- End demo-description -->