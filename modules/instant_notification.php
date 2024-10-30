<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

if(isset($_POST["save_default"])){
	$url = $_POST["mm_default_url"];
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_INI_DEFAULT_URL, $url);
}

$defaultScriptUrl = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INI_DEFAULT_URL);

$view = new MM_InstantNotificationView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "event";

$rows = array();

$headers = array
(	    
	'id'				=> array('content' => 'Event ID'),
   	'event_name'		=> array('content' => 'Event Name'),
   	'script_url'		=> array('content' => 'Script URL'),
   	'status'			=> array('content' => 'Status'),
   	'actions'			=> array('content' => 'Actions')
);

foreach($data as $key=>$item)
{	
    if(empty($item->script_url)) {
	    if($defaultScriptUrl == "") {
	    	$item->script_url = MM_NO_DATA;
	    }
	    else {
	    	$item->script_url = $defaultScriptUrl . " <i>(default)</i>";
	    }
    }
    
	$actions = '<a title="Edit Notifiction Settings" onclick="mmjs.edit(\'mm-instant-notification-dialog\', \''.$item->id.'\', 500, 280)" style="margin-left: 5px; cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
	
	if($item->script_url!=MM_NO_DATA){
		$actions .= '<a title="Execute Test Notification" style="margin-left: 5px; cursor:pointer" onclick="mmjs.sendTestNotify(\''.$item->id .'\');"><img src="'.MM_Utils::getImageUrl("script_go").'" /></a>';
	}
	 
	
	$rows[] = array
    (
    	array('content' => $item->id),
    	array('content' => $item->event_name, 'attr' => 'class="name"'),
    	array('content' => $item->script_url, 'attr' => 'class="name"'),
    	array('content' => MM_Utils::getStatusImage($item->status)),
    	array('content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No events found.</i></p>";
}
$filePath = MM_TEMPLATE_BASE."/instant_notification_sample.php";
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_transmit'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Instant Notification Settings</h2>
    <p style="width:650px">
		When the events listed below occur, MemberMouse can call an external script of your choosing which gives you the opportunity to perform custom actions based on realtime events within MemberMouse. For example, you could send out an email, update an external database or call an API for a 3rd party system. Each event can be associated with its own external script. If an event-level script is not defined, MemberMouse will use the default script.
	</p>
	<p style="width:650px">
		When the events listed below occur, MemberMouse will call the appropriate script, pass an event ID and any relevant parameters. Download this sample script to see how you could listen for the different events and access the data passed:   
	</p>
	<p style="vertical-align:middle; font-size: 14px;">
		<img border="0" src="<?php echo MM_Utils::getImageUrl("script_code"); ?>" /> <a style="cursor:pointer" onclick="mmjs.downloadFile('<?php echo MM_MODULES_URL."/export_file.php?file_path=".urlencode($filePath); ?>');">Download Sample Script</a>
	</p>
	<form method='post' onsubmit="return mmjs.checkDefaultURL()" >
	<table>
		<tr>
			<td width="150">Default Script URL</td>
			<td><input type="text"  id='mm_default_url' name="mm_default_url" value="<?php echo $defaultScriptUrl; ?>" style="width:550px" /></td>
		</tr>
		<tr>
			<td colspan='2' ><input type='submit' name='save_default' value='Save Default URL' class="button-primary" /></td>
		</tr>
	</table>
	</form>
	<div class="mm-button-container">
		
	</div>
	
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>