<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
	<?php if($p->showCsvControl =='1'){ ?>
	<a class="button-secondary" onclick="mmjs.csvExport(0);"><img src="<?php echo MM_Utils::getImageUrl('page_white_excel'); ?>" style="vertical-align: middle;" /> Export CSV</a>
	<?php } ?>
<?php if($p->showPagingControls) { echo MM_Template::generate(MM_MODULES."/datagrid.controls.php", $p); } ?>
<table <?php echo $p->datagrid->attr; ?>>
	<thead>
		<tr>
		<?php foreach($p->datagrid->headers as $key=>$header) { ?>
			<th <?php echo ((isset($header["attr"]))?$header["attr"]:""); ?>><?php echo $header["content"]; ?></th>
		<?php } ?>
		</tr>
	</thead>
	
	<?php foreach($p->datagrid->rows as $key=>$record) { ?>
	<tr>
		<?php foreach($record as $key=>$field){ ?>
				<td <?php echo ((isset($field["attr"]))?$field["attr"]:""); ?>><?php echo $field["content"]; ?></td>
		<?php } ?>
	</tr>
	<?php } ?>
	<?php if(count($p->datagrid->rows) > 15) { ?>
	<tfoot>
		<tr>
		<?php foreach($p->datagrid->headers as $key=>$header){ ?>
			<th <?php echo ((isset($header["attr"]))?$header["attr"]:""); ?>><?php echo $header["content"]; ?></th>
		<?php } ?>
		</tr>
	</tfoot>
	<?php } ?>
</table>
<?php if(count($p->datagrid->rows) > 15 && $p->showPagingControls) { echo MM_Template::generate(MM_MODULES."/datagrid.controls.php", $p); } ?>

<script>
	mmjs.setDataGridProps('<?php echo $p->sortBy; ?>', '<?php echo $p->sortDir; ?>', '<?php echo $p->crntPage; ?>', '<?php echo $p->resultSize; ?>');
</script>