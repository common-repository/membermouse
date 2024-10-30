<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_SmartTagLibraryView extends MM_View
{	
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{	
				case self::$MM_JSACTION_GET_LOOKUP_GRID:
					return $this->renderLookupGrid($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	public function getSmartTags()
	{
		global $wpdb;
		
		$groups = array();
		
		$sql = "SELECT * FROM ".MM_TABLE_SMARTTAG_GROUPS." WHERE visible='1' AND parent_id='0' ORDER BY name asc";
		
		$rows = $wpdb->get_results($sql);
		
		foreach($rows as $row) 
		{
			$group = new MM_SmartTagGroup();
			$group->setData($row);
			
			if($group->isValid()) {
				array_push($groups, $group);
			}
		}
		
		return $groups;
	}
	
	public function getLookupData($objectType, MM_DataGrid $dg=null)
	{
		global $wpdb;
		
		$where = "";
		$columns = "tbl.*";
		switch($objectType)
		{
			case MM_TYPE_POST:
				$dg->sortBy = "post_title";
				$columns = "tbl.ID as id, tbl.post_title as name";
				$tableName = $wpdb->posts;
				//$where = " tbl.ID NOT IN (select page_id from ".MM_TABLE_CORE_PAGES.")";
				$where = " tbl.post_status='publish' AND (select count(*) as total from mm_core_pages where page_id=tbl.ID)<=0";
				break;
				
			case MM_TYPE_ACCESS_TAG:
				$tableName = MM_TABLE_ACCESS_TAGS;
				break;
				
			case MM_TYPE_MEMBER_TYPE:
				$tableName = MM_TABLE_MEMBER_TYPES;
				break;
				
			case MM_TYPE_EMAIL_ACCOUNT:
				$tableName = MM_TABLE_EMAIL_ACCOUNTS;
				break;
				
			case MM_TYPE_PRODUCT:
				$tableName = MM_TABLE_PRODUCTS;
				$columns = "tbl.id, tbl.name, tbl.product_id";
				break;
				
			case MM_TYPE_CUSTOM_FIELD:
				$tableName = MM_TABLE_CUSTOM_FIELDS;
				$dg->sortBy = "field_name";
				$columns = "tbl.field_name as id, tbl.field_label as name";
				break;
		}
		
		$sqlResultCount = "SELECT count(distinct tbl.id) as total FROM ".$tableName ." tbl ";
	
		if(!empty($where)){
			$sqlResultCount.= " where {$where} ";	
		}
		
		$countRow = $wpdb->get_row($sqlResultCount);
		
		$sql = "SELECT '{$countRow->total}' as total, {$columns} FROM ".$tableName." tbl";
	
		if(!empty($where)){
			$sql .= " where {$where} ";	
		}
		
		if(!is_null($dg) && !is_null($dg->sortBy) && !empty($dg->sortBy)) {
			$sql .= " ORDER BY tbl.{$dg->sortBy} {$dg->sortDir} ";
		}
		
		$rows = $wpdb->get_results($sql);
		
		return $rows;
	}
	
	private function renderLookupGrid($post)
	{
		$info = new stdClass();
		
		foreach($post as $key=>$value)
		{
			$info->$key = $value;
		}
		
		if(isset($info->objectType)) {
			return MM_TEMPLATE::generate(MM_MODULES."/smarttag.idlookup.datagrid.php", $info);
		}
		else {
			return new MM_Response("MM_SmartTagLibraryView.renderLookupGrid(): object type missing", MM_Response::$ERROR);
		}
	}
		
	public function customMediaButtons() {
		global $post_ID, $temp_ID; 
			
		$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID); 
			
		$output = 'Upload/Insert: ';
			 
		$media_upload_iframe_src = "media-upload.php?post_id={$uploading_iframe_ID}"; 
			
		$image_upload_iframe_src = apply_filters('image_upload_iframe_src', "{$media_upload_iframe_src}&amp;type=image"); 
		$image_title = __('Add an Image');      
		$image_button = "<a href='{$image_upload_iframe_src}&amp;TB_iframe=true' id='add_image' class='thickbox' title='{$image_title}'><img src='images/media-button-image.gif' alt='{$image_title}' /></a>"; 
		$output .= apply_filters('image_upload_button', $image_button ); 

		$video_upload_iframe_src = apply_filters('video_upload_iframe_src', "{$media_upload_iframe_src}&amp;type=video"); 
		$video_title = __('Add Video'); 
 		$video_button = "<a href='{$video_upload_iframe_src}&amp;TB_iframe=true' id='add_video' class='thickbox' title='{$video_title}'><img src='images/media-button-video.gif' alt='{$video_title}' /></a>"; 
 		$output .= apply_filters('video_upload_button', $video_button ); 
 			
 		$audio_upload_iframe_src = apply_filters('audio_upload_iframe_src', "{$media_upload_iframe_src}&amp;type=audio"); 
		$audio_title = __('Add Audio'); 
		$audio_button = "<a href='{$audio_upload_iframe_src}&amp;TB_iframe=true' id='add_audio' class='thickbox' title='{$audio_title}'><img src='images/media-button-music.gif' alt='{$audio_title}' /></a>"; 
		$output .= apply_filters('audio_upload_button', $audio_button ); 
		
		$output .= self::smartTagLibraryButtons("wordpress");
		
		return $output;
	}
	
	public function addContentContainers()
	{
		echo "<div id=\"mm-smarttag-library-dialog\" title=\"SmartTag&trade; Library\"></div>";
		echo "<div id=\"mm-id-lookup-dialog\" title=\"ID Lookup\"></div>";
		
		echo "<script>";
		echo "mmJQuery(function() {";
		echo "mmJQuery(\"#mm-smarttag-library-dialog\").dialog({autoOpen: false});";
		echo "mmJQuery(\"#mm-id-lookup-dialog\").dialog({autoOpen: false});";
		echo "});";
		echo "</script>";
	}
	
	public static function smartTagLibraryButtons($contentAreaId)
	{
		$output = "";
		
		$output .= "<a onclick=\"stl_js.showSmartTagLibrary('".$contentAreaId."')\"><img src='".MM_Utils::getImageUrl("smarttag_group_closed")."' title=\"SmartTag Library\" /></a>"; 
		
		if($contentAreaId != "wordpress") {
			$output .= " ";
		}
		
		$output .= "<a onclick=\"stl_js.showIdLookup('".$contentAreaId."')\"><img src='".MM_Utils::getImageUrl("zoom")."' title=\"ID Lookup\" /></a>"; 
			
		return $output;
	}
}
?>
