<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */


$view = new MM_AccessLogView();
$dataGrid = new MM_DataGrid($_REQUEST, "date_added", "desc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "access event";

$rows = array();

$headers = array
(	    
	'id'				=> array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">ID</a>'),
   	'url'				=> array('content' => '<a onclick="mmjs.sort(\'url\');" href="#">URL</a>'),
   	'referrer'			=> array('content' => '<a onclick="mmjs.sort(\'referrer\');" href="#">Referral</a>'),
   	'ip'				=> array('content' => '<a onclick="mmjs.sort(\'ip\');" href="#">IP</a>'),
   	'event_type'				=> array('content' => '<a onclick="mmjs.sort(\'event_type\');" href="#">Event Type</a>'),
   	'date_added'		=> array('content' => '<a onclick="mmjs.sort(\'date_added\');" href="#">Date</a>'),
   	'user_id'			=> array('content' => '<a onclick="mmjs.sort(\'user_id\');" href="#">Member</a>'),
);

foreach($data as $key=>$item)
{	
	$actions = '';
	$user = new MM_User($item->user_id);
	$userLink = $user->getUsername();
	if(!MM_Utils::isAdmin($item->user_id)){
		$userLink = "<a href='?page=".MM_MODULE_MANAGE_MEMBERS."&module=details_general&user_id=".$item->user_id."'>".$user->getUsername()."</a>";
	}
	if(empty($userLink)){
		$userLink = MM_NO_DATA;
	}
	$ipaddress = "<a href='http://www.infobyip.com/ip-".$item->ip.".html' target='_blank'>".$item->ip."</a>";
	
	$eventType = "";
	$eventTypeArr = explode(" ", $item->event_type);
	foreach($eventTypeArr as $word){
		$eventType.= ucfirst($word)." ";
	}
	if(empty($item->referrer)){
		$item->referrer = MM_NO_DATA;
	}
	
    $rows[] = array
    (
    	array('content' => $item->id),
    	array('content' => $item->url, 'attr' => 'class="name"'),
    	array('content' => $item->referrer, 'attr' => 'class="name"'),
    	array('content' => $ipaddress, 'attr' => 'class="name"'),
    	array('content' => $eventType, 'attr' => 'class="name"'),
    	array('content' => Date("m/d/Y h:i a", strtotime($item->date_added)), 'attr' => 'class="name"'),
    	array('content' => $userLink, 'attr' => 'class="name"'),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No access events found.</i></p>";
}

$arr= array_merge(array("All"), MM_AccessLog::getTypesForSelect());
$eventTypes = MM_HtmlUtils::generateSelectionsList($arr,MM_Session::value("mm_accesslog_event_types"));

?>
<script type='text/javascript'>
mmJQuery(document).ready(function(){
	mmJQuery("#mm_from_date").datepicker();
	mmJQuery("#mm_to_date").datepicker();
});
</script>
<div class="wrap">
    <h2 class="mm-header-text">Access Log</h2>
			<form name='al' method='post'>
			<table cellspacing="5">
				<tr>
					<td>From</td>
					<td>
						<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
						<input id="mm_from_date"  name="mm_from_date" type="text" value="<?php echo MM_Session::value("mm_accesslog_from_date");?>" style="width: 152px" /> 
					</td>
					<td>
						Event Type
					</td>
					<td>
						<select id='mm_event_types' name='mm_event_types'>
							<?php echo $eventTypes; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>To</td>
					<td>
						<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
						<input id="mm_to_date" name="mm_to_date" type="text" style="width: 152px"  value='<?php echo MM_Session::value("mm_accesslog_to_date");?>'/>
					</td>
					<td colspan='2'></td>
				</tr>
				<tr>
					<td colspan='4'>
						<input type='submit' name='submit' value='Apply Filter' class="button-secondary" />
					</td>
				</tr>
				</table></form>
				
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>