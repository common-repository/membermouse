<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 class MM_AccessRightsView extends MM_View
 {
 	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveAccessRights($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
 	
 	public function editAccessRight($post)
 	{
		$pc = new MM_ProtectedContentEngine();
		$info=new stdClass();	
		$info->access_type ="";
		$info->access_id ="";
		$info->day = "0";
		$info->options = "";
		$access_arr = array('mt'=>'Member Type','at'=>'Access Tag');
		$info->access_rights_choice = "";
		
		$info->access_rights_mt_style = "display:none;";
		$info->access_rights_at_style = "display:none;";
		$info->edit = "0";
		if(isset($post["access_type"]) && isset($post["access_id"]))
		{
			$info->edit = "1";
			$info->access_type = $post["access_type"];
			$info->access_id = $post["access_id"];
			$info->day = $pc->getDays($post["access_id"], $post["access_type"], $post["post_ID"]);
			switch($info->access_type)
			{
				case "member_type":
					$obj  =new MM_MemberType($info->access_id);
					$info->options = "<option value='".$obj->getId()."'>".$obj->getName()."</option>";
					$info->access_rights_mt_style = "";
					unset($access_arr["at"]);
				break;
				
				case "access_tag":
					$obj  =new MM_AccessTag($info->access_id);
					$info->options = "<option value='".$obj->getId()."'>".$obj->getName()."</option>";
					$info->access_rights_at_style = "";
					unset($access_arr["mt"]);
				break;
			}
		}
		foreach($access_arr as $value=>$txt)
			$info->access_rights_choice.= "<option value='{$value}'>{$txt}</option>";
		
		$data= MM_TEMPLATE::generate(MM_MODULES."/page_meta.dialog.php", $info);
		
		return new MM_Response($data);
 	}
 	
 	public function refreshMetaBox($post)
	{
		$obj  =new stdClass();
		$obj->ID = $post["post_ID"];
		$obj->page_type = (isset($post["post_type"]))?$post["post_type"]:null;
		$ph = new MM_ProtectedContentView();
		ob_start();
		$ph->postPublishingBox($obj, true);
		$contents = ob_get_contents();
		ob_end_clean();
		return new MM_Response($contents);
	}
 	
 	public function getAccessRightsOptions($post)
 	{
 		global $wpdb;

		$sel_id = "";
		if(isset($post["id"]) && intval($post["id"])>0)
			$sel_id =$post["id"];
		
		$table = MM_TABLE_ACCESS_TAGS;
		if(!isset($post["type"]) || (isset($post["type"]) && $post["type"]=="mt"))
			$table = MM_TABLE_MEMBER_TYPES;
			
		$qualifier = ($table==MM_TABLE_MEMBER_TYPES)?'member_type':'access_tag';
			
		//get existing access rights
		$pc = new MM_ProtectedContentEngine();
		$existing_rights = $pc->getPostAccessRights($post["post_ID"]);
		$rights = array();
		
		if(isset($post["id"]) && intval($post["id"])>0)
		{
			$sql = "select id, name from {$table} where id='{$post["id"]}' limit 1";
			
			$row = $wpdb->get_row($sql);
			$conv = array();
			if(is_object($row))
			{
				$conv = array($row->id=>$row->name);
			}
			$html  = MM_HtmlUtils::generateSelectionsList($conv);
			return new MM_Response($html);
		}
		
		foreach($existing_rights as $row)
		{
			if(isset($sel_id) && intval($sel_id)>0)
			{ 
				if($sel_id==$row->access_id && $row->access_type==$qualifier)
					$rights[$row->access_type][] = $row->access_id;
			}
			else
				$rights[$row->access_type][] = $row->access_id;	
		}
		$rows = parent::getData($table);
		$conv_rows = array();
		if(!$rows)
			return new MM_Response('');
	
		foreach($rows as $row)
		{
			if(isset($rights[$qualifier]) && is_array($rights[$qualifier]))
			{
				if(in_array($row->id, $rights[$qualifier]))
					continue;
			}
				
			$conv_rows[$row->id] = $row->name;
		}
		$html  = MM_HtmlUtils::generateSelectionsList($conv_rows, $sel_id);
		return new MM_Response($html);
 	}
 	
 	public function getData($sortBy=null, $sortDir=null)
	{
		$pc = new MM_ProtectedContentEngine();
		$info=new stdClass();	
		$info->access_type ="";
		$info->access_id ="";
		$info->day = "0";
		$access_arr = array('mt'=>'Member Type','at'=>'Access Tag');
		$info->access_rights_choice = "";
		
		$info->access_rights_mt_style = "display:none;";
		$info->access_rights_at_style = "display:none;";
		$info->edit = "0";
		if(isset($post["access_type"]) && isset($post["access_id"]))
		{
			$info->edit = "1";
			$info->access_type = $post["access_type"];
			$info->access_id = $post["access_id"];
			$info->day = $pc->getDays($post["access_id"], $post["access_type"], $post["post_ID"]);
			//$info->day = 
			switch($info->access_type)
			{
				case "member_type":
					unset($access_arr["at"]);
				break;
				
				case "access_tag":
					unset($access_arr["mt"]);
				break;
				
			}
		}
		
		foreach($access_arr as $value=>$txt)
			$info->access_rights_choice.= "<option value='{$value}'>{$txt}</option>";
		
		return $info;
	}
	
	public function saveAccessRights($post)
	{
		$ret = false;
		$pc = new MM_ProtectedContentEngine();
		
		if(!isset($post["post_ID"]) || (isset($post["post_ID"]) && intval($post["post_ID"])<=0))
			return new MM_Response("Could not find the corresponding post.", MM_Response::$ERROR);	
		
		$post_elem = "mm_member_types_opt";
		if($post["type"] != "member_type")
			$post_elem = "mm_access_tags_opt";
		
		if(intval($post["edit_id"])>0)
			$ret = $pc->updatePostAccessRights($post["post_ID"], $post["type"], $post[$post_elem], $post["day"]);
		else
			$ret = $pc->setPostAccessRights($post["post_ID"], $post["type"], $post[$post_elem], $post["day"]);
			
		if($ret===false)
			return new MM_Response("Unable to save protected access", MM_Response::$ERROR);	
		
		return new MM_Response();
	}
	
	public function removeAccessRights($post)
	{	
		$req = array('access_id','post_ID', 'access_type');
		$errors = "";
		$result = 0;
		foreach($req as $key)
		{
			if(!isset($post[$key]))
				$errors.= $key." is not valid.<br />";
			else
				$$key =$post[$key];
		}
		if(empty($errors))
		{
			$pc = new MM_ProtectedContentEngine();
			if($pc->removeAccessRights($post_ID, $access_id, $access_type))
			{
				$result=1;
			}
			return new MM_Response();
		}
		return new MM_Response($errors, MM_Response::$ERROR);
	}
	
	public static function isPage($post_type=null)
	{
		global $post;
		if(!is_null($post_type))
		{
			if($post_type=="page")
				return true;
		}
		else
		{
			if(isset($post->post_type))
			{
				if($post->post_type == "page")
					return true;
			}	
		}
		
		return false;
	}
 }
?>