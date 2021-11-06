<?php
if(!class_exists('uczniowie_oplaty'))
{
	class uczniowie_oplaty
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
		#region get_cntent
		public function get_content()
		{
			$content_text="<p class='title'>UCZNIOWIE - OPŁATY</p>";
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
		public function get_ido_list($idu)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select ido from ".get_class($this)." where idu=$idu and usuniety='nie';");
			if($wynik)
			{
				while(list($ido)=$wynik->fetch_row())
				{
					$rettext[] =(int)$ido;
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		public function get_kwota_do_zaplaty($idop, $idu)
		{
			$kwota = 0;
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select uo.idop,nazwa,kwota,rabat_nazwa,rabat_kwota from uczniowie_oplaty uo, oplaty o where o.idop = uo.idop and o.usuniety = 'nie' and uo.usuniety = 'nie' and uo.idop = $idop and uo.idu = $idu;");
			if($wynik)
			{
				list($idop,$nazwa,$kwota_z_r,$rabat_nazwa,$rabat_kwota) = $wynik->fetch_row();
				$kwota = $kwota_z_r - $rabat_kwota;
			}
			//--------------------
			return $kwota;
		}
		//----------------------------------------------------------------------------------------------------
		public function get_iduop($idop, $idu)
		{
			$iduop = 0;
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select iduop from uczniowie_oplaty where usuniety = 'nie' and idop = $idop and idu = $idu;");
			if($wynik)
			{
				list( $iduop ) = $wynik->fetch_row();
			}
			//--------------------
			return $iduop;
		}
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