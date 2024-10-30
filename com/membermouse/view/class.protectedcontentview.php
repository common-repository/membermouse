<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 class MM_ProtectedContentView
 {

 	public function postPublishingBox($post)
 	{	
 		$info = new stdClass();
 		
 		///initialization
 		$info->existing_access_rights = "";
 		$info->existing_corepage_features = "";
 		$info->mm_core_pages_meta_style = "";
 		$info->requiredTags = "";
 		$info->is_free = 'paid';
 		
 		if(!isset($post->page_type))
 			$post->page_type = null;

 		if(!MM_AccessRightsView::isPage($post->page_type))
 		{
 			$info->mm_core_pages_meta_style = "display: none;";
 		}
 		else
 		{
 			///// setup core page dropdown
 			$default_pages = MM_CorePage::getDefaultPages();
 			$defaultPageProperties = null;
 			if(MM_CorePage::isDefaultCorePage($post->ID))
 				$defaultPageProperties = MM_CorePage::getDefaultCorePageSettingsById($post->ID);
 			
 			$pages = array();
 			foreach($default_pages as $row)
 			{
 				if(isset($defaultPageProperties->core_page_type_id) && intval($defaultPageProperties->core_page_type_id)>0 && $row->id == $defaultPageProperties->core_page_type_id)
 				{
 					$pages[$row->id] = $row->name;	
 				}
 				else if(MM_CorePage::isAvailable($row->id, $post->ID))
 				{
 					$pages[$row->id] = $row->name;	
 				}
 			}
 			
 			$page_settings = MM_CorePage::getCorePageSettingsByPageID($post->ID);
 			$corePageTypeId = array();
 			if(is_array($page_settings))
 			{
 				if(count($page_settings)==1)
 				{
 					$page_setting = $page_settings[0];
	 				$corePageTypeId = $page_setting->core_page_type_id;
	 				
					$default = MM_CorePage::getDefaultCorePageByType($corePageTypeId);
	 				
					if($default->page_id == $post->ID)
	 				{
	 					$info->default_icon = MM_Utils::getImageUrl('default_flag');
	 				}
 				}
 				else
 				{	
	 				foreach($page_settings as $setting)
	 				{
	 					$corePageTypeId = $setting->core_page_type_id;
	 				}
					$default = MM_CorePage::getDefaultCorePageByType($corePageTypeId);
					if(isset($post->ID) && isset($default->page_id))
					{	
						if($default->page_id == $post->ID)
		 				{
		 					$info->default_icon = MM_Utils::getImageUrl('default_flag');
		 				}
					}
 				}
 			}
 			
 			if($corePageTypeId==MM_CorePageType::$FREE_CONFIRMATION){
 				$corePageTypeId = MM_CorePageType::$PAID_CONFIRMATION;
 				$info->is_free = 'free';
 			}

 			$info->corePageTypeId = $corePageTypeId;
 			
 			$requiredTags = MM_CorePageEngine::getRequiredTags($corePageTypeId);
 			foreach($requiredTags as $tag)
 			{
 				if($tag->is_global == '1' || ($tag->is_global=='0' && (!empty($info->default_icon))))
 				{
 					$showRequiredTag = true;
					if(MM_CorePageEngine::isErrorCorePage($post->ID)){
						$cpe = new MM_CorePageEngine();
						if($cpe->hasReferences($post->ID)){
	 						$showRequiredTag = false;		
						}
					}
					if($showRequiredTag){
 						$info->requiredTags .= "<img src='".MM_Utils::getImageUrl('exclamation')."' style='vertical-align: middle;' /> [{$tag->name}] is required<br />";
					}	
 				}
 			}
 			$info->existing_corepage_features = MM_HtmlUtils::generateSelectionsList($pages, $corePageTypeId);
 		}
 		
 		///grab some data for access rights
		$pc = new MM_ProtectedContentEngine();
		$rows = $pc->getPostAccessRights($post->ID);
		LogMe::write("postPublishingBox() : ".json_encode($rows));
		foreach($rows as $row)
		{
			$row->edit_icon = MM_Utils::getImageUrl('edit');
			if($row->access_type=="member_type")
				$row->type_icon = MM_Utils::getImageUrl('user');
			else
				$row->type_icon = MM_Utils::getImageUrl('tag');
				
			$row->delete_icon = MM_Utils::getImageUrl('delete');
			
			$info->existing_access_rights .= MM_TEMPLATE::generate(MM_TEMPLATE_META."/accessrow.html.php", $row);
		}	
 		
		// Not using MM_Response because this is not a AJAX response.
		echo MM_TEMPLATE::generate(MM_MODULES."/page_meta.php", $info);
 	}
 }