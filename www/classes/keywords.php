<?php
if(!class_exists('keywords'))
{
	class keywords
	{
		//----------------------------------------------------------------------------------------------------
		public function __construct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_keywords($zmk,$language_obj) 
		{
			switch($zmk)
			{
				default:
				    $rettext=$language_obj->pobierz("keywords");
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function get_rating($zmk,$language_obj)
		{
			switch($zmk)
			{
				default:
					$rettext=$language_obj->pobierz("rating");
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>