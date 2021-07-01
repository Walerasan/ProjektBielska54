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
					$rettext .= "<h3>".$this->get_imie_uczniowie_nazwisko_uczniowie($idu)."</h3><br />";
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
		#region szczegoly_dla_ucznia
		private function szczegoly_dla_ucznia($idu)
		{
			$rettext = "";
			//--------------------
			//$rettext .= "<div style='text-indent: 20px;'>Zrobić szczegóły</div>";
			
			//$wynik=$this->page_obj->database_obj->get_data("select idop,nazwa,kwota from oplaty where usuniety='nie';");			
			$wynik = $this->page_obj->database_obj->get_data("select uo.idop,nazwa,kwota,rabat_nazwa,rabat_kwota from uczniowie_oplaty uo, oplaty o where o.idop = uo.idop and o.usuniety = 'nie' and uo.usuniety = 'nie' and uo.idu = $idu;");
			if($wynik)
			{
				$rettext.="
				<style>
					#customers {
					font-family: Arial, Helvetica, sans-serif;
					border-collapse: collapse;
					width: 100%;
					}

					#customers td, #customers th {
					border: 1px solid gray;
					padding: 8px;
					}

					#customers tr:nth-child(even){background-color: #f2f2f2;}

					#customers tr:hover {background-color: #ddd;}

					#customers th {
					padding-top: 12px;
					padding-bottom: 12px;
					text-align: left;
					background-color: orange;
					color: white;
					}
					button.oplac{
						background-color: yellow;
						border:2px solid black;
						border-radius:10px;
						font-weight:bold;
						padding:5px;
					}
					button.oplac:hover{
						background-color: #2F4F4F;
						border:2px solid black;
						border-radius:10px;
						font-weight:bold;
						color:white;
						padding:5px;
					}
				</style>
				";
				$rettext.="<table id='customers'>";
				$rettext.="
					<tr>
						<th>Lp.</th>
						<th>nazwa</th>
						<th>kwota</th>
						<th>rabat</th>
						<th>kwota</th>
						<th>do zapłaty</th>
						<th>Opłaty</th>
					</tr>";
				$lp=1;
				while(list($idop,$nazwa,$kwota,$rabat_nazwa,$rabat_kwota)=$wynik->fetch_row())
				{
					$rettext.="
						<tr>
							<td>$lp.</td>
							<td>$nazwa</td>
							<td>$kwota zł</td>
							<td>$rabat_nazwa</td>
							<td>$rabat_kwota zł</td>
							<td>".($kwota - $rabat_kwota)."</td>
							<td><button class='oplac'>OPŁAĆ</button></td>
						</tr>";
					$lp++;
				}
				$rettext.="</table>";
			}
			//--------------------
			return $rettext;
		}
		#endregion
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