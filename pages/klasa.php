<?php
if(!class_exists('klasa'))
{
	class klasa
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
			$content_text="<p class='title'>KLASY</p>";
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
		#region get_list
		public function get_list()
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idkl,idod,nazwa from ".get_class($this)." where usuniety='nie' order by idod;");
			if($wynik)
			{
				while(list($idkl,$idod,$nazwa)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idkl, (int)$idod, $nazwa);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list_for_idod
		public function get_list_for_idod($idod)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idkl,nazwa from ".get_class($this)." where usuniety='nie' and idod=$idod;");
			if($wynik)
			{
				while(list($idkl,$nazwa)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idkl, (int)$idod, $nazwa);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_name
		public function get_name($idkl)
		{
			$nazwa='';
			if($idkl!="" && is_numeric($idkl) && $idkl>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select nazwa from ".get_class($this)." where usuniety='nie' and idkl=$idkl");
				if($wynik)
				{
					list($nazwa)=$wynik->fetch_row();
				}
			}
			return $nazwa;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_oddzial
		public function get_oddzial($idkl)
		{
			$idod=0;
			if($idkl!="" && is_numeric($idkl) && $idkl>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idod from ".get_class($this)." where usuniety='nie' and idkl=$idkl");
				if($wynik)
				{
					list($idod)=$wynik->fetch_row();
				}
			}
			return $idod;
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