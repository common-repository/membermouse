<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Transaction
{
	public static function begin()
	{
		global $wpdb;
		@mysql_query("BEGIN", $wpdb->dbh);
	}
	
	public static function rollback()
	{
		global $wpdb;
		@mysql_query("ROLLBACK", $wpdb->dbh);
	}
	
	public static function commit()
	{
		global $wpdb;
		@mysql_query("COMMIT", $wpdb->dbh);
	}
}
?>
