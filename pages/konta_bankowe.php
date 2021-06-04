<?php
if(!class_exists('konta_bankowe'))
{
	class konta_bankowe
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
			$content_text="<p class='title'>KONTA BANKOWE</p>";
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
			$wynik=$this->page_obj->database_obj->get_data("select idk,numer_konta from ".get_class($this)." where usuniety='nie' order by idod;");
			if($wynik)
			{
				while(list($idk,$numer_konta)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idk, $numer_konta);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_name
		public function get_numer_konta($idk)
		{
			$numer_konta='';
			if($idk!="" && is_numeric($idk) && $idk>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select numer_konta from ".get_class($this)." where usuniety='nie' and idk=$idk");
				if($wynik)
				{
					list($numer_konta)=$wynik->fetch_row();
				}
			}
			return $numer_konta;
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