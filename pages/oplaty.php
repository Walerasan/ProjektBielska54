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
			// dla danego opiekuna
			$rettext = "";
			$wynik = $this->page_obj->database_obj->get_data("select idu from uczniowie_opiekunowie where ido = $ido and usuniety = 'nie';");
			if($wynik){
				

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
						<th>Imie i nazwisko</th>
						<th>Nazwa</th>
						<th>Kwota zł</th>
						<th>Rabat nazwa</th>
						<th>Rabat</th>
						<th>Status</th>
					</tr>";
				$lp=1;



				while(list($idu)=$wynik->fetch_row())
				{
					$wynik_imie_nazwisko = $this->page_obj->database_obj->get_data("select imie_uczniowie, nazwisko_uczniowie from uczniowie where idu = $idu and usuniety = 'nie';");
					list($imie,$nazwisko)=$wynik_imie_nazwisko->fetch_row();
					
					$wynik_uczniowie_oplaty = $this->page_obj->database_obj->get_data("select idop,rabat_kwota,rabat_nazwa,status from uczniowie_oplaty where idu = $idu and usuniety = 'nie';");
					if($wynik_uczniowie_oplaty){
						
						while(list($idop,$rabat_kwota,$rabat_nazwa,$status)=$wynik_uczniowie_oplaty->fetch_row()){
						
							$wynik_oplaty = $this->page_obj->database_obj->get_data("select idto,nazwa,kwota from oplaty where idop = $idop and usuniety = 'nie';");
							if($wynik_oplaty){
								list($idto,$nazwa,$kwota)=$wynik_oplaty->fetch_row();
								$rettext.="
								<tr>
									<td>$lp.</td>
									<td>$imie $nazwisko</td>
									<td>$nazwa</td>
									<td>$kwota zł</td>
									<td>$rabat_nazwa</td>
									<td>$rabat_kwota</td>
									<td>$status</td>
								</tr>";
								$lp++;
							}
							
						}

					}

				}
				$rettext.="</table>";
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