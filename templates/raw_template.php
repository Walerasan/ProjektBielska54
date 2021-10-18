<?php
//--------------------
if(!class_exists('raw_template'))
{
    class raw_template
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
	    public function __construct($page_obj)
		{
			$this->page_obj = $page_obj;
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_content($trescstrony)
		{
			echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
					<html>
					<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
					</head>
					<body>";
			echo $trescstrony;
			echo "</body>
					</html>";
			exit;
		}
		//----------------------------------------------------------------------------------------------------		
	}
}
?>