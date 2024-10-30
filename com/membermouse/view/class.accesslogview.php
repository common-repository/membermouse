<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_AccessLogView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
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
		
		$where = "";
		
		if(isset($_POST["mm_event_types"])){
			MM_Session::value("mm_accesslog_event_types",$_POST["mm_event_types"]);
		}
		if(isset($_POST["mm_from_date"])){
			if(empty($_POST["mm_from_date"])){
				MM_Session::value("mm_accesslog_from_date","");
			}
			else{
				MM_Session::value("mm_accesslog_from_date",Date("Y-m-d",strtotime($_POST["mm_from_date"])));
			}
		}
		if(isset($_POST["mm_to_date"])){
			if(empty($_POST["mm_to_date"])){
				MM_Session::value("mm_accesslog_to_date","");
			}
			else{
				MM_Session::value("mm_accesslog_to_date",Date("Y-m-d",strtotime($_POST["mm_to_date"])));
			}
		}
		
		$eventType = MM_Session::value("mm_accesslog_event_types");
		$from = MM_Session::value("mm_accesslog_from_date");
		$to = MM_Session::value("mm_accesslog_to_date");
		
		if(!empty($eventType)){
			$where .= " (event_type='{$eventType}' ) ";
		}
		
		if(!empty($from)){
			if(!empty($where)){
				$where.=" AND ";
			}
			$where .= " (DATE(date_added)>=Date('{$from}') ) ";
		}
		
		if(!empty($to)){
			if(!empty($where)){
				$where.=" AND ";
			}
			$where .= " (DATE(date_added)<=Date('{$to}') ) ";
		}
//		echo $where;
		$rows = parent::getData(MM_TABLE_ACCESS_LOGS, null, $dg, $where);
		
		return $rows;
	}
}
?>
