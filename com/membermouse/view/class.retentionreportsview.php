<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_RetentionReportsView extends MM_View
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
		
		if(isset($_POST["mm_from_date"])){
			if(empty($_POST["mm_from_date"])){
				MM_Session::value("mm_retentionreport_from_date","");
			}
			else{
				MM_Session::value("mm_retentionreport_from_date",Date("Y-m-d",strtotime($_POST["mm_from_date"])));
			}
		}
		if(isset($_POST["mm_to_date"])){
			if(empty($_POST["mm_to_date"])){
				MM_Session::value("mm_retentionreport_to_date","");
			}
			else{
				MM_Session::value("mm_retentionreport_to_date",Date("Y-m-d",strtotime($_POST["mm_to_date"])));
			}
		}
		
		$from = MM_Session::value("mm_retentionreport_from_date");
		$to = MM_Session::value("mm_retentionreport_to_date");
		
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
		$rows = parent::getData(MM_TABLE_RETENTION_REPORTS, null, $dg, $where);
		
//		global $wpdb;
//		$sql = "select rr.affiliate_id, rr.sub_affiliate_id, period_diff(date_format(rr.last_rebill_date, '%Y%m'), date_format(rr.date_added, '%Y%m')) as months ";
//		
//		$csv = "";
//		if(is_array($totalRows) && count($totalRows)>0){
//			$hasHeader = false;
//			foreach($totalRows as $row){
//				if(!$hasHeader){
//					$header = "";
//					foreach($row as $column=>$value){
//						$header.= "\"".$column."\",";
//					}
//					$csv.=preg_replace("/(\,)$/", "", $header);
//				}
//				$csvRow = "";
//				foreach($row as $column=>$value){
//					$csvRow.= "\"".$value."\",";
//				}
//				$csv.=preg_replace("/(\,)$/", "", $csvRow);
//			}
//		}
		return $rows;
	}
}
?>
