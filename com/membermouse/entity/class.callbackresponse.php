<?php

/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.

Generated class
 */

class MM_CallbackResponse extends MM_Entity{
		private $paymentMethod = "";
		private $response = "";
		private $userId = 0;
		private $paymentId =0;
		private $productId =0;
		private $paymentStatus = "";

		public function getData(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_CALLBACK_RESPONSES." WHERE id='".$this->id."'";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}
		
		public function getDataByUserAndProduct(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_CALLBACK_RESPONSES." WHERE user_id='".$this->userId."' and product_id='".$this->productId."'";
			LogMe::write("MM_CallbackResponse::getDataByUserAndProduct() : ".$sql);
			$result = $wpdb->get_row($sql);

			if($result){
				$this->id = $result->id;
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}
		
		public static function getDataByUser($userId){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_CALLBACK_RESPONSES." WHERE user_id='".$userId."' order by id desc";
			LogMe::write("MM_CallbackResponse::$getDataByUser ".$sql);
			$result = $wpdb->get_results($sql);
			
			$responses = array();
			if(is_array($result)){
				foreach($result as $row){
					$obj = new MM_CallbackResponse();
					$obj->setData($row);
					$responses[] = $obj;
				}
			}
			return $responses;
		}

		public function getFormFields(){
			$info = new stdClass();
			$info->payment_method = $this->paymentMethod;
			$info->response = $this->response;
			$info->payment_status = $this->paymentStatus;
			$info->payment_id = $this->paymentId;
			$info->product_id = $this->productId;
			$info->user_id = $this->userId;

			return $info;
		}

		public function setData($data){
			try 
			{
				$this->paymentMethod = $data->payment_method;
				$this->response = $data->response;
				$this->paymentStatus = $data->payment_status;
				$this->paymentId = $data->payment_id;
				$this->userId = $data->user_id;
				$this->productId = $data->product_id;

				parent::validate();
			}
			catch (Exception $ex) {
				parent::invalidate();
			}
		}

		public function commitData(){

			global $wpdb;

			if(intval($this->id)>0){

				$sql = "update ".MM_TABLE_CALLBACK_RESPONSES." set 
					payment_method = '".mysql_escape_string($this->paymentMethod)."',
					response = '".mysql_escape_string($this->response)."',
					payment_status = '".mysql_escape_string($this->paymentStatus)."',
					payment_id = '".mysql_escape_string($this->paymentId)."',
					user_id = '".mysql_escape_string($this->userId)."',
					product_id = '".mysql_escape_string($this->productId)."',
					date_modified=NOW() 
				where
					id='".$this->id."'";

				$wpdb->query($sql);
				return $this->id;
			}
			else{

				$sql = "insert into ".MM_TABLE_CALLBACK_RESPONSES." set 
					payment_method = '".mysql_escape_string($this->paymentMethod)."',
					response = '".mysql_escape_string($this->response)."',
					payment_status = '".mysql_escape_string($this->paymentStatus)."',
					payment_id = '".mysql_escape_string($this->paymentId)."',
					user_id = '".mysql_escape_string($this->userId)."',
					product_id = '".mysql_escape_string($this->productId)."',
					date_modified=NOW()";

				$wpdb->query($sql);
				return mysql_insert_id();

			}

			return false;

		}

		public function getPaymentMethod(){
			return $this->paymentMethod;
		}

		public function setPaymentMethod($val){
			$this->paymentMethod = $val;
		}

		public function getResponse(){
			return json_decode($this->response);
		}

		public function setResponse($val){
			$this->response = json_encode($val);
		}

		public function getPaymentStatus(){
			return $this->paymentStatus;
		}

		public function setPaymentStatus($val){
			$this->paymentStatus = $val;
		}

		public function getUserId(){
			return $this->userId;
		}

		public function setUserId($val){
			$this->userId = $val;
		}

		public function getPaymentId(){
			return $this->paymentId;
		}

		public function setPaymentId($val){
			$this->paymentId = $val;
		}

		public function getProductId(){
			return $this->productId;
		}

		public function setProductId($val){
			$this->productId = $val;
		}

}?>