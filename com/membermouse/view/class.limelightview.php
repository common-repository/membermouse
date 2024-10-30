<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_LimeLightView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SYNC:
					return MM_LimeLightService::sync();
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
 	public function getData($dataGrid, $campaignId=0)
	{
		$where = "";
		if($campaignId>0){
			$where = " campaign_id='".addslashes($campaignId)."' ";
		}
		return parent::getData(MM_TABLE_PRODUCTS, null, $dataGrid, $where);
	}
}
?>
