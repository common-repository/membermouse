<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_OrderHistory extends MM_Entity
{
	private $orderDate = "";
	private $productId = "";
	private $userId = "";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_ORDER_HISTORY." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_LogApi(): error retrieving data  with id of {$this->id}. Query run is ".$sql);
		}
	}

	public function setData($data)
	{
		try 
		{
			$this->orderDate = $data->order_date;
			$this->productId = $data->product_id;
			$this->userId = $data->user_id;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function commitData()
	{	
		global $wpdb;
		
		$doUpdate = false;
		$sql = "select count(*) as total from ".MM_TABLE_ORDER_HISTORY." where id='{$this->id}' ";
		$row = $wpdb->get_row($sql);
		if(intval($row->total)>0){
			$doUpdate = true;
		}
		
		if(intval($this->id)>0 && $doUpdate){
			$sql = "update ".MM_TABLE_ORDER_HISTORY." set 
						order_date='%s', 
						product_id='%s', 
						user_id='%s'
					where 
						id='{$this->id}'
			";
			$wpdb->query($wpdb->prepare($sql, $this->orderDate, $this->productId, $this->userId));
		}
		else{
			
			$sql = "insert into ".MM_TABLE_ORDER_HISTORY." set 
					id='%s',
					order_date='%s', 
					product_id='%s', 
					user_id='%s'
			";
			$wpdb->query($wpdb->prepare($sql, $this->id, $this->orderDate, $this->productId, $this->userId));
			$this->id = $wpdb->insert_id;
		}
	}
	
	public static function getHistory($userId, $limit=5, $where=""){
		global $wpdb;
		
		$limitSql = ($limit>0)?"limit {$limit}":"";
		if(!empty($where)){
			$where = " AND ".$where;
		}
		$sql = "select * from ".MM_TABLE_ORDER_HISTORY." where user_id='{$userId}' {$where} order by order_date desc {$limitSql}";
		
		$rows = $wpdb->get_results($sql);
		
		if(is_array($rows)){
			$results = array();
			foreach($rows as $row){
				$product = new MM_Product($row->product_id);
				$llProduct = MM_LimeLightService::getProduct($product->getProductId());

				if(!($llProduct instanceof MM_Response) && isset($llProduct->product_name)){
					$productName = preg_replace("/[\,]+$/", "", urldecode($llProduct->product_name));
					$productPrice =  number_format(preg_replace("/[\,]+$/", "", urldecode($llProduct->product_price)),2);
					$results[$row->id] = array(
						'time_stamp'=>$row->order_date,
						'product_name'=>urldecode($productName),
						'product_price'=>"\$".$productPrice,
					);
				}
			}
			return $results;
		}
		return array();
	}
	
	public function setOrderDate($msg){
		$this->orderDate = $msg;
	}
	
	public function getOrderDate(){
		return $this->orderDate;
	}
	
	public function setProductId($req){
		$this->productId = $req;
	}
	
	public function getProductId(){
		return $this->productId;
	}
	
	public function setUserId($req){
		$this->userId = $req;
	}
	
	public function getUserId(){
		return $this->userId;
	}
}