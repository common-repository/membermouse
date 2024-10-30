<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_CustomFieldView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveCustomField($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeCustomField($post);
					
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
		
		$rows = parent::getData(MM_TABLE_CUSTOM_FIELDS, null, $dg);
		
		return $rows;
	}
	
	public function removeCustomField($post){
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$customField = new MM_CustomField($post["id"], false);
			$result = $customField->delete();
			
			if($result) {
				return new MM_Response();
			} 
		}
		
		return new MM_Response("Unable to delete custom field. No id specified.", MM_Response::$ERROR);
	}
	
	
	private function saveCustomField($post)
	{
		$req = array('field_name','field_label', 'is_required', 'show_on_reg','show_on_myaccount');
		foreach($req as $key){
			if(!isset($post["mm_".$key])){
				return new MM_Response("Could not find mm_{$key} field which is required.".json_encode($post), MM_Response::$ERROR);
			}
		}
		
		$customField = new MM_CustomField();
		
		if(isset($post["mm_id"]) && intval($post["mm_id"])>0) {
			$customField->setId($post["mm_id"]);
		}
		
		$customField->setFieldLabel($post["mm_field_label"]);
		$customField->setFieldName($post["mm_field_name"]);
		$customField->setRequired($post["mm_is_required"]);
		$customField->setShowOnReg($post["mm_show_on_reg"]);
		$customField->setShowOnMyAccount($post["mm_show_on_myaccount"]);
		return $customField->commitData();
	}
}
?>
