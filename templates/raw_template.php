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
			$this->page_obj=$page_obj;
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_content($trescstrony)
		{			
		    return $trescstrony;
		}
		//----------------------------------------------------------------------------------------------------		
	}
}
?>