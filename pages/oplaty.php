<?php
if(!class_exists('oplaty'))
{
	class oplaty
	{
		var $page_obj;
		var $javascript_select_uczniowie;
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj=$page_obj;
			$this->javascript_select_uczniowie="";
			$this->definicjabazy();
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region destructor
		public function __destruct()
		{
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_content
		public function get_content()
		{
			$content_text="<p class='title'>OPŁATY</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					default:
						$content_text.=$this->get_payments_list($this->page_obj->opiekunowie->get_login_ido());
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_payments_list
		private function get_payments_list($ido)
		{
			// pobrać wszystkich uczniów i dla nich pobrać wszystkie opłaty
			
			$rettext = "";
			//--------------------
			$idu_array = $this->page_obj->uczniowie_opiekunowie->get_idu_list($this->page_obj->opiekunowie->get_login_ido());
			foreach($idu_array as $idu)
			{
				$ido_array = $this->page_obj->uczniowie_oplaty->get_ido_list($idu);
				foreach($ido_array as $ido)
				{
					$rettext .= $idu." ".$ido." <br />";
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region definicjabazy
		private function definicjabazy()
		{
			//definition is in ksiegowosc.nzpe.pl
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
	}
}//end if
else
	die("Class exists: ".__FILE__);
?>