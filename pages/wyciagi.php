<?php
if(!class_exists('wyciagi'))
{
	class wyciagi
	{
		var $page_obj;
		var $katalog;//katalog do uploud dokumentów do przetwarzania
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj=$page_obj;
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
			$content_text="<p class='title'>WYCIAGI BANKOWE</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					default:
						$content_text.=$this->get_statements_list($this->page_obj->opiekunowie->get_login_ido());
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_statements_list
		private function get_statements_list($ido)
		{
			$rettext = "";
			//--------------------
			$rettext = "ido = $ido";
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