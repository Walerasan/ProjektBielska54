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
						$content_text.=$this->get_payments_list2($this->page_obj->opiekunowie->get_login_ido());
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
			if($wynik)
			{
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
					
					$wynik_uczniowie_oplaty = $this->page_obj->database_obj->get_data("select idop,rabat_kwota,rabat_nazwa,status, comment from uczniowie_oplaty where idu = $idu and usuniety = 'nie';");
					if($wynik_uczniowie_oplaty){
						
						while(list($idop,$rabat_kwota,$rabat_nazwa,$status, $comment)=$wynik_uczniowie_oplaty->fetch_row()){
						
							$wynik_oplaty = $this->page_obj->database_obj->get_data("select idto,nazwa,kwota from oplaty where idop = $idop and usuniety = 'nie';");
							if($wynik_oplaty){
								list($idto,$nazwa,$kwota)=$wynik_oplaty->fetch_row();
								$rettext.="
								<tr>
									<td>$lp.</td>
									<td>$imie $nazwisko</td>
									<td>$nazwa ($comment)</td>
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
		#region get_payments_list2
		private function get_payments_list2($ido)
		{
			$rettext = "";
			//--------------------
			$idu_array = $this->page_obj->uczniowie_opiekunowie->get_idu_list($ido);
			if(sizeof($idu_array) > 0)
			{
				$rettext .= "<div style='padding-bottom:20px;'>";
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
						<th>saldo</th>
						<th>do zapłaty</th>
						<th>Opłaty</th>
					</tr>";
				$this->lp = 1;
				foreach($idu_array as $idu)
				{
					$rettext .= $this->szczegoly_dla_ucznia($idu, false);
				}
				$rettext.="</table>";
				$rettext .= "</div>";
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
		private function szczegoly_dla_ucznia($idu, $active_link)
		{
			$rettext = "";
			//--------------------
			// pobrac sume jaka ma uczen rozliczona
			$suma_rozliczona = 0;
			// [wyciagi]
			//select wu.idw,count(wu.idu) from wyciagi_uczniowie wu where wu.usuniety = 'nie' and wu.idw in (select idw from wyciagi_uczniowie where idu = 1 and usuniety = 'nie') group by wu.idw;
			//select wu.idw,count(wu.idu),w.kwota,(w.kwota/count(wu.idu)) from wyciagi_uczniowie wu, wyciagi w where wu.usuniety = 'nie' and w.usuniety = 'nie' and w.idw = wu.idw and wu.idw in (select idw from wyciagi_uczniowie where idu = 1 and usuniety = 'nie') group by wu.idw;
			//$rettext .= "select wu.idw,count(wu.idu),w.kwota,(w.kwota/count(wu.idu)) from wyciagi_uczniowie wu, wyciagi w where wu.usuniety = 'nie' and w.usuniety = 'nie' and w.idw = wu.idw and wu.idw in (select idw from wyciagi_uczniowie where idu = $idu and usuniety = 'nie') group by wu.idw; <br />";
			$wynik = $this->page_obj->database_obj->get_data("select wu.idw,count(wu.idu),w.kwota,(w.kwota/count(wu.idu)) from wyciagi_uczniowie wu, wyciagi w where wu.usuniety = 'nie' and w.usuniety = 'nie' and w.idw = wu.idw and wu.idw in (select idw from wyciagi_uczniowie where idu = $idu and usuniety = 'nie' and status = 'auto') group by wu.idw;");
			if($wynik)
			{
				while( list($idw_r,$idu_r,$kwota_r,$kwota_jednostowa_r) = $wynik->fetch_row() )
				{
					$suma_rozliczona += $kwota_jednostowa_r;
				}
			}
			// [gotowka]
			//select kwota from iden_wyciagu iw, wyciagi w where iw.idw = w.idw and iw.idu = 14 and iw.usuniety = 'nie';
			//select sum(kwota) from iden_wyciagu iw, wyciagi w where iw.idw = w.idw and iw.idu = 14 and iw.usuniety = 'nie';
			$wynik = $this->page_obj->database_obj->get_data("select sum(kwota) from iden_wyciagu iw, wyciagi w where iw.idw = w.idw and iw.idu = $idu and iw.usuniety = 'nie';");
			if($wynik)
			{
				while( list($kwota_jednostowa_r) = $wynik->fetch_row() )
				{
					$suma_rozliczona += $kwota_jednostowa_r;
				}
			}
			//--------------------

			//$rettext .= "<div style='text-indent: 20px;'>Zrobić szczegóły</div>";
			
			//$wynik=$this->page_obj->database_obj->get_data("select idop,nazwa,kwota from oplaty where usuniety='nie';");
			$wynik = $this->page_obj->database_obj->get_data("select uo.iduop, uo.idop,nazwa,kwota,rabat_nazwa,rabat_kwota, comment from uczniowie_oplaty uo, oplaty o where o.idop = uo.idop and o.usuniety = 'nie' and uo.usuniety = 'nie' and uo.idu = $idu;");
			if($wynik)
			{
				
				

				while(list($iduop, $idop, $nazwa, $kwota, $rabat_nazwa, $rabat_kwota, $comment) = $wynik->fetch_row())
				{
					//pobieram płatności online
					$oplata_rozliczona = $this->page_obj->blue_media->jest_oplata_rozliczona($iduop);
					//-----
					$kwota_m = $kwota;
					$kwota_z_r = $kwota - $rabat_kwota;
					$suma_rozliczona_b = $suma_rozliczona;
					if( $oplata_rozliczona )
					{
						$kwota = 0;
					}
					else
					{
						if( $suma_rozliczona > $kwota_z_r )
						{
							$kwota = 0;
						}
						else
						{
							$kwota = round( ($kwota_z_r - $suma_rozliczona), 2);
						}
					}
					if( ($suma_rozliczona - $kwota_z_r) < 0 )
					{
						$suma_rozliczona = 0;
					}
					else
					{
						$suma_rozliczona = $suma_rozliczona - $kwota_z_r;
					}

					if($active_link)
					{
						$link_action = "onclick='window.location.href=\"blue_media,index,get_link,$idop,$idu\"'";
					}
					else
					{
						$link_action = "";
					}
					$link_do_platnosci = "";
					if($oplata_rozliczona)
					{
						$link_do_platnosci = "Opłacone online";
					}
					else if ($kwota > 0)
					{
						$link_do_platnosci = "<button class='oplac' $link_action >OPŁAĆ</button>";
					}
					else
					{
						$link_do_platnosci = "Rozliczone z salda";
					}
					//--------------------
					$rettext.="
						<tr>
							<td>".$this->lp.".</td>
							<td>$nazwa ($comment)</td>
							<td>$kwota_m zł</td>
							<td>$rabat_nazwa</td>
							<td>$rabat_kwota zł</td>
							<td>$suma_rozliczona_b</td>
							<td>" . ($kwota) . "</td>
							<td>$link_do_platnosci</td>
						</tr>";
						$this->lp++;
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