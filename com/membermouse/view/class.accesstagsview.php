<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_AccessTagsView extends MM_View
{	
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveAccessTag($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeAccessTag($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	public function getData($post)
	{
		global $wpdb;
		
		$rows = parent::getData(MM_TABLE_ACCESS_TAGS, null, $post);
		
		foreach($rows as $row)
		{ 
			$sql = "select count(*) as total from ".MM_TABLE_APPLIED_ACCESS_TAGS." where access_type='user' and access_tag_id='{$row->id}' and is_refunded='0' and status='1' ";
			$obj =$wpdb->get_row($sql);
			$row->member_count = $obj->total;
			
			$sql = "select p.* from ".MM_TABLE_ACCESS_TAG_PRODUCTS." atp, ".MM_TABLE_PRODUCTS." p where atp.access_tag_id='{$row->id}' and atp.product_id=p.id";
			$row->products = $wpdb->get_results($sql);
		}
		
		return $rows;
	}
	
	private function saveAccessTag($post)
	{
		$tag = new MM_AccessTag();
		
		if(isset($post["id"]) && intval($post["id"])>0) {
			$tag->setId($post["id"]);
		}
		
		if(isset($post["mm_subscription_type"]) && $post["mm_subscription_type"]=="free") { 
	 		$post["mm_subscription_type"] = '1';
	 	} else { 
	 		$post["mm_subscription_type"]=  '0';
	 	}
	 	
	 	if($post["mm_status"]=="active") {
	 		$post["mm_status"] = "1";
	 	} else {
	 		$post["mm_status"] = "0";
	 	}
	 	
	 	$tag->setIsFree($post["mm_subscription_type"]);
	 	$tag->setStatus($post["mm_status"]);
		$tag->setName($post["mm_display_name"]);
	 	$tag->setDescription($post["mm_description"]);	
	 	$tag->setBadgeUrl($post["badge_url"]);
	 	
		// update product relationships
	 	if(isset($post["mm_products"]) && is_array($post["mm_products"]) && count($post["mm_products"]) > 0)
	 	{
		 	$tag->setProducts($post["mm_products"]);
		}
		
		return $tag->commitData();
	}
	
	private function removeAccessTag($post)
	{
		global $wpdb;
		
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$tag = new MM_AccessTag($post["id"], false);
			$result = $tag->delete();
			
			if($result) {
				return new MM_Response();
			} 
			else {
				return new MM_Response("This access tag has existing associations and can't be removed.", MM_Response::$ERROR);
			}
		}
		
		return new MM_Response("Unable to delete access tag. No id specified.", MM_Response::$ERROR);
	}
}
?>
