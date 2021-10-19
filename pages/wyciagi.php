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
			// wyciagi: select * from uczniowie_opiekunowie uo, uczniowie u, wyciagi_uczniowie wu, wyciagi w where uo.idu = u.idu and wu.idu = u.idu and wu.idw = w.idw and uo.ido = 5 and uo.usuniety = 'nie' and u.usuniety = 'nie' and wu.usuniety = 'nie' and w.usuniety = 'nie';
			// gotówka: select * from uczniowie_opiekunowie uo, uczniowie u, iden_wyciagu iw, wyciagi w where uo.idu = u.idu and iw.idu = u.idu and w.idw = iw.idw and uo.ido = 5 and uo.usuniety = 'nie' and u.usuniety = 'nie' and iw.usuniety = 'nie' and w.usuniety = 'nie';
			// wyciagi: select imie_uczniowie, nazwisko_uczniowie, tytul, kwota from uczniowie_opiekunowie uo, uczniowie u, wyciagi_uczniowie wu, wyciagi w where uo.idu = u.idu and wu.idu = u.idu and wu.idw = w.idw and uo.ido = 5 and uo.usuniety = 'nie' and u.usuniety = 'nie' and wu.usuniety = 'nie' and w.usuniety = 'nie';
			// gotówka: select imie_uczniowie, nazwisko_uczniowie, tytul, kwota from uczniowie_opiekunowie uo, uczniowie u, iden_wyciagu iw, wyciagi w where uo.idu = u.idu and iw.idu = u.idu and w.idw = iw.idw and uo.ido = 5 and uo.usuniety = 'nie' and u.usuniety = 'nie' and iw.usuniety = 'nie' and w.usuniety = 'nie';

			$rettext .= "
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
						<th>Tytuł</th>
						<th>Kwota zł</th>
					</tr>";
				$lp=1;

			//wyciagi
			$wynik = $this->page_obj->database_obj->get_data("select imie_uczniowie, nazwisko_uczniowie, tytul, kwota from uczniowie_opiekunowie uo, uczniowie u, wyciagi_uczniowie wu, wyciagi w where uo.idu = u.idu and wu.idu = u.idu and wu.idw = w.idw and uo.ido = $ido and uo.usuniety = 'nie' and u.usuniety = 'nie' and wu.usuniety = 'nie' and w.usuniety = 'nie';");
			if($wynik)
			{
				while( list($imie_uczniowie, $nazwisko_uczniowie, $tytul, $kwota) = $wynik->fetch_row() )
				{
					$rettext.="
					<tr>
						<td>$lp.</td>
						<td>$imie_uczniowie $nazwisko_uczniowie</td>
						<td>$tytul</td>
						<td>$kwota zł</td>
					</tr>";
					$lp++;
				}
			}

			//gotówka
			$wynik = $this->page_obj->database_obj->get_data("select imie_uczniowie, nazwisko_uczniowie, tytul, kwota from uczniowie_opiekunowie uo, uczniowie u, iden_wyciagu iw, wyciagi w where uo.idu = u.idu and iw.idu = u.idu and w.idw = iw.idw and uo.ido = $ido and uo.usuniety = 'nie' and u.usuniety = 'nie' and iw.usuniety = 'nie' and w.usuniety = 'nie';");
			if($wynik)
			{
				while( list($imie_uczniowie, $nazwisko_uczniowie, $tytul, $kwota) = $wynik->fetch_row() )
				{
					$rettext.="
					<tr>
						<td>$lp.</td>
						<td>$imie_uczniowie $nazwisko_uczniowie</td>
						<td>$tytul</td>
						<td>$kwota zł</td>
					</tr>";
					$lp++;
				}
			}

			$rettext.="</table>";
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