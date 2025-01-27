<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

 // TODO move to corepageview and protectedcontentview 
 class MM_PostHooks
 {	
 	public function pagesColumns($defaults)
 	{
 		$offset = 2; ///column offset
		$defaults = array_slice($defaults, 0, $offset, true) +
            array("core_page_type"=>__('Core Page Type')) +
            array("access_rights"=>__('Access Rights')) +
            array_slice($defaults, $offset, NULL, true);
            
	    return $defaults;
 	}
 	
 	public function checkPosts(){
 		global $current_user,$wp_query;
 		$userId = 0;
 		if(isset($current_user->ID)){
 			$userId = $current_user->ID;
 		}
 		if(is_home()){
			$protectedContent = new MM_ProtectedContentEngine();
	 		
			$posts = array();
	 		for($i=0; $i<count($wp_query->posts); $i++){
	 			$post = $wp_query->posts[$i];
		 		if($protectedContent->canAccessPost($post->ID,$userId)){
		 			$posts[] = $post;
		 		}
		 		else{
		 			$wp_query->post_count--;
		 		}
	 		}
	 		$wp_query->posts = $posts;
 		}
 	}
 	
 	public function postsColumns($defaults)
 	{
 		$offset = 2; ///column offset
		$defaults = array_slice($defaults, 0, $offset, true) +
            array("access_rights"=>__('Access Rights')) +
            array_slice($defaults, $offset, NULL, true);
            
	    return $defaults;
 	}
 	
 	public function handlePostWhere($where)
 	{
 		global $wpdb;
 		if(is_admin())
 		{
	 		$mt_sql = "";
	 		$at_sql = "";
	 		$cp_sql = "";
	 		if(isset($_GET["member_types"]) && !empty($_GET["member_types"]) && preg_match("/^[0-9]+$/", $_GET["member_types"]))
	 		{
	 			$mt_sql = " AND (
	 								({$wpdb->posts}.id IN (select post_id from ".MM_TABLE_POSTS_ACCESS." where access_type='member_type' and access_id='".$_GET["member_types"]."' ))
	 								OR
	 								({$wpdb->posts}.id IN (select page_id from ".MM_TABLE_CORE_PAGES." where ref_type='member_type' and ref_id='".$_GET["member_types"]."'))
									OR
	 								({$wpdb->posts}.id IN (select page_id from ".MM_TABLE_CORE_PAGES." where ref_type='product' and ref_id IN (select registration_product_id from ".MM_TABLE_MEMBER_TYPES." where id='{$_GET["member_types"]}' )))
	 					) ";
	 		}
	 		if(isset($_GET["access_tags"]) && !empty($_GET["access_tags"]) && preg_match("/^[0-9]+$/", $_GET["access_tags"]))
	 		{
	 			$at_sql = " AND (
	 								({$wpdb->posts}.id IN (select post_id from ".MM_TABLE_POSTS_ACCESS." where access_type='access_tag' and access_id='".$_GET["access_tags"]."' ))
	 								OR
	 								({$wpdb->posts}.id IN (select page_id from ".MM_TABLE_CORE_PAGES." where ref_type='access_tag' and ref_id='".$_GET["access_tags"]."')) 
	 								OR
	 								({$wpdb->posts}.id IN (select page_id from ".MM_TABLE_CORE_PAGES." where ref_type='product' and ref_id IN (select product_id from ".MM_TABLE_ACCESS_TAG_PRODUCTS." where access_tag_id='{$_GET["access_tags"]}' )))
	 							) ";
	 		}
	 		if(isset($_GET["core_page_types"]) && !empty($_GET["core_page_types"]) && preg_match("/(core_pages|wp_pages)/", $_GET["core_page_types"]))
	 		{
	 			if($_GET["core_page_types"]=="core_pages")
	 			{
	 				$cp_sql  = " AND {$wpdb->posts}.id IN (select page_id from ".MM_TABLE_CORE_PAGES." where page_id IS NOT NULL ) ";	
	 			}
	 			else if($_GET["core_page_types"]=="wp_pages")
	 			{
	 				$cp_sql  = " AND {$wpdb->posts}.id NOT IN (select page_id from ".MM_TABLE_CORE_PAGES." where page_id IS NOT NULL ) ";	
	 			}
	 		}
	 		
	 		$where .= $mt_sql." ".$at_sql." ". $cp_sql;	
	 		//echo $where;
 		}
 		return $where;
 	}
 	
 	public function editPostsFilter()
 	{
 		global $post;
 		
 		$atSelected = (isset($_GET["access_tags"]))?$_GET["access_tags"]:"";
 		$mtSelected = (isset($_GET["member_types"]))?$_GET["member_types"]:"";
 		$select = "<select name='member_types'>
 					<option value=''>Show all Member Types</a>";
 		$select.=MM_HtmlUtils::generateSelectionsList(MM_MemberType::getMemberTypesList(true),$mtSelected);
 		$select.="</select>";
 		$select .= "<select name='access_tags'>
 					<option value=''>Show all Access Tags</a>";
 		$select.=MM_HtmlUtils::generateSelectionsList(MM_AccessTag::getAccessTagsList(true),$atSelected);
 		$select.="</select>";
 		
 		if((isset($post->post_type) && $post->post_type=='page') || (isset($_GET["post_type"]) && $_GET["post_type"]=='page') )
 		{
 			$cpt = (isset($_GET["core_page_types"]))?$_GET["core_page_types"]:"";
 			$select .= "<select name='core_page_types'>
 					<option value=''>Show all Pages</a>";
 			$select .= "<option value='core_pages' ".(($cpt=="core_pages")?"selected":"").">Show only MM Core Pages</a>";
 			$select .= "<option value='wp_pages' ".(($cpt=="wp_pages")?"selected":"").">Show only Standard Pages</a>";
 			$select.="</select>";
 		}
 		echo $select;
 	}
 	
 	public function postCustomColumns($column_name, $postId)
 	{
		if($column_name === 'core_page_type'){
			$data= "";
 			if(MM_CorePage::isDefaultCorePage($postId))
 			{
 				$data = "<img src='".MM_Utils::getImageUrl('default_flag')."' style='vertical-align: middle;'/> ";
 			}
 			
 			$cp = MM_CorePage::getCorePageInfo($postId);
 			if(isset($cp->core_page_type_name))
 			{
 				switch($cp->core_page_type_id)
 				{
 					case MM_CorePageType::$FREE_CONFIRMATION:
 						$data .= "Confirmation (Free)";
 					break;
 					case MM_CorePageType::$ERROR:
 						$refArr = explode(",", $cp->refs);
 						$ext = "";
 						if(is_array($refArr))
 						{
 							$ext = "";
 							foreach($refArr as $ref_id)
 							{
		 						$errorType = new MM_ErrorType($ref_id);
		 						$name = $errorType->getName();
		 						
		 						$ext .= (!empty($name))?$name.", ":"";
 							}
 							$ext =preg_replace("/[\s\,]+$/","", $ext);
 						}
 						if(!empty($ext))
 							$ext = "(".$ext.")";
 							
 						$data .= $cp->core_page_type_name ." ".$ext;
 					break;
 					default:
 						$data .= $cp->core_page_type_name;
 					break;
 				}
 			}
 			
 			if(empty($data)){
 				echo MM_NO_DATA;
 			}
 			else
 			{
 				echo $data;
 			}
		}
		else if($column_name === 'access_rights'){
			/// display access rights for post/page
			$associations = MM_ProtectedContentEngine::getAccessRights($postId);
			
			if(count($associations)<=0)
			{
				$memberTypesStr = "";
				$accessTagStr = "";
				$pages = MM_CorePage::getCorePagesByPageID($postId);
				if(is_array($pages))
				{
		 			foreach($pages as $page)
		 			{
		 				switch($page->ref_type)
		 				{
		 					case "product":
		 						$product = new MM_Product($page->ref_id);
		 						$memberType = $product->getAssociatedMemberType();
		 						if(isset($memberType->name) && !empty($memberType->name))
			 					{
			 						if(empty($memberTypesStr))
			 						{
			 							$memberTypesStr = "<img src='".MM_Utils::getImageUrl('user')."' style='vertical-align: middle;'/> ";
			 						}
			 						$memberTypesStr.= $memberType->name.", ";
		 						}
		 						
		 						$accessTag = $product->getAssociatedAccessTag();
		 						
		 						if($accessTag) 
		 						{
			 						if(empty($accessTagStr))
			 						{
			 							$accessTagStr = "<img src='".MM_Utils::getImageUrl('tag')."' style='vertical-align: middle;'/> ";
			 						}
			 						
			 						$accessTagStr.= $accessTag->name.", ";
		 						}
		 					break;
		 					case "member_type":
		 						if(empty($memberTypesStr))
		 						{
		 							$memberTypesStr = "<img src='".MM_Utils::getImageUrl('user')."' style='vertical-align: middle;'/> ";
		 						}
		 						$memberTypesStr.= $page->mt_name.", ";
		 					break;
		 					case "access_tag":
		 						if(empty($accessTagStr))
		 						{
		 							$accessTagStr = "<img src='".MM_Utils::getImageUrl('tag')."' style='vertical-align: middle;'/> ";
		 						}
		 						$accessTagStr.= $page->at_name.", ";
		 						
		 					break;
		 				}
		 			}
				}
	 			if(empty($memberTypesStr) && empty($accessTagStr))
	 			{
	 				echo MM_NO_DATA;	
	 			}
	 			else
	 			{
	 				if(strlen($memberTypesStr)>0)
	 					$memberTypesStr= substr($memberTypesStr, 0, strlen($memberTypesStr)-2);
	 				
	 				if(strlen($accessTagStr)>0)
	 					$accessTagStr= substr($accessTagStr, 0, strlen($accessTagStr)-2);
	 					
	 				echo $memberTypesStr." ".$accessTagStr;
	 			}	
			}
			else
			{
	        	$memberTypesStr = "";
	        	$accessTagStr = "";
	 			foreach($associations as $rights)
	 			{
	 				switch($rights->access_type)
	 				{
	 					case "member_type":
	 						if(empty($memberTypesStr))
	 						{
	 							$memberTypesStr = "<img src='".MM_Utils::getImageUrl('user')."' style='vertical-align: middle;'/> ";
	 						}
	 						$memberTypesStr.= $rights->mt_name.", ";
	 					break;
	 					case "access_tag":
	 						if(empty($accessTagStr))
	 						{
	 							$accessTagStr = "<img src='".MM_Utils::getImageUrl('tag')."' style='vertical-align: middle;'/> ";
	 						}
	 						$accessTagStr.= $rights->at_name.", ";
	 						
	 					break;
	 				}
	 			}
	 			
	 			if(empty($memberTypesStr) && empty($accessTagStr))
	 			{
	 				echo MM_NO_DATA;	
	 			}
	 			else
	 			{
	 				if(strlen($memberTypesStr)>0)
	 					$memberTypesStr= substr($memberTypesStr, 0, strlen($memberTypesStr)-2);
	 				
	 				if(strlen($accessTagStr)>0)
	 					$accessTagStr= substr($accessTagStr, 0, strlen($accessTagStr)-2);
	 					
	 				echo $memberTypesStr." ".$accessTagStr;
	 			}
			}
		}
 	}
 	
 	/*** Post hooks for maintenance ***/
 	public function trashPost($post_id)
 	{
 		global $wpdb;
 		
 		if(MM_CorePage::isDefaultCorePage($post_id))
 		{
 			MM_Messages::addError("You cannot delete a default core page.");
 			wp_redirect("edit.php?post_type=page");
 			exit;
 		}
 		else
 		{
	 		///remove access rights, if any
			$protected_content = new MM_ProtectedContentEngine();
			$protected_content->removeAllRights($post_id);
			
			///remove core page associations? or throw error.
			$corepage = new MM_CorePageEngine();
			$corepage->removeCorePageById($post_id);
 		}
 	}
 	
 	
 }
?>