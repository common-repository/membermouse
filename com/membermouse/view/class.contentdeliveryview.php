<?php
class MM_ContentDeliveryView{
	public static $NONCE = 'mm-content-delivery';
	public static $FORM_FIELD_SEND = 'send';
	public static $FORM_FIELD_FROM = 'from';
	public static $FORM_FIELD_BODY = 'body';
	public static $FORM_FIELD_SUBJECT = 'subject';
	public static $SENT_QUEUE_AJAX = 'queue';
	public static $PREFIX = 'mm-content-delivery-';
	public function show(){
		global $post;
		$info = new stdClass();
		$info->post_id = (isset($post->ID))?$post->ID:0; 
		echo MM_TEMPLATE::generate(MM_MODULES."/content_delivery.php", $info);
	}
}