<?php
if(!class_exists('admin'))
{
	class admin
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
		public function get_content()
		{
			//this can use only admin template
			$this->page_obj->template="admin";
			//--------------------
			$content_text="";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if($this->page_obj->template=="admin")
			{
				switch($this->page_obj->target)
				{
					case "test01":
						$content_text=$this->test01_target();
						break;
					default:
						$content_text=$this->default_target();
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		//----------------------------------------------------------------------------------------------------
		private function default_target()
		{
			return "";
		}
		//----------------------------------------------------------------------------------------------------
		private function test01_target()
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