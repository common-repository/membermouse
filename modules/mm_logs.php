<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$view = new MM_AccessLogView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "Access Events";

$rows = array();

$headers = array
(	    
	'id'				=> array('content' => 'ID'),
   	'url'				=> array('content' => 'URL'),
   	'referrer'			=> array('content' => 'Referrer'),
   	'ip'				=> array('content' => 'IP'),
   	'date_added'		=> array('content' => 'Date'),
   	'user_id'			=> array('content' => 'Member'),
);

foreach($data as $key=>$item)
{	
    // Actions
	$actions = '';
	$user = new MM_User($item->user_id);
	$userLink = "<a href='?page=".MM_MODULE_MANAGE_MEMBERS."&module=details_general&user_id=".$item->user_id."'>".$user->getUsername()."</a>";
    $rows[] = array
    (
    	array('content' => $item->id),
    	array('content' => $item->url, 'attr' => 'class="name"'),
    	array('content' => $item->referrer, 'attr' => 'class="name"'),
    	array('content' => $item->ip, 'attr' => 'class="name"'),
    	array('content' => Date("m/d/Y", strtotime($item->date_added)), 'attr' => 'class="name"'),
    	array('content' => $userLink, 'attr' => 'class="name"'),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No access events found.</i></p>";
}
?>
<div class="wrap">
    <h2 class="mm-header-text">Access Log</h2>
	
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>