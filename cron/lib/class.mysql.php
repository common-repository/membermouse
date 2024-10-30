<?php
class MYSQL
{
	private $host = "";
	private $database = "";
	private $username = "";
	private $password = "";

	public function __construct($host="", $username="", $password="", $database="")
	{
		$this->host = ($host!="")?$host:$this->host;
		$this->database = ($database!="")?$database:$this->database;
		$this->username = ($username!="")?$username:$this->username;
		$this->password = ($password!="")?$password:$this->password;
	}
	public function Check()
	{
		return $this->connect();
	}
	public function query($sql)
	{
		if($conn = $this->connect())
		{
			$res = mysql_query($sql);
			if(!$res)
			{
				return false;
			}
			return $res;
		}
	}

	private function connect()
	{
		if ( $conn = @mysql_connect ($this->host, $this->username, $this->password) )
		{
			if ( @mysql_select_db ($this->database, $conn) )
			{
				return $conn;
			}
		}
		return false;
	}

	public function disconnect()
	{
		@mysql_close();
	}

	public function backup($table_arr,$dir="/tmp")
	{
		foreach($table_arr as $table)
		{
			$path = $dir."/{$table}_".Date("Y_m_d_h_i").".sql";
			$cmd = "mysqldump -acf -u ".DB_USERNAME." --password=".DB_PASSWORD."  ".DB_DATABASE." {$table}> {$path}";
			shell_exec($cmd);

			echo $cmd;

			if(!file_exists($path))
			{
				return $path ." could not be backed up. ";
			}
			else if(filesize($path)<=0)
			{
				return $path ." could not be backed up. ";
			}
		}
		return "SUCCESS";
	}

}

?>