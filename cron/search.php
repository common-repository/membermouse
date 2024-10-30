<?php
require_once("lib/class.mysql.php");
if(!isset($argv[1]))
{
	echo "?";
	exit;	
}

$keyword = preg_replace("/(\+)/", " ", $argv[1]);

$db = new MYSQL('localhost', 'root', 'root', 'membermouse_new');
$query ="show tables"; 		
$res = $db->query($query);
if(!$res)
{
	////Error? Let someone know
	$this->error(mysql_error().": ".__LINE__);
	exit;
}
if(mysql_num_rows($res)<=0)
{
	///nothing to process
	$this->error(mysql_error().": ".__LINE__);
	exit;
}
while($row = mysql_fetch_assoc($res))
{
	$table = array_shift($row);
	
	$sql = "describe {$table}";
	$res2= $db->query($sql);
	if(mysql_num_rows($res2)<=0)
		continue;
		
	$where ="";
	while($columns = mysql_fetch_assoc($res2))
	{
		if(!preg_match("/(varchar|text)/", strtolower($columns["Type"])))
		{
			continue;
		}
		$column = $columns["Field"];
		$where .= "{$column}='".mysql_escape_string($keyword)."' OR ";
	}
	$where = substr($where, 0, strlen($where)-3);
	
	$sql = "select * from {$table} where {$where}";
//	echo $sql."\n";
//	sleep(3);
	$res3 = $db->query($sql);
	if(mysql_num_rows($res3)>0)
	{
		echo "Results for keyword {$keyword} found in {$table}:\n\n{$sql}\n\n";
		while($result = mysql_fetch_assoc($res3))
		{
			var_dump($result);
		} 	
		echo "********** End Results ************\n\n";
		sleep(3);
	}
	else
		echo "Could not find {$keyword} in {$table}\n";
	
}
?>