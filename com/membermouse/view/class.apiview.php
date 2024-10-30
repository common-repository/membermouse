<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_ApiView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveApi($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeApiSet($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	public function getData(MM_DataGrid $dg)
	{
		global $wpdb;
		
		$rows = parent::getData(MM_TABLE_API_KEYS, null, $dg);
		
		return $rows;
	}
	
	public function removeApiSet($post){
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$apiSet = new MM_Api($post["id"], false);
			$result = $apiSet->delete();
			
			if($result) {
				return new MM_Response();
			} 
		}
		
		return new MM_Response("Unable to delete api set. No id specified.", MM_Response::$ERROR);
	}
	
	
	private function saveApi($post)
	{
		$req = array('api_key','api_secret', 'status', 'name');
		foreach($req as $key){
			if(!isset($post["mm_".$key])){
				return new MM_Response("Could not find mm_{$key} field which is required.".json_encode($post), MM_Response::$ERROR);
			}
		}
		
		$api = new MM_Api();
		
		if(isset($post["mm_id"]) && intval($post["mm_id"])>0) {
			$api->setId($post["mm_id"]);
		}
		
		$api->setApiKey($post["mm_api_key"]);
		$api->setApiSecret($post["mm_api_secret"]);
		$api->setName($post["mm_name"]);
		$api->setStatus($post["mm_status"]);
		return $api->commitData();
	}
}
?>
