<?php

/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.

Generated class
 */

class MM_AppliedAccessTag extends MM_Entity{
		private $accessType = "";
		private $accessTagId = "";
		private $refId = "";
		private $orderId = "";
		private $status = "";
		private $productId = "";
		private $isRefunded = "";
		private $applyDate = "";

		public function getData(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." WHERE id='".$this->id."'";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}
		
		public function getDataByTagAndUser(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." WHERE access_tag_id='".$this->accessTagId."' and access_type='user' and ref_id='".$this->refId."' limit 1";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}
		
		public function getDataByOrderAndUser(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." WHERE order_id='".$this->orderId."' and access_type='user' and ref_id='".$this->refId."' limit 1";
			$result = $wpdb->get_row($sql);

			if(intval($this->orderId)>0 && $result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}
		
		public function getDataByProductAndUser(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." WHERE product_id='".$this->productId."' and access_type='user' and ref_id='".$this->refId."' limit 1";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}

		public function getFormFields(){
			$info = new stdClass();
			$info->access_type = $this->accessType;
			$info->access_tag_id = $this->accessTagId;
			$info->ref_id = $this->refId;
			$info->order_id = $this->orderId;
			$info->status = $this->status;
			$info->product_id = $this->productId;
			$info->is_refunded = $this->isRefunded;
			$info->apply_date = $this->applyDate;

			return $info;
		}

		public function setData($data){
			try 
			{
				$this->accessType = $data->access_type;
				$this->accessTagId = $data->access_tag_id;
				$this->refId = $data->ref_id;
				$this->orderId = $data->order_id;
				$this->status = $data->status;
				$this->productId = $data->product_id;
				$this->isRefunded = $data->is_refunded;
				$this->applyDate = $data->apply_date;

				parent::validate();
			}
			catch (Exception $ex) {
				parent::invalidate();
			}
		}

		public function commitData(){

			global $wpdb;
			
			if($this->accessTagId>0){

				$sql = "update ".MM_TABLE_APPLIED_ACCESS_TAGS." set 
					order_id = '".mysql_escape_string($this->orderId)."',
					status = '".mysql_escape_string($this->status)."',
					is_refunded = '".mysql_escape_string($this->isRefunded)."',
					apply_date = '".mysql_escape_string($this->applyDate)."'
				where
					access_tag_id = '".mysql_escape_string($this->accessTagId)."' AND 
					access_type = '".mysql_escape_string($this->accessType)."' AND 
					ref_id = '".mysql_escape_string($this->refId)."' 
				";

				$wpdb->query($sql);
				return $this->id;
			}
			else{

				$sql = "insert into ".MM_TABLE_APPLIED_ACCESS_TAGS." set 
					access_type = '".mysql_escape_string($this->accessType)."',
					access_tag_id = '".mysql_escape_string($this->accessTagId)."',
					ref_id = '".mysql_escape_string($this->refId)."',
					order_id = '".mysql_escape_string($this->orderId)."',
					status = '".mysql_escape_string($this->status)."',
					product_id = '".mysql_escape_string($this->productId)."',
					is_refunded = '".mysql_escape_string($this->isRefunded)."',
					apply_date = '".mysql_escape_string($this->applyDate)."'";

				$wpdb->query($sql);
				return mysql_insert_id();

			}

			return false;

		}

		public function getAccessType(){
			return $this->accessType;
		}

		public function setAccessType($val){
			$this->accessType = $val;
		}

		public function getAccessTagId(){
			return $this->accessTagId;
		}

		public function setAccessTagId($val){
			$this->accessTagId = $val;
		}

		public function getRefId(){
			return $this->refId;
		}

		public function setRefId($val){
			$this->refId = $val;
		}

		public function getOrderId(){
			return $this->orderId;
		}

		public function setOrderId($val){
			$this->orderId = $val;
		}

		public function getStatus(){
			return $this->status;
		}

		public function setStatus($val){
			$this->status = $val;
		}

		public function getProductId(){
			return $this->productId;
		}

		public function setProductId($val){
			$this->productId = $val;
		}

		public function getIsRefunded(){
			return $this->isRefunded;
		}

		public function setIsRefunded($val){
			$this->isRefunded = $val;
		}

		public function getApplyDate(){
			return $this->applyDate;
		}

		public function setApplyDate($val){
			$this->applyDate = $val;
		}

}?>