<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_CorePagesView extends MM_View
{	
	public function performAction($post) 
	{	
		switch($post[self::$MM_JSACTION]) 
		{
			case self::$MM_JSACTION_SHOW_DIALOG:
				return $this->changeCorePageDialog($post);
				
			case self::$MM_JSACTION_CHANGE_COREPAGE:
				return $this->changeDefaultPage($post);
				
			default:
				return "MM_CorePagesView.performAction(): action '".$post[self::$MM_JSACTION]."' is not supported";
		}
	}
 	
	public function changeCorePageDialog($post)
	{
		$rows = MM_CorePage::getAvailableWPPages();
		
		$options =  array();
		if(is_array($rows))
		{
			foreach($rows as $row)
			{
				$options[$row->id] = $row->post_title;
			}
		}
			
		$info = new stdClass();
		$info->options = MM_HtmlUtils::generateSelectionsList($options);
		$msg = MM_TEMPLATE::generate(MM_MODULES."/core_pages.change.php", $info);
		return new MM_Response($msg);
	}
	
	public function changeDefaultPage($post)
	{
		global $wpdb;
		
		if(!isset($post["new_page_id"]) || !isset($post["post_ID"]))
			return array('error'=>1);
		
		$page = MM_CorePage::getCorePage($post["post_ID"]);
		if(!$page)
			return array('error'=>'Could not find core page type id for existing page.');
			
		$corePageEngine = new MM_CorePageEngine();	
		$corePageEngine->saveDefaultPage($post["new_page_id"], $page->core_page_type_id,true);
		return new MM_Response($page->core_page_type_id);
	}
	
	public function saveCorePage($post)
	{
		if(!isset($_POST["save-mm-corepages"]) || !isset($_POST["save-mm-corepages"]["core_page_type_id"]) || !isset($_POST["post_ID"]) || (isset($_POST["post_ID"]) && intval($_POST["post_ID"])<=0))
		{
			MM_Messages::addError("Unable to save Membermouse Core Page Options: Missing core_page_type_id, post_id, or nonce was not set properly.");
			return false;
		}
		
		$corePageTypeId = $_POST["save-mm-corepages"]["core_page_type_id"];
		$postId = $_POST["post_ID"];
		if(isset($_POST["save-mm-corepages"]["confirmation_type"]) && preg_match("/(free|paid)/",$_POST["save-mm-corepages"]["confirmation_type"]))
		{
			if($_POST["save-mm-corepages"]["confirmation_type"] == "free")
				$corePageTypeId=MM_CorePageType::$FREE_CONFIRMATION;
		}
		
		$corePageEngine = new MM_CorePageEngine();	
		$requiredTags = MM_CorePageEngine::getRequiredTags($corePageTypeId);
		$content =$_POST["post_content"];
		
		/// house keeping if permissions were set.
		$protected_content = new MM_ProtectedContentEngine();
		$protected_content->removeAllRights($postId);
		
		// lets wipe all core page associations too
		if($corePageEngine->hasReferences($postId) && intval($corePageTypeId)<=0)
		{
			$corePageEngine->removeAllReferences($postId);
		}
		
		/// selected a reference type
		if(isset($_POST["save-mm-corepages"]["ref_id"]))
		{
			if(is_array($_POST["save-mm-corepages"]["ref_id"]) || $_POST["save-mm-corepages"]["ref_id"]>=0)
			{
				$refIds = $_POST["save-mm-corepages"]["ref_id"];
				$refType = $_POST["save-mm-corepages"]["ref_type"];
				
				return $corePageEngine->saveSpecificPage($postId, $corePageTypeId, $refIds, $refType);
			}
		}
		
		$hasOneTag = false;
		foreach($requiredTags as $tag)
		{
			if(!preg_match("/\[".$tag->name."\]/", $content))
			{
 				if($corePageTypeId != MM_CorePageType::$CANCELLATION){
					MM_Messages::addError("[".$tag->name."] is a required tag for this core page.");
					return false;
 				}
			}
			else{
				$hasOneTag = true;
			}
 		}
 		
 		if(!$hasOneTag && $corePageTypeId == MM_CorePageType::$CANCELLATION){
 			MM_Messages::addError("[MM_PauseMembership] or [MM_CancelMembership] is a required tag for this core page.");
			return false;
 		}
 		
 		/*
 		 */
		
		//// no reference types selected, trying to be default page?
		return $corePageEngine->saveDefaultPage($postId, $corePageTypeId);
	}
	
	public function getOptionsByCorePageType($post)
	{
		global $wpdb;
		if(isset($post["core_page_type_id"]) && intval($post["core_page_type_id"])>0)
		{
			$row = MM_CorePage::getDefaultCorePageByType($post["core_page_type_id"]);
			if(!$row)
			{
				$content = $this->showError("Could not find any core page information.");
			}
			return call_user_func_array(array($this, $this->getAjaxMethod($post["core_page_type_id"])), array($post));	
		}
		if(isset($post["core_page_type_id"]) && empty($post["core_page_type_id"]))
		{
			$content = $this->showError("Could not find Core Page ID.");
			return new MM_Response($content, MM_Response::$ERROR);
		}
		$content = $this->showError("Could not find any core page sub types.");
		return new MM_Response($content, MM_Response::$ERROR);
	}
	
	public function getAjaxMethod($corePageTypeId)
	{
		switch($corePageTypeId)
		{
			case MM_CorePageType::$ERROR:
				return "getErrorTypeDialog";
				
			case MM_CorePageType::$LIMELIGHT_SUCCESS:
			case MM_CorePageType::$LOGIN_PAGE:
			case MM_CorePageType::$FORGOT_PASSWORD:
			case MM_CorePageType::$REGISTRATION:
			case MM_CorePageType::$MY_ACCOUNT:
				return "noReferenceTypes";
			
			case MM_CorePageType::$PAID_CONFIRMATION:
			case MM_CorePageType::$FREE_CONFIRMATION:
				return "getProductDialog";
			
			case MM_CorePageType::$CANCELLATION:
			case MM_CorePageType::$LOGOUT_PAGE:
			case MM_CorePageType::$MEMBER_HOME_PAGE:
				return "getMemberTypeDialog";
		}
	}
	
	public function getData()
	{
		global $wpdb;
		
		$sql = "select id, post_title from {$wpdb->posts} p where p.post_type='page' and p.post_status IN ('publish','draft') and p.id NOT IN (select page_id from ".MM_TABLE_CORE_PAGES." c where page_id IS NOT NULL)";
	
		$rows = $wpdb->get_results($sql);
		
		$options =  array();
		if(is_array($rows))
		{
			foreach($rows as $row)
			{
				$options[$row->id] = $row->post_title;
			}
		}
		return $options;	
	}
	
	private function noReferenceTypes()
	{
		return new MM_Response();
	}
	
	private function getErrorTypeDialog($post)
	{
		global $wpdb;
		$info = new stdClass();
		
		$sel_id = $this->getPreviousSelections($post["post_ID"]); 
		
		$rows = MM_ErrorType::getAvailableErrors($sel_id);
		if(!$rows)
		{
			/// if no results, return nothing.
			return new MM_Response("Could not find any available error types.", MM_Response::$ERROR);
		}
		
		$error_types =array();
		/// set them up for Utility option
		foreach($rows as $row)
			$error_types[$row->id] = $row->name; 
			
		///create select box
		$info->options = MM_HtmlUtils::createCheckboxGroup($error_types, "save-mm-corepages[ref_id][]", $sel_id);
		$info->ref_type = "error_type";
		$content = MM_TEMPLATE::generate(MM_MODULES."/core_pages.types.php", $info);
		
		$requiredTags = "";
		if(isset($post["core_page_type_id"])){
			$requiredTagsArr = MM_CorePageEngine::getRequiredTags($post["core_page_type_id"]);
			if(count($requiredTagsArr)>0)
			{
				
	 			$showRequiredTag = true;
 				if($post["core_page_type_id"] == MM_CorePageType::$ERROR){
		 				$showRequiredTag = false;
 				}
 				
				if($showRequiredTag){
					foreach($requiredTagsArr as $tag){
						$requiredTags.="<img src='".MM_Utils::getImageUrl('exclamation')."' style='vertical-align: middle;' /> [{$tag->name}] is required<br />";
					}
				}
			}
		}
		return new MM_Response(array('content'=>$content,'requiredTags'=>$requiredTags));
	}
	
	/*
	 * Generates a dialog for member-type driven core page selections
	 */
	private function getMemberTypeDialog($post)
	{
		global $wpdb;
		
		///initialize
		$info = new stdClass();
		$mts = array();

		$member_obj = new MM_MemberType();
		$rows = $member_obj->getAvailableTypes($post["core_page_type_id"], $post["post_ID"]);
		if(!$rows)
		{
			/// if no results, return nothing.
			return new MM_Response("Could not find any member types available.", MM_Response::$ERROR);
		}
		
		$info = MM_CorePage::getCorePageInfo($post["post_ID"]);
		$shouldPreSelect = false;
		if(isset($info->core_page_type_id) && $info->core_page_type_id == $post["core_page_type_id"])
		{
			$shouldPreSelect = true;
		}
		$sel_id = 0;
		if($shouldPreSelect)
			$sel_id = $this->getPreviousSelections($post["post_ID"]); 
		
		/// set them up for Utility option
		foreach($rows as $row)
			$mts[$row->id] = $row->name; 
			
		///create select box
		$info->options = MM_HtmlUtils::createCheckboxGroup($mts, "save-mm-corepages[ref_id][]", $sel_id, "user");
		$info->ref_type = "member_type";
		$content = MM_TEMPLATE::generate(MM_MODULES."/core_pages.types.php", $info);
	
		$requiredTags = "";
		if(isset($post["core_page_type_id"])){
			$requiredTagsArr = MM_CorePageEngine::getRequiredTags($post["core_page_type_id"]);
			if(count($requiredTagsArr)>0)
			{
				foreach($requiredTagsArr as $tag){
					$requiredTags.="<img src='".MM_Utils::getImageUrl('exclamation')."' style='vertical-align: middle;' /> [{$tag->name}] is required<br />";
				}
			}
		}
		return new MM_Response(array('content'=>$content,'requiredTags'=>$requiredTags));
	}
	
	private function getPreviousSelections($page_id)
	{
		global $wpdb;
		
		$sel_id = array();
		$current_posts = MM_CorePage::getAssociatedWPCorePages($page_id);
		
		if(is_array($current_posts))
		{
			foreach($current_posts as $current_post)
			{
				if(isset($current_post->ref_id) && intval($current_post->ref_id)>0)
					$sel_id[$current_post->ref_id] = $current_post->ref_id;	
			}
		}
		return $sel_id;
	}
	
	private function getProductDialog($post)
	{
		global $wpdb;
		
		/// get member types for selection
		$products = new MM_Product();
		$rows = $products->getProductsAndAssociations($post["core_page_type_id"], $post["post_ID"]);
		if(!$rows)
		{
			/// if no results, return nothing.
			return new MM_Response("Could not find any products available.", MM_Response::$ERROR);
		}
		
		$sel_id = array();
		
		$current_posts = null;
		$shouldPreSelect = false;
		
		// lets see if this matches what we have saved and if so we will preselect, otherwise empty.
		$info = MM_CorePage::getCorePageInfo($post["post_ID"]);
		if(isset($info->core_page_type_id) && ($info->core_page_type_id == $post["core_page_type_id"] ||
					 ($info->core_page_type_id==MM_CorePageType::$FREE_CONFIRMATION && $post["core_page_type_id"]==MM_CorePageType::$PAID_CONFIRMATION) ||
					 ($info->core_page_type_id==MM_CorePageType::$PAID_CONFIRMATION && $post["core_page_type_id"]==MM_CorePageType::$FREE_CONFIRMATION)
		))
		{
			$shouldPreSelect = true;
		}
		
		///Check for a specific page already chosen for this ID
		if($shouldPreSelect)
		{
			$current_posts = MM_CorePage::getAssociatedWPCorePages($post["post_ID"]);
			if(is_array($current_posts))
			{
				foreach($current_posts as $current_post)
				{
					if(isset($current_post->ref_id) && intval($current_post->ref_id)>0)
					{ 
						$sel_id[$current_post->ref_id] = $current_post->ref_id;
						$sel_id[$current_post->ref_id."-".$current_post->ref_type] = $current_post->ref_id;
					}	
				}
			}
		}
		
		$prods = array();
		$is_free = ($post["is_free"]=="paid")?"0":"1";
		
		$width = 30;
		/// set them up for Utility option
		foreach($rows as $row)
		{ 
			$obj = new stdClass();
			if($is_free == $row->is_free)
			{
				if(empty($row->product_name)){
					continue;
				}
				
				$typeStr = "";
				if($row->ref_type=="member_type"){
					$tmpMemberType = new MM_MemberType($row->type_id);
					if($tmpMemberType->isValid()){
						$typeStr = $tmpMemberType->getName()." ("; 
					}
				}
				else if($row->ref_type=="access_tag"){
					$tmpAccessTag = new MM_AccessTag($row->type_id);
					if($tmpAccessTag->isValid()){
						$typeStr = $tmpAccessTag->getName()." ("; 
					}
				}
				$end = ((!empty($typeStr))?")":"");
				$campaignName = (!empty($row->campaign_name))?", ".$row->campaign_name:"";
				$obj->value = ((bool)$row->is_free)?$row->type_name:$typeStr.$row->product_name.$campaignName."".$end;
				$obj->alt = $typeStr.$obj->value.$end;
				$obj->image = ($row->ref_type=="member_type")?'user':'tag';
				$id = (intval($row->product_id)>0)?$row->product_id:$row->type_id;
				
				if((bool)$row->is_free)
					$prods[$id."-".$row->ref_type] = $obj;
				else
					$prods[$id] = $obj;	
			}
			else if(($is_free=="1" || $is_free==="")  && $row->is_free == "1")
			{
				$campaignName = (!empty($row->campaign_name))?", ".$row->campaign_name:"";
				$obj->value = ((bool)$row->is_free)?$row->type_name:$typeStr.$row->product_name.$campaignName."".$end;
				$obj->alt = $typeStr.$obj->value.$end;
				$obj->image = ($row->ref_type=="member_type")?'user':'tag';
				$id = (intval($row->product_id)>0)?$row->product_id:$row->type_id;
				
				if((bool)$row->is_free)
					$prods[$id."-".$row->ref_type] = $obj;
				else
					$prods[$id] = $obj;
			}
		}

		///create select box
		$info = new stdClass();
		$info->options = MM_HtmlUtils::createCheckboxGroup($prods, "save-mm-corepages[ref_id][]", $sel_id, "", null, "", $width);
		$info->ref_type = "product";
		$info->is_free = $is_free;
		$requiredTags = "";
		if(isset($post["core_page_type_id"])){
			$requiredTagsArr = MM_CorePageEngine::getRequiredTags($post["core_page_type_id"]);
			if(count($requiredTagsArr)>0)
			{
				foreach($requiredTagsArr as $tag){
					$requiredTags.="<img src='".MM_Utils::getImageUrl('exclamation')."' style='vertical-align: middle;' /> [{$tag->name}] is required<br />";
				}
			}
		}
		return new MM_Response(array('content'=>MM_TEMPLATE::generate(MM_MODULES."/core_pages.products.php", $info),'requiredTags'=>$requiredTags));
	}
	
}
?>