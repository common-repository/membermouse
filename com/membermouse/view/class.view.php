<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
abstract class MM_View
{
	// TODO MATT setup js vars to read from constants and dynamically use in mm-core.js
	public static $MM_JSACTION = "mm_action";
	public static $MM_JSACTION_GATEWAY_OPTIONS = "gateway_options";
	public static $MM_JSMODULE = "mm_module";
	public static $MM_JSACTION_SAVE = "save";
	public static $MM_JSACTION_GATEWAY = "getGateway";
	public static $MM_JSACTION_CSV_IMPORT = "getCSVMembers";
	public static $MM_JSACTION_CONFIRM_SSL = "confirm_ssl";
	public static $MM_JSACTION_SEND_PASSWORD = "sendPasswordEmail";
	public static $MM_JSACTION_ACCESS_RIGHTS_ADD = "addAccessRights";
	public static $MM_JSACTION_ACCESS_RIGHTS_DIALOG = "accessRightsDialog";
	public static $MM_JSACTION_ACCESS_RIGHTS_UPDATE = "updateAccessRights";
	public static $MM_JSACTION_ACCESS_RIGHTS_UPDATE_DIALOG = "updateAccessRightsDialog";
	public static $MM_JSACTION_CONFIRM_AT_CANCEL = "confirmAccessTagCancel";
	public static $MM_JSACTION_CHOOSE_FORM = "chooseForm";
	public static $MM_JSACTION_UNIT_TEST_GROUP1 = "runGroup1";
	public static $MM_JSACTION_UNIT_TEST_GROUP2 = "runGroup2";
	public static $MM_JSACTION_UNIT_TEST_GROUP3 = "runGroup3";
	public static $MM_JSACTION_TEST_CALL_API = "callApiMethod";
	public static $MM_JSACTION_REMOVE = "remove";
	public static $MM_JSACTION_SHOW_DIALOG = "showDialog";
	public static $MM_JSACTION_REFRESH_VIEW = "refreshView";
	public static $MM_JSACTION_SET_DEFAULT = "setDefault";
	public static $MM_JSACTION_SET_CONFIRM = "forceConfirm";
	public static $MM_JSACTION_SYNC = "syncLimeLight";
	public static $MM_JSACTION_RESET_FORM = "resetForm";
	public static $MM_JSACTION_FF_DIALOG = "ffDialog";
	public static $MM_JSACTION_FF = "ffMembership";
	public static $MM_JSACTION_SEARCH = "search";
	public static $MM_JSACTION_UNINSTALL = "uninstall";
	public static $MM_JSACTION_PLACE_NEW_ORDER = "placeNewOrder";
	public static $MM_JSACTION_UPDATE_MEMBER = "updateMember";
	public static $MM_JSACTION_LOCK_ACCOUNT = "lockAccount";
	public static $MM_JSACTION_UNLOCK_ACCOUNT = "unlockAccount";
	public static $MM_JSACTION_CANCEL_MEMBERSHIP = "cancelMembership";
	public static $MM_JSACTION_ACTIVATE_MEMBERSHIP = "activateMembership";
	public static $MM_JSACTION_CHANGE_MEMBERSHIP = "changeMembership";
	public static $MM_JSACTION_PAUSE_MEMBERSHIP = "pauseMembership";
	public static $MM_JSACTION_GET_PRODUCT_NAME = "getProductName";
	public static $MM_JSACTION_ACTIVATE_ACCESS_TAG = "activateAccessTag";
	public static $MM_JSACTION_DEACTIVATE_ACCESS_TAG = "deactivateAccessTag";
	public static $MM_JSACTION_ATTACH_ORDER = "attachOrder";
	public static $MM_JSACTION_NEXT_STEP = "getNextStep";
	public static $MM_JSACTION_PREV_STEP = "getPrevStep";
	public static $MM_JSACTION_GET_MEMBER_TYPE = "getMemberTypeInfo";
	public static $MM_JSACTION_GET_LOOKUP_GRID = "getLookupGrid";
	public static $MM_JSACTION_DISPLAY_VIDEO = "displayVideo";
	public static $MM_JSACTION_SAVE_TERMS = "saveTerms";
	public static $MM_JSACTION_VERIFY_LL = "verifyLL";
	public static $MM_JSACTION_SAVE_ADMIN = "saveSite";
	public static $MM_JSACTION_CHANGE_COREPAGE = "changeCorePage";
	public static $MM_JSACTION_PREVIEW_CHANGE_MEMBERTYPE = "previewChangeMemberType";
	public static $MM_JSACTION_PREVIEW_CHANGE_TAGS = "previewChangAccessTags";
	public static $MM_JSACTION_PREVIEW_HIDE = "togglePreviewBar";
	public static $MM_JSACTION_PREVIEW_SUBMIT = "savePreview";
	public static $MM_JSACTION_MEMBERSHIP_CHANGE_DIALOG = "changeMembershipDialog";
	public static $MM_JSACTION_MEMBERSHIP_CHANGE = "changeMembership";
	public static $MM_JSACTION_MEMBERSHIP_CANCEL_DIALOG = "cancelDialog";
	public static $MM_JSACTION_MEMBERSHIP_CANCEL = "cancelMembership";
	public static $MM_JSACTION_MEMBERSHIP_PAUSE_DIALOG = "pauseDialog";
	public static $MM_JSACTION_MEMBERSHIP_PAUSE = "pauseMembership";
	public static $MM_JSACTION_ONECLICK_DIALOG = "confirmOneClick";
	public static $MM_JSACTION_ONECLICK_PURCHASE = "purchaseAccessTag";
	public static $MM_JSACTION_ONECLICK_RESPONSE = "responseDialog";
	public static $MM_JSACTION_IMPORT_DIALOG = "getImportForm";
	public static $MM_JSACTION_IMPORT_FIND = "findMembers";
	public static $MM_JSACTION_IMPORT_DISPLAY_MEMBERS = "getImportMemberDetails";
	public static $MM_JSACTION_IMPORT_MEMBERS = "importMembers";
	public static $MM_JSACTION_SITE_REMOVE_CAMPAIGN = "removeCampaign";
	public static $MM_JSACTION_SITE_REFRESH_CAMPAIGN = "refreshCampaignList";
	public static $MM_JSACTION_SEND_TEST_NOTIFY = "sendTestNotification";
	public static $MM_JSACTION_DETERMINE_CAMPAIGN = "determineProductCampaign";
	
	function __construct()
 	{
 		// do nothing
 	}
 	
 	public function callMethod($post) 
 	{
 		if(isset($post["method"]))
		{
			if(method_exists($this, $post["method"])) {
				return call_user_func_array(array($this, $post["method"]), array($post));
			}
		}
		
		return array();
 	}
	
	public function performAction($post) 
	{	
		if(isset($post[self::$MM_JSACTION])) 
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_REFRESH_VIEW:
					return $this->refreshView($post);
				
				case self::$MM_JSACTION_SHOW_DIALOG:
					return $this->showDialog($post);
					
				default:
					return "";
			}
		}
		else 
		{
			return new MM_Response("MM_View.performAction(): '".self::$MM_JSACTION."' is required", MM_Response::$ERROR);
		}
	}
 	
	private function showResponseDialog($post)
	{
		$info->message = (!isset($post["message"]))?"Oh, something bad happened":$post["message"];
 		$msg = MM_TEMPLATE::generate(MM_MODULES."/response.php", $info);
 		return new MM_Response($msg);
	}
	
	protected function refreshView($post)
	{
		ob_start();
		
		$crntModule = $this->getModule($post,"refreshView");
		
		if($crntModule != "") {
			include($module=MM_MODULES."/".$crntModule.".php");
		}
		else {
			$errorMsg = "MM_View.refreshView(): requested module does not exist".$post[self::$MM_JSMODULE];
			include($module=MM_MODULES."/".MM_MODULE_ERROR.".php");
		}
		
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	protected function showError($errorMsg)
	{
		ob_start();
		include($module=MM_MODULES."/".MM_MODULE_ERROR.".php");
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	private function showDialog($post)
	{
		$info = new stdClass();
		
		foreach($post as $key=>$value)
		{
			$info->$key = $value;
		}
		
		if(!isset($info->id)) {
			$info->id = "";
		}
		
		$crntModule = $this->getModule($post);
		
		if($crntModule != "") {
			return MM_TEMPLATE::generate(MM_MODULES."/".$crntModule.".dialog.php", $info);
		}
		else {
			return new MM_Response("MM_View.showDialog(): {$post[self::$MM_JSMODULE]} requested module does not exist", MM_Response::$ERROR);
		}
	}
	
	private function getModule($post, $action="")
	{
		global $mmSite;
		
		if($this instanceof MM_MemberTypesView) {
			return MM_MODULE_MEMBER_TYPES;
		}
		else if($action=="refreshView" && $this instanceof MM_SiteMgmtView &&  $mmSite->isMM() && (isset($post[self::$MM_JSMODULE]) && $post[self::$MM_JSMODULE]=='sitemgmt')){

			return MM_MODULE_SITE_MANAGEMENT_DIALOG;
		}
		else if($this instanceof MM_SiteMgmtView && ((isset($post[self::$MM_JSMODULE]) && $post[self::$MM_JSMODULE] != "sitemgmt") || !isset($post[self::$MM_JSMODULE]))) {
			if(is_admin() && $mmSite->isMM()){
				
				return ($action=="refreshView")?MM_MODULE_SITE_MANAGEMENT:MM_MODULE_SITE_MANAGEMENT_DIALOG;
			}
			else{
				return MM_MODULE_SITE_MANAGEMENT_DIALOG;
			}
		}
		else if($this instanceof MM_ApiView) {
			return MM_MODULE_API;
		}
		else if($this instanceof MM_ProductView) {
			return MM_MODULE_PRODUCTS;
		}
		else if($this instanceof MM_CampaignSettingsView) {
			return MM_Session::value(MM_Session::$KEY_CAMPAIGN_SETTINGS_ID);
			
		}
		else if($this instanceof MM_AccessLogView) {
			return MM_MODULE_LOGS;
		}
		else if($this instanceof MM_SSLView){
			return MM_MODULE_SETTINGS_SSL;
		}
		else if($this instanceof MM_RetentionReportsView){
			return MM_MODULE_REPORTS;
		}
		else if($this instanceof MM_DeliveryScheduleManagerView){
			return MM_MODULE_DELIVERY_SCHEDULE_MANAGER;
		}
		else if($this instanceof MM_UnitTestView) {
			return MM_MODULE_UNIT_TEST;
		}
		else if($this instanceof MM_InstantNotificationView) {
			return MM_MODULE_INSTANT_NOTIFICATION;
		}
		else if($this instanceof MM_CustomFieldView) {
			return MM_MODULE_CUSTOM_FIELD;
		}
		else if($this instanceof MM_ApiTestView) {
			return MM_MODULE_API_TEST;
		}
		else if($this instanceof MM_AccessTagsView) {
			return MM_MODULE_ACCESS_TAGS;
		}
		else if($this instanceof MM_AccountTypesView) {
			return MM_MODULE_ACCOUNT_TYPES;
		}
		else if($this instanceof MM_EmailAccountsView) {
			return MM_MODULE_EMAIL_ACCOUNTS;
		}
		else if($this instanceof MM_MembersView) {
			if($action=="import"){
				return MM_MODULE_IMPORT_MEMBERS;
			}
			return MM_MODULE_BROWSE_MEMBERS;
		}
		else if($this instanceof MM_LimeLightView) {
			return MM_MODULE_LIMELIGHT;
		}
		else if($this instanceof MM_AccessRightsView || $this instanceof MM_CorePagesView) {
			return MM_MODULE_META;
		}
		else if($this instanceof MM_UninstallView) {
			return MM_MODULE_SETTINGS_UNINSTALL;
		}
		else if($this instanceof MM_DashboardView) {
			return MM_MODULE_DASHBOARD_VIEW;
		}
		else 
		{
			if(isset($post[self::$MM_JSMODULE])) {
				return $post[self::$MM_JSMODULE];
			} 
			else {
				return "";
			}
		}
	}
	
	public function uploadBadge() 
	{
		$error = "";
		$msg = "";
		$fileElementName = 'fileToUpload';
		
		if(!empty($_FILES[$fileElementName]['error']))
		{
			switch($_FILES[$fileElementName]['error'])
			{
	
				case '1':
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
					
				case '2':
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
					
				case '3':
					$error = 'The uploaded file was only partially uploaded';
					break;
					
				case '4':
					$error = 'No file was uploaded';
					break;
	
				case '6':
					$error = 'Missing a temporary folder';
					break;
					
				case '7':
					$error = 'Failed to write file to disk';
					break;
					
				case '8':
					$error = 'File upload stopped by extension';
					break;
					
				case '999':
				default:
					$error = 'No error code avaiable';
			}
		}
		elseif(empty($_FILES['fileToUpload']['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none')
		{
			$error = 'No file was uploaded';
		} 
		else 
		{
				$msg .= " File Name: " . $_FILES['fileToUpload']['name'] . ", ";
				$msg .= " File Size: " . @filesize($_FILES['fileToUpload']['tmp_name']);
				
				//for security reasons, we force the removal of all uploaded files
				$overrides = array('test_form' => false);
				$file=wp_handle_upload($_FILES['fileToUpload'], $overrides);
				
				@unlink($_FILES['fileToUpload']);
				
				$fileExists = false;
				if(file_exists($file['file'])){
					$fileExists = true;	
				}
				
				if(!isset($file["error"]) && isset($file["url"]) && $fileExists) {
					echo "<script type='text/javascript'>top.mmjs.stopUpload('1','".$file["url"]."','".$file["file"]."');</script>";
				} 
				else if(!$fileExists){
					echo "<script type='text/javascript'>top.mmjs.stopUpload('0','".preg_replace("/[\'\"]+/","", "File was not uploaded successfully, please check directory permissions.")."');</script>";
				}
				else {
					echo "<script type='text/javascript'>top.mmjs.stopUpload('0','".preg_replace("/[\'\"]+/","", $file["error"])."');</script>";	
				}
		}
	}
 	
 	public function getData($tableName, $fields=null, MM_DataGrid $dg=null, $where = "", $getTotal=false)
 	{
		global $wpdb;
		
		$columns = (is_null($fields)) ? "tbl.*" : implode(",", $fields);
		
 		$sqlResultCount = "SELECT count(distinct id) as total FROM ".$tableName;
		if(!empty($where)){
			if(preg_match("/^(where)/", strtolower(trim($where)))){
				$sqlResultCount.= " {$where} ";
			}	
			else{
				$sqlResultCount.= " where {$where} ";
			}
		}
		
		$countRow = $wpdb->get_row($sqlResultCount);
		
		if($countRow) {
			$sql = "SELECT '{$countRow->total}' as total, ".$columns." FROM ".$tableName." as tbl ";
		}
		else {
			$sql = "SELECT ".$columns." FROM ".$tableName." ";
		}
		
		if(!empty($where)){
			if(preg_match("/^(where)/", strtolower(trim($where)))){
				$sql.= " {$where} ";
			}	
			else{
				$sql.= " where {$where} ";
			}
		}
		
		if(!is_null($dg) && !is_null($dg->sortBy) && !empty($dg->sortBy)) {
			$sql.= "ORDER BY {$dg->sortBy} {$dg->sortDir}";
		}
		
		if($getTotal){
			$result = $wpdb->get_results($sql);
			return $result;
		}
		
		if(!is_null($dg)) {
			$sql .= $dg->getLimitSql();
		}
		
		LogMe::write("View.getData() : ".$sql);
		$result = $wpdb->get_results($sql);
		
		if(!$result || is_null($result)) {
			return array();
		}
		
		return $result;
 	}
}
?>