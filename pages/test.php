<?php
if(!class_exists('test'))
{
	class test
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
			$rettext="";
			$template_class_name=$page_obj->template."_template";
			//--------------------
			if($page_obj->template=="admin")
			{	
			    switch($page_obj->target)
			    {
			        case "test01":
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->test01_target(),"menu");
			            break;
			        default:
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->default_target(),"menu");
			            break;
			    }
			    /*if($page_obj->users->is_login())
				{
				    switch($page_obj->target)
					{
						
					}
				}
				else
					$rettext="Proszę się zalogować";*/
			}
			else if($page_obj->template=="raw")
			{
			    switch($page_obj->target)
			    {
			        case "test01":
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->test01_target(),"menu");
			            break;
			        default:
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->default_target(),"menu");
			            break;
			    }
			}
			else if($page_obj->template=="index")			
			{			    
			    switch($page_obj->target)
			    {
			        case "test01":
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->test01_target(),"menu");
			            break;
			        default:
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->default_target(),"menu");
			            break;
			    }
			    
			    
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function default_target()
		{
		    return "Tutaj przypiść stronę główna - plik test - linia kodu 55";
		}
		//----------------------------------------------------------------------------------------------------
		private function test01_target()
		{
		    return "test";
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
    die("Class exists: ".__FILE__);
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
?>