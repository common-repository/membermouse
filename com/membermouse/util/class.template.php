 <?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_TEMPLATE
{
	private $info;
	
	public function __construct ()
	{
		$this->info = new stdClass();
	}
	
	public function render_with($template)
	{
		$p = $this->info;
		if(file_exists($template))
		{
			return include($template);
		}
		else
		{
			echo "file does not exist {$template}";
		}
	}
	
	public static function generate($filename, $info="")
	{
			$p = $info;
			
			if (is_file($filename)) {
				ob_start();
				include $filename;
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}
			
			return false;
	}
	
	public function add_var($name, $val)
	{
		$this->info->$name = $val;
	}

}
 
?>
