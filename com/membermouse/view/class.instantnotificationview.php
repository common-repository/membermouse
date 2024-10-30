<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_InstantNotificationView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveNotification($post);
					
				case self::$MM_JSACTION_SEND_TEST_NOTIFY:
					return $this->sendTestNotification($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function sendTestNotification($post){
		if(!isset($post["id"])){
			return new MM_Response("Please pass an event type.", MM_Response::$ERROR);
		}
		
		$previewObj = MM_Preview::getData();
		$user = $previewObj->getUser();
		if(!($user instanceof MM_User)){
			return new MM_Response("Could not find user.", MM_Response::$ERROR);
		}
		
		$instantNotification = new MM_InstantNotificationEngine();
		$instantNotification->forceRemoteCall = true;
		return $instantNotification->sendNotification($post["id"],$user);
	} 
	
	public function getData(MM_DataGrid $dg)
	{
		global $wpdb;
		
		$rows = parent::getData(MM_TABLE_NOTIFICATION_EVENT_TYPES, null, $dg);
		
		return $rows;
	}
	
	
	private function saveNotification($post)
	{
		$req = array('status','script_url', 'id');
		foreach($req as $key){
			if(!isset($post["mm_".$key])){
				return new MM_Response("Could not find mm_{$key} field which is required.".json_encode($post), MM_Response::$ERROR);
			}
		}
		
		$ini = new MM_InstantNotification($post["mm_id"]);
		$ini->setScriptUrl($post["mm_script_url"]);
		$ini->setStatus($post["mm_status"]);
		return $ini->commitData();
	}
}
?>
