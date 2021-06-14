<?php
if(!class_exists('uczniowie'))
{
	class uczniowie
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
			$content_text="<p class='title'>UCZNIOWIE</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					default:
						$content_text.=$this->uczniowie_info();
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region uczniowie_info
		private function uczniowie_info()
		{
			$rettext = "";
			//--------------------
			$idu_array = $this->page_obj->uczniowie_opiekunowie->get_idu_list($this->page_obj->opiekunowie->get_login_ido());
			if(sizeof($idu_array) > 0)
			{
				foreach($idu_array as $idu)
				{
					$rettext .= "<div style='padding-bottom:20px;'>";
					$rettext .= $this->get_imie_uczniowie_nazwisko_uczniowie($idu)."<br />";
					$rettext .= $this->szczegoly_dla_ucznia($idu);
					$rettext .= "</div>";
				}
			}
			else
			{
				$rettext .= "Brak przypisanych uczniów.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		private function szczegoly_dla_ucznia($idu)
		{
			$rettext = "";
			//--------------------
			//$rettext .= "<div style='text-indent: 20px;'>Zrobić szczegóły</div>";
			
			$wynik=$this->page_obj->database_obj->get_data("select idop,nazwa,kwota from oplaty where usuniety='nie';");
			if($wynik)
			{
				$rettext.="<table style='width:100%;font-size:16px;' cellspacing='0'>";
				$rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>nazwa</td>
						<td>kwota</td>
						<td style='width:18px;'>Opłaty</td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=1;
				while(list($idop,$nazwa,$kwota)=$wynik->fetch_row())
				{
					$rettext.="
						<tr>
							<td style='text-align:right;padding-right:10px;color:#555555;'>$lp.</td>
							<td>$nazwa</td>
							<td>$kwota</td>
							<td>opłać</td>
							<td></td>
						</tr>";
					$lp++;
				}
				$rettext.="</table>";
			}
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		#region get_list_for_klasa
		public function get_list_for_klasa($idkl)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idu,imie_uczniowie,nazwisko_uczniowie from ".get_class($this)." where usuniety='nie' and idkl=$idkl;");
			if($wynik)
			{
				while(list($idu,$imie_uczniowie,$nazwisko_uczniowie)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idu, "$imie_uczniowie $nazwisko_uczniowie");
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_imie_uczniowie_nazwisko_uczniowie
		public function get_imie_uczniowie_nazwisko_uczniowie($idu)
		{
			$imie_uczniowie_nazwisko_uczniowie='';
			if($idu!="" && is_numeric($idu) && $idu>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select CONCAT(imie_uczniowie,' ',nazwisko_uczniowie) from ".get_class($this)." where usuniety='nie' and idu=$idu");
				if($wynik)
				{
					list($imie_uczniowie_nazwisko_uczniowie)=$wynik->fetch_row();
				}
			}
			return $imie_uczniowie_nazwisko_uczniowie;
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