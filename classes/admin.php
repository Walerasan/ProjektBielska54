<?php
if(!class_exists('admin'))
{
	class admin
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
		public function get_content($page_obj)
		{
		    //this can use only admin template
		    $page_obj->template="admin";
		    //--------------------
		    $rettext="";
		    $template_class_name=$page_obj->template."_template";
		    //--------------------
		    if($page_obj->template=="admin")
		    {
		        switch($page_obj->target)
		        {
		            case "test01":
		                $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->test01_target($page_obj));
		                break;
		            default:
		                $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->default_target());
		                break;
		        }		        
		    }
		    //--------------------
		    return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function default_target()
		{
		    return "Tutaj przypiść stronę główna - plik admin - linia kodu 36";
		}
		//----------------------------------------------------------------------------------------------------
		private function test01_target($page_obj)
		{
		    
		    $rettext="To jest sprawdzian klasy generatorów <br />";		    		    
		    return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>