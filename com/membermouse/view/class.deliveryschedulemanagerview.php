<?php
class MM_DeliveryScheduleManagerView extends MM_View{
	
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_ACCESS_RIGHTS_ADD:
					return $this->saveAccessRights($post);
					
				case self::$MM_JSACTION_ACCESS_RIGHTS_DIALOG:
					return $this->accessRightsDialog($post);
					
				case self::$MM_JSACTION_ACCESS_RIGHTS_UPDATE_DIALOG:
					return $this->updateAccessRightsDialog($post);
					
				case self::$MM_JSACTION_ACCESS_RIGHTS_UPDATE:
					return $this->updateAccessRights($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function updateAccessRights($post){
		$post["edit_id"]=1;
		
		if($post["should_remove"]=="1"){
			/*
			 * 'access_id','post_ID', 'access_type'
			 */
			$data = array(
				'access_id'=>$post["access_id"],
				'access_type'=>$post["access_type"],
				'day'=>$post["day"],
				'post_ID'=>$post["post_id"],
			);
			$accessRights = new MM_AccessRightsView();
			$response = $accessRights->removeAccessRights($data);
			
			if($response->type == MM_Response::$SUCCESS){
				return new MM_Response("Successfully removed access rights.");
			}
			return $response;
		}
		return $this->saveAccessRights($post);
	}
	
	private function saveAccessRights($post){
		$postElem = "mm_member_types_opt";
		if($post["access_type"] != "member_type")
			$postElem = "mm_access_tags_opt";

		$data = array(
			'post_ID'=>$post["post_id"],
			'type'=>$post["access_type"],
			'edit_id'=>((isset($post["edit_id"]))?$post["edit_id"]:0),
			'day'=>$post["day"],
			$postElem=>$post["access_id"],
		);
		
		$accessRights = new MM_AccessRightsView();
		$response = $accessRights->saveAccessRights($data);
		
		if($response->type == MM_Response::$SUCCESS){
			if(isset($post["cell_id"])){
				$responseArr = array(
						'cell_id'=>$post["cell_id"],
						'post_id'=>$post["post_id"],
						'access_type'=>$post["access_type"],
						'access_id'=>$post["access_id"],
						'image'=>MM_Utils::getImageUrl("accept")
				);
				return new MM_Response($responseArr);
			}
			else{
				return new MM_Response("Access rights saved");
			}
		}
		return $response;
	}
	
	private function updateAccessRightsDialog($post){
		$info = new stdClass();
		
		$req = array("access_type", "access_id", "post_id","day");
		
		foreach($req as $key){
			if(!isset($post[$key])){
				return new MM_Response("Could not find {$key}.", MM_Response::$ERROR);
			}
			$info->$key = $post[$key];
		}
		
		$obj = null;
		if($post["access_type"]=="member_type"){
			$obj= new MM_MemberType($post["access_id"]);
		}
		else{
			$obj= new MM_AccessTag($post["access_id"]);
		}
		
		$thisPost = get_post($post["post_id"]);
		
		$info->page_name = $thisPost->post_title;
		$info->type_name = $obj->getName();
		$info->day = $post["day"];
		$info->dialog = "updateAccessRights";
		$accessRightsForm = MM_TEMPLATE::generate(MM_MODULES."/delivery_schedule_manager_dialogs.php", $info);
		return new MM_Response($accessRightsForm);
		
	}
	
	private function accessRightsDialog($post){
		$info = new stdClass();
		
		if(!isset($post["type"])){
			return new MM_Response("Could not find access type.", MM_Response::$ERROR);
		}
		
		$id = preg_replace("/[^0-9]+/", "", $post["type"]);
		if(preg_match("/(mt_)/", $post["type"])){
			$memberType = new MM_MemberType($id);
			if(!$memberType->isValid()){
				return new MM_Response("Invalid member type.", MM_Response::$ERROR);
			}
			$info->id = $id;
			$info->type= "member_type";
		}
		else if(preg_match("/(at_)/", $post["type"])){
			$accessTag = new MM_AccessTag($id);
			if(!$accessTag->isValid()){
				return new MM_Response("Invalid access tag.", MM_Response::$ERROR);
			}
			$info->id = $id;
			$info->type= "access_tag";
		}
		else{
			return new MM_Response("Invalid access type.", MM_Response::$ERROR);
		}
		
		$posts = get_posts(array("post_type"=>"post"));
		$pages = get_posts(array("post_type"=>"page"));
		
		$postSelect = MM_Utils::createOptionsArray($posts, "ID", "post_title");
		$pageArr = MM_Utils::createOptionsArray($pages, "ID", "post_title");
		$pageSelect=  array();
		foreach($pageArr as $k=>$v){
			if(!MM_CorePageEngine::isCorePage($k)){
				$pageSelect[$k] = $v;
			}	
		}
		
		$info->posts_select = MM_HtmlUtils::generateSelectionsList($postSelect);
		$info->pages_select = MM_HtmlUtils::generateSelectionsList($pageSelect);
		$info->type_name = $post["type_name"];
		$info->dialog = "showAddAccessRigths";
		$accessRightsForm = MM_TEMPLATE::generate(MM_MODULES."/delivery_schedule_manager_dialogs.php", $info);
		return new MM_Response($accessRightsForm);

	}
}