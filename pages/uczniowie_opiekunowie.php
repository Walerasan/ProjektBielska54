<?php
if(!class_exists('uczniowie_opiekunowie'))
{
	class uczniowie_opiekunowie
	{
		var $page_obj;
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
			$content_text="<p class='title'>UCZNIOWIE - OKIEKUNOWIE</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					default:
						$content_text.="No access is available";
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_ido
		public function get_ido($idu)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select ido from ".get_class($this)." where idu=$idu and usuniety='nie';");
			if($wynik)
			{
				while(list($ido)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$ido);
				}
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_idu
		public function get_idu_list($ido)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select uo.idu from ".get_class($this)." uo, uczniowie u where uo.idu = u.idu and uo.ido = $ido and uo.usuniety = 'nie' and u.usuniety = 'nie';");
			if($wynik)
			{
				while(list($idu)=$wynik->fetch_row())
				{
					$rettext[] = (int)$idu;
				}
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_idu
		public function get_idu($ido)
		{
			$rettext=-1;
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idu from ".get_class($this)." where ido=$ido and usuniety='nie';");
			if($wynik)
			{
				list($rettext)=$wynik->fetch_row();
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_iduo
		public function get_iduo($idu,$ido)
		{
			$rettext=-1;
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select iduo from ".get_class($this)." where idu=$idu and ido=$ido;");
			if($wynik)
			{
				list($rettext)=$wynik->fetch_row();
			}
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