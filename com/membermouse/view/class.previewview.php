<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_PreviewView extends MM_View
{
	public function performAction($post) 
	{	
		switch($post[self::$MM_JSACTION]) 
		{
			case self::$MM_JSACTION_PREVIEW_CHANGE_MEMBERTYPE:
				return $this->changeMemberType($post);
				
			case self::$MM_JSACTION_PREVIEW_CHANGE_TAGS:
				return $this->changeAccessTags($post);
				
			case self::$MM_JSACTION_PREVIEW_HIDE:
				return $this->hideShowPreviewBar($post);
				
			case self::$MM_JSACTION_PREVIEW_SUBMIT:
				return $this->savePreviewSettings($post);
				
			default:
				return new MM_Response();
		}
	}
	
	private function savePreviewSettings($post)
	{
 	 	global $current_user;
		
 	 	$mt = MM_MemberType::getMemberTypesList(true);
		$preview = new MM_Preview();
 	 	$mat = array();
 	 	$firstElem = 0;
 	 	
 	 	//// get info
 	 	$previewObj = MM_Preview::getData();
	
 	 	if(isset($post["mm_preview_member_type"]))	
 	 	{
			$firstElem = $post["mm_preview_member_type"];
			$memberType = new MM_MemberType($firstElem);
			$mat = $memberType->getAccessTags();
	 	 	$days = (isset($post["mm_preview_days"]) && intval($post["mm_preview_days"])>0)?$post["mm_preview_days"]:0;
			
	 	 	/// user id, days, member type id, access tags list
	 	 	$tags = array();
	 	 	if(is_array($mat) || is_object($mat))
	 	 	{
		 	 	foreach($mat as $k=>$v)
		 	 	{
		 	 		$tags[$k] = $days;
		 	 	}
	 	 	}
	 	 	
	 	 	if(isset($post["preview_access_tags"]))
	 	 	{
	 	 		$selectedTags = explode(",", $post["preview_access_tags"]);
	 	 		foreach($selectedTags as $tag)
	 	 		{
		 	 		$tags[$tag] = $days;
	 	 		}
	 	 	}
	 	 	
	 	 	$preview->setData($current_user->ID,$days, $firstElem, $tags);
 	 		return new MM_Response();
 	 	}
 	 	
 	 	return new MM_Response("Unable to save preview settings", MM_Response::$ERROR);
	}

	public function hideShowPreviewBar($post)
	{
		if(isset($post["mm_should_show"]))
		{
			MM_OptionUtils::setOption("mm-show_preview", $post["mm_should_show"]);
		}
		return new MM_Response();
	}
	
	public static function show()
	{	
 	 	global $current_user;
		
 	 	$mt = MM_MemberType::getMemberTypesPostAccess();
		$preview = new MM_Preview();
 	 	$mat = array();
 	 	$firstElem = 0;
 	 	
 	 	//// get info
 	 	$previewObj = MM_Preview::getData();
 	 	if(isset($_POST["mm-preview_btn"]))	
 	 	{
			$firstElem = $_POST["mm-preview-member_type"];
			$memberType = new MM_MemberType($firstElem);
			$mat = $memberType->getAccessTags();
	 	 	$days = (isset($_POST["mm-preview-days"]) && intval($_POST["mm-preview-days"])>0)?$_POST["mm-preview-days"]:0;
			
	 	 	/// user id, days, member type id, access tags list
	 	 	$tags = array();
	 	 	if(is_array($mat) || is_object($mat))
	 	 	{
		 	 	foreach($mat as $k=>$v)
		 	 	{
		 	 		$tags[$k] = $days;
		 	 	}
	 	 	}
	 	 	
	 	 	if(isset($_POST["preview_access_tags"]) && is_array($_POST["preview_access_tags"]))
	 	 	{
	 	 		foreach($_POST["preview_access_tags"] as $selectedTags)
	 	 		{
		 	 		$tags[$selectedTags] = $days;
	 	 		}
	 	 	}
	 	 	
	 	 	$preview->setData($current_user->ID,$days, $firstElem, $tags);
 	 	}
 	 	else if(!$previewObj)
 	 	{
 	 		$firstElem = key($mt);
			$memberType = new MM_MemberType($firstElem);
			$mat = $memberType->getAccessTags();
	 	 	$tags = array();
	 	 	if(is_array($mat) || is_object($mat))
	 	 	{
		 	 	foreach($mat as $k=>$v)
		 	 	{
		 	 		$tags[$k] = 1;
		 	 	}
	 	 	}
	 	 	$preview->setData($current_user->ID,1, $firstElem, $tags);
 	 	}
 	 	$previewObj = MM_Preview::getData();
 	 	if(empty($mat)){
			$memberType = new MM_MemberType($previewObj->getMemberTypeId());
			$mat = $memberType->getAccessTags();
 	 	}
 	 	
		$info = new stdClass();
		$info->memberTypes = MM_HtmlUtils::generateSelectionsList($mt,$previewObj->getMemberTypeId());
		
		$at = MM_AccessTag::getAccessTagsPostAccess();
		
		if(empty($at) && empty($mt)){
			return "";
		}
		
		$selectedTags = $previewObj->getAccessTags();
		
		$sel = array();
		if(is_array($selectedTags))
		{
			foreach($selectedTags as $tag)
			{
				if(!empty($tag)){
					$sel[$tag] = $tag;
				}
			}	
		}
		if(is_array($mat))
		{
			foreach($mat as $key=>$v)
			{
				if(!empty($key)){
					$sel[$key] =$v;
				}
			}
		}
		
		$info->count_tags = count($at);
		$info->count_applied =count($sel);
		
		$info->accessTags = MM_HtmlUtils::generateSelectionsList($at, $sel,$mat);
		
		//days
		$access_arr = array(
			'member_type'=>array($previewObj->getMemberTypeId()),
			'access_tag'=>$sel,
		);
		$info->days = self::getDaysOptions($access_arr, $previewObj->getDays());
		$info->imageUrl = MM_Utils::getImageUrl('user');
		echo MM_TEMPLATE::generate(MM_MODULES."/preview.php", $info);
	}
	
	/*
	 * Needs to return pre-selected access tags (if any)
	 * Needs to return days associated with access tags (if any)
	 */
	private function changeMemberType($post)
	{
		if(!isset($post["member_type_id"]) || (isset($post["member_type_id"]) && intval($post["member_type_id"])<=0))
		{
			return new MM_Response("Invalid Member Type ID!", MM_Response::$ERROR);
		}
		$memberType = new MM_MemberType($post["member_type_id"]);
		if($memberType->getId()>0)
		{	
			$at = MM_AccessTag::getAccessTagsPostAccess();
			$mat = $memberType->getAccessTags();
			//$accessTags = MM_HtmlUtils::createCheckboxGroup($at, "preview_access_tags[]", $mat, "", $mat, "mmPreviewJs.changeAccessTags();");
			$accessTags = MM_HtmlUtils::generateSelectionsList($at, $mat,$mat);
			
			//days
			$access_arr = array(
				'member_type'=>array($post["member_type_id"]),
				'access_tag'=>array_keys($mat),
			);
			$days = self::getDaysOptions($access_arr);
			return new MM_Response(array('access_tags'=>$accessTags,'days'=>$days));
		}
		return new MM_Response("Invalid Member Type ID!", MM_Response::$ERROR);
	}
	
	private static function getDaysOptions($access_arr, $selected=null)
	{
		if(!is_array($access_arr))
			return "";
		
		$allDays = array();	
		foreach($access_arr as $type=>$idObj)
		{
			foreach($idObj as $id){
				$allDays[] = MM_ProtectedContentEngine::getPostDays($type, $id);
			}
		}
		$daysArr = array();
		$daysArr[0] = 0;
		foreach($allDays as $days)
		{
			foreach($days as $day)
				$daysArr[$day] = $day;
		}
		ksort($daysArr);
		return MM_HtmlUtils::generateSelectionsList($daysArr, $selected);
	}
	
	/*
	 * This should append extra days (if any)
	 */
	private function changeAccessTags($post)
	{
		if(!isset($post["preview_access_tags"]) || (isset($post["preview_access_tags"]) && empty($post["preview_access_tags"])))
		{
			return new MM_Response();
		}
		if(!isset($post["member_type_id"]) || (isset($post["member_type_id"]) && intval($post["member_type_id"])<=0))
		{
			return new MM_Response("Invalid Member Type ID!", MM_Response::$ERROR);
		}
		$accessTags = explode(",", $post["preview_access_tags"]);
		
		$sel = array();
		foreach($accessTags as $tag)
		{
			$sel[$tag] = $tag;
		}
		
		//days
		$access_arr = array(
			'member_type'=>array($post["member_type_id"]),
			'access_tag'=>$accessTags,
		);
		$days = self::getDaysOptions($access_arr);
		
		$info = new stdClass();
		$at = MM_AccessTag::getAccessTagsPostAccess();
		$memberType = new MM_MemberType($post["member_type_id"]);
		$mat = $memberType->getAccessTags();
		foreach($mat as $k=>$v)
		{
			$sel[$k]=$v;
		}
		//$accessTags = MM_HtmlUtils::createCheckboxGroup($at, "preview_access_tags[]", $sel, "", $mat, "mmPreviewJs.changeAccessTags();");
		$accessTags = MM_HtmlUtils::generateSelectionsList($at, $sel, $mat);
		return new MM_Response(array('access_tags'=>$accessTags,'days'=>$days));
	}
	
}