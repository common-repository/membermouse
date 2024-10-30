<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$view = new MM_ManageInstallView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "desc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "version";

$rows = array();

$headers = array
(	    
   	'version'				=> array('content' => '<a onclick="mmjs.sort(\'version\');" href="#">Version</a>'),
   	'date_added'				=> array('content' => '<a onclick="mmjs.sort(\'date_added\');" href="#">Date</a>'),
	'notes'				=> array('content' => ''),
);

foreach($data as $key=>$item)
{	
	$link = "<a onclick=\"mmjs.showReleaseNotes('".urlencode($item->version)."');\" style='cursor:pointer'>Release Notes</a>";
    // Actions
    $rows[] = array
    (
    	array('content' => $item->version, 'attr' => 'class="name"'),
    	array('content' => Date("m/d/Y", strtotime($item->date_added)), 'attr' => 'class="name"'),
    	array('content' => $link, 'attr' => 'class="name"'),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No custom fields.</i></p>";
}
?>
<div class="wrap">
    <h2 class="mm-header-text">Version History</h2>
	<div class="mm-button-container">
	</div>
	
	<div class="clear"></div>
	<div id='mm-release-notes-dialog'></div>
	<div style='width: 700px;'>
	<?php echo $dgHtml; ?>
	</div>
</div>