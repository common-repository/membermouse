<?php

abstract class Runner
{	
	protected function error($str)
	{
		echo $str."\n";
	}
	protected function status($str)
	{
		echo $str."\n";
	}
	public abstract function Process();  
	public abstract function getNextRunDate();   
	
}
?>