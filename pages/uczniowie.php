<?php
if(!class_exists('uczniowie'))
{
	class uczniowie
	{
		var $page_obj;
		var $update_select_field_from_oddzialy_js_script;
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
			if( ($this->page_obj->template == "admin") || ($this->page_obj->template == "index") )
			{
				switch($this->page_obj->target)
				{
					case "usun_konto_bankowe":
						$idukb = isset($_GET['par1']) ? $_GET['par1'] : 0;
						$confirm = isset($_GET['par2']) ? $_GET['par2'] : "";
						$idu = isset($_GET['par3']) ? $_GET['par3'] : 0;
						$content_text .= $this->usun_konto_bankowe($idukb, $confirm, $idu);
						break;
					case "zapisz_gotowka":
						$idu = isset($_POST['idu']) ? $_POST['idu'] : 0;
						$kwota = isset($_POST['kwota']) ? $_POST['kwota'] : 0;
						$tytul = isset($_POST['tytul']) ? $_POST['tytul'] : 0;
						$content_text .= $this->zapisz_gotowka($idu, $kwota, $tytul);
						break;
					case "form_gotowka":
						$idu = isset($_GET['par1']) ? $_GET['par1'] : 0;
						$content_text .= $this->form_gotowka($idu, 0, "");
						break;
					case "szczegoly":
						$idu = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$content_text .= $this->szczegoly($idu);
						break;
					case "przywroc":
						$idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->restore($idu,$confirm);
					break;
					case "usun":
						$idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->delete($idu,$confirm);
					break;
					case "zapisz":
						$idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$idkl=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idkl'])?$_POST['idkl']:0);
						$imie_uczniowie=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['imie_uczniowie'])?$_POST['imie_uczniowie']:"");
						$nazwisko_uczniowie=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['nazwisko_uczniowie'])?$_POST['nazwisko_uczniowie']:"");
						$numer_indeksu=isset($_GET['par5'])?$_GET['par5']:(isset($_POST['numer_indeksu'])?$_POST['numer_indeksu']:"");
						$ido=isset($_GET['par6'])?$_GET['par6']:(isset($_POST['ido'])?$_POST['ido']:"");
						$imie_opiekun=isset($_GET['par7'])?$_GET['par7']:(isset($_POST['imie_opiekun'])?$_POST['imie_opiekun']:"");
						$nazwisko_opiekun=isset($_GET['par8'])?$_GET['par8']:(isset($_POST['nazwisko_opiekun'])?$_POST['nazwisko_opiekun']:"");
						$telefon_opiekun=isset($_GET['par9'])?$_GET['par9']:(isset($_POST['telefon_opiekun'])?$_POST['telefon_opiekun']:"");
						$email_opiekun=isset($_GET['par10'])?$_GET['par10']:(isset($_POST['email_opiekun'])?$_POST['email_opiekun']:"");
						$haslo=isset($_GET['par10'])?$_GET['par10']:(isset($_POST['haslo'])?$_POST['haslo']:"");
						$haslo_confirm=isset($_GET['par10'])?$_GET['par10']:(isset($_POST['haslo_confirm'])?$_POST['haslo_confirm']:"");
						$content_text.=$this->add($idu,$idkl,$imie_uczniowie,$nazwisko_uczniowie,$numer_indeksu,$ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun,$haslo,$haslo_confirm);
					break;
					case "formularz":
						$idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$idkl=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idkl'])?$_POST['idkl']:0);
						$imie_uczniowie=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['imie_uczniowie'])?$_POST['imie_uczniowie']:"");
						$nazwisko_uczniowie=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['nazwisko_uczniowie'])?$_POST['nazwisko_uczniowie']:"");
						$numer_indeksu=isset($_GET['par5'])?$_GET['par5']:(isset($_POST['numer_indeksu'])?$_POST['numer_indeksu']:"");
						$ido=isset($_GET['par6'])?$_GET['par6']:(isset($_POST['ido'])?$_POST['ido']:"");
						$imie_opiekun=isset($_GET['par7'])?$_GET['par7']:(isset($_POST['imie_opiekun'])?$_POST['imie_opiekun']:"");
						$nazwisko_opiekun=isset($_GET['par8'])?$_GET['par8']:(isset($_POST['nazwisko_opiekun'])?$_POST['nazwisko_opiekun']:"");
						$telefon_opiekun=isset($_GET['par9'])?$_GET['par9']:(isset($_POST['telefon_opiekun'])?$_POST['telefon_opiekun']:"");
						$email_opiekun=isset($_GET['par10'])?$_GET['par10']:(isset($_POST['email_opiekun'])?$_POST['email_opiekun']:"");
						$content_text .= $this->form($idu,$idkl,$imie_uczniowie,$nazwisko_uczniowie,$numer_indeksu,$ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun);
					break;
					case "lista":
					default:
						$aktualnailosc = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['aktualnailosc'])?$_POST['aktualnailosc']:0);
						$content_text .= $this->lista($aktualnailosc);
					break;
				}
			}
			else if( ($this->page_obj->template == "raw") )
			{
				switch($this->page_obj->target)
				{
					case "szczegoly_drukuj":
						$idu = isset($_GET['par1']) ? $_GET['par1'] : (isset($_POST['idu']) ? $_POST['idu'] : 0);
						$content_text .= $this->szczegoly_drukuj($idu);
						break;
					default:
						$content_text .= "";
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region lista
		public function lista($aktualnailosc)
		{
			$rettext="";
			//--------------------
			$rettext .= "<button class='button_add' title='dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz\"'>Dodaj nowy</button><br />";
			//--------------------
			if($aktualnailosc == "") $aktualnailosc = 0;
			$this->page_obj->database_obj->get_data("select idu from ".get_class($this)." where usuniety='nie'");
			$iloscwszystkich=$this->page_obj->database_obj->result_count();
			$iloscnastronie = 15;
			//--------------------
			if(isset($_POST['submit_szukaj']))
			{
				if(isset($_POST['imie']) && !empty($_POST['imie']))
				{
					$szukajimie =$_POST['imie'];
					$wynik = $this->page_obj->database_obj->get_data("select idu,idkl,imie_uczniowie,nazwisko_uczniowie,numer_indeksu,usuniety from ".get_class($this)." WHERE imie_uczniowie like '%$szukajimie%' order by nazwisko_uczniowie ;");
				}
				else if(isset($_POST['nazwisko']) && !empty($_POST['nazwisko']))
				{
					$szukajnazwisko = $_POST['nazwisko'];
					$wynik = $this->page_obj->database_obj->get_data("select idu,idkl,imie_uczniowie,nazwisko_uczniowie,numer_indeksu,usuniety from ".get_class($this)." WHERE nazwisko_uczniowie like '%$szukajnazwisko%' order by nazwisko_uczniowie ;");
				}
				else
				{
					$wynik = $this->page_obj->database_obj->get_data("select idu,idkl,imie_uczniowie,nazwisko_uczniowie,numer_indeksu,usuniety from ".get_class($this)." order by nazwisko_uczniowie limit $aktualnailosc,$iloscnastronie;");
				}

				if($wynik)
				{
					$rettext .= "<script type='text/javascript' src='./js/opticaldiv.js'></script>";
					$rettext .= "<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
					$rettext .= "<fieldset style='border:1px solid black;width:500px;'>";
					$rettext .= "<legend>Szukaj ucznia:</legend>";
					$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},lista'>";
					$rettext .= "<input type='text' name='imie' placeholder='wg imienia'>&nbsp;&nbsp";
					$rettext .= "<input type='text' name='nazwisko' placeholder='wg nazwiska'>&nbsp;&nbsp";
					$rettext .= "<input type='submit' name='submit_szukaj' value='szukaj' style='background-color:#97b6c3;border:1px solid #3a7090;border-radius:5px;width:80px;height:25px;color:white;font-weight:bold;'>";
					$rettext .= "</form>";
					$rettext .= "</fieldset><br><br>";
					$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0' id='uczniowie_blocks'>";
					$rettext .= "
						<tr style='font-weight:bold;'>
							<td style='width:25px;'>Lp.</td>
							<td>Nazwisko, imie</td>
							<td>klasa</td>
							
							<td style='width:18px;'></td>
							<td style='width:18px;'></td>
							<td style='width:18px;'></td>
						</tr>";
					$lp=0;
					while(list($idu,$idkl,$imie_uczniowie,$nazwisko_uczniowie,$numer_indeksu,$usuniety)=$wynik->fetch_row())
					{
						$lp++;
						//--------------------
						if($usuniety=='nie')
						{
							$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idu,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
						}
						else
						{
							$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idu,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:30px;'/></a>";
						}
						//--------------------
						$rettext .= "
							<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idu' onmouseover=\"setopticalwhite50('wiersz$idu')\" onmouseout=\"setoptical0('wiersz$idu')\">
								<td style='text-align:right;padding-right:10px;color:#555555;' onclick=\"uczniowie.open($idu);\">$lp.</td>
								<td onclick=\"uczniowie.open($idu);\">$nazwisko_uczniowie $imie_uczniowie</td>
								<td onclick=\"uczniowie.open($idu);\">{$this->page_obj->klasa->get_name($idkl)} - {$this->page_obj->oddzialy->get_name($this->page_obj->klasa->get_oddzial($idkl))}</td>
								
								<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},szczegoly,$idu'><img src='./media/ikony/szczegoly.png' alt='' style='height:30px;'/></a></td>
								<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idu'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a></td>
								<td style='text-align:center;'>$operacja</td>
							</tr>";
						$oplaty_array = $this->get_kwota_do_zaplaty($idu);
						$rozliczenia = $this->get_kwota_rozliczona($idu);
						$pozostalo_do_zaplaty = $oplaty_array[2] - $rozliczenia;
						if($pozostalo_do_zaplaty < 0)
						{
							$pozostalo_do_zaplaty = "nadpłata: ".(-1 * $pozostalo_do_zaplaty);
						}
						else if($pozostalo_do_zaplaty > 0)
						{
							$pozostalo_do_zaplaty = "niedopłata: ".$pozostalo_do_zaplaty;
						}
						else
						{
							$pozostalo_do_zaplaty = "rozliczone";
						}
						$szczagoly = "Suma opłat: ".$oplaty_array[0].",&#160;&#160; suma rabatów: ".$oplaty_array[1].",&#160;&#160; do rozliczenia: ".$oplaty_array[2].",&#160;&#160; rozliczono: ".$rozliczenia.",&#160;&#160; $pozostalo_do_zaplaty";
						$rettext .= "<tr id='wiersz{$idu}_szczagoly' style='display:none;' onmouseover=\"setopticalwhite50('wiersz$idu')\" onmouseout=\"setoptical0('wiersz$idu')\"><td >&#160;</td><td colspan='1000' style='vertical-align:top;padding-bottom:20px;font-size:14px;'>$szczagoly</td></tr>";
					}
					$rettext .= "</table>";
					$rettext .= "<div style='text-align:center;clear:both;'>".$this->page_obj->subpages->create($iloscwszystkich,$iloscnastronie,$aktualnailosc,get_class($this).",".$this->page_obj->template.",lista")."</div>";
					
				}
				else
				{
					$rettext .= "<br />Brak wpisów<br />";
				}
			}
			else
			{
				$wynik = $this->page_obj->database_obj->get_data("select idu,idkl,imie_uczniowie,nazwisko_uczniowie,numer_indeksu,usuniety from ".get_class($this)." order by nazwisko_uczniowie;");
				$iloscwszystkich = $this->page_obj->database_obj->result_count();

				$wynik=$this->page_obj->database_obj->get_data("select idu,idkl,imie_uczniowie,nazwisko_uczniowie,numer_indeksu,usuniety from ".get_class($this)." order by nazwisko_uczniowie limit $aktualnailosc,$iloscnastronie;");
				if($wynik)
				{
					$rettext .= "<script type='text/javascript' src='./js/opticaldiv.js'></script>";
					$rettext .= "<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
					$rettext .= "<fieldset style='border:1px solid black;width:500px;'>";
					$rettext .= "<legend>Szukaj ucznia:</legend>";
					$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},lista'>";
					$rettext .= "<input type='text' name='imie' placeholder='wg imienia'>&nbsp;&nbsp";
					$rettext .= "<input type='text' name='nazwisko' placeholder='wg nazwiska'>&nbsp;&nbsp";
					$rettext .= "<input type='submit' name='submit_szukaj' value='szukaj' style='background-color:#97b6c3;border:1px solid #3a7090;border-radius:5px;width:80px;height:25px;color:white;font-weight:bold;'>";
					$rettext .= "</form>";
					$rettext .= "</fieldset><br><br>";
					$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0' id='uczniowie_blocks'>";
					$rettext .= "
						<tr style='font-weight:bold;'>
							<td style='width:25px;'>Lp.</td>
							<td>Nazwisko, imie</td>
							<td>klasa</td>
							
							<td style='width:18px;'></td>
							<td style='width:18px;'></td>
							<td style='width:18px;'></td>
						</tr>";
					$lp=0;
					while(list($idu,$idkl,$imie_uczniowie,$nazwisko_uczniowie,$numer_indeksu,$usuniety)=$wynik->fetch_row())
					{
						$lp++;
						//--------------------
						if($usuniety=='nie')
						{
							$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idu,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
						}
						else
						{
							$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idu,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:30px;'/></a>";
						}
						//--------------------
						$rettext .= "
							<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idu' onmouseover=\"setopticalwhite50('wiersz$idu')\" onmouseout=\"setoptical0('wiersz$idu')\">
								<td style='text-align:right;padding-right:10px;color:#555555;' onclick=\"uczniowie.open($idu);\">".($aktualnailosc + $lp).".</td>
								<td onclick=\"uczniowie.open($idu);\">$nazwisko_uczniowie $imie_uczniowie</td>
								<td onclick=\"uczniowie.open($idu);\">{$this->page_obj->klasa->get_name($idkl)} - {$this->page_obj->oddzialy->get_name($this->page_obj->klasa->get_oddzial($idkl))}</td>
								
								<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},szczegoly,$idu'><img src='./media/ikony/szczegoly.png' alt='' style='height:30px;'/></a></td>
								<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idu'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a></td>
								<td style='text-align:center;'>$operacja</td>
							</tr>";
						$oplaty_array = $this->get_kwota_do_zaplaty($idu);
						$rozliczenia = $this->get_kwota_rozliczona($idu);
						$pozostalo_do_zaplaty = $oplaty_array[2] - $rozliczenia;
						if($pozostalo_do_zaplaty < 0)
						{
							$pozostalo_do_zaplaty = "nadpłata: ".(-1 * $pozostalo_do_zaplaty);
						}
						else if($pozostalo_do_zaplaty > 0)
						{
							$pozostalo_do_zaplaty = "niedopłata: ".$pozostalo_do_zaplaty;
						}
						else
						{
							$pozostalo_do_zaplaty = "rozliczone";
						}
						$szczagoly = "Suma opłat: ".$oplaty_array[0].",&#160;&#160; suma rabatów: ".$oplaty_array[1].",&#160;&#160; do rozliczenia: ".$oplaty_array[2].",&#160;&#160; rozliczono: ".$rozliczenia.",&#160;&#160; $pozostalo_do_zaplaty";
						$rettext .= "<tr id='wiersz{$idu}_szczagoly' style='display:none;' onmouseover=\"setopticalwhite50('wiersz$idu')\" onmouseout=\"setoptical0('wiersz$idu')\"><td >&#160;</td><td colspan='1000' style='vertical-align:top;padding-bottom:20px;font-size:14px;'>$szczagoly</td></tr>";
					}
					$rettext .= "</table><br>";
					$rettext .= "<div style='text-align:center;clear:both;'>".$this->page_obj->subpages->create($iloscwszystkich,$iloscnastronie,$aktualnailosc,get_class($this).",".$this->page_obj->template.",lista")."</div>";
				}
				else
				{
					$rettext .= "<br />Brak wpisów<br />";
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region form
		public function form($idu,$idkl,$imie_uczniowie,$nazwisko_uczniowie,$numer_indeksu,$ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun)
		{
			$rettext="";
			$ido_array = array();
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($idu!="" && is_numeric($idu) && $idu>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idkl,imie_uczniowie,nazwisko_uczniowie,numer_indeksu from ".get_class($this)." where usuniety='nie' and idu=$idu");
				if($wynik)
				{
					list($idkl,$imie_uczniowie,$nazwisko_uczniowie,$numer_indeksu)=$wynik->fetch_row();
					$ido_array=$this->page_obj->uczniowie_opiekunowie->get_ido($idu);
					$idod=$this->page_obj->klasa->get_oddzial($idkl);
				}
			}
			else
			{
				$idod=1;
			}
			//--------------------
			$imie_uczniowie=$this->page_obj->text_obj->doedycji($imie_uczniowie);
			$nazwisko_uczniowie=$this->page_obj->text_obj->doedycji($nazwisko_uczniowie);
			$numer_indeksu=$this->page_obj->text_obj->doedycji($numer_indeksu);
			//--------------------
			$rettext .= "
				<style>
					div.wiersz{float:left;clear:left;}
					div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
					div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
				</style>";
			$rettext .= "
				<form method='post' action='".get_class($this).",{$this->page_obj->template},zapisz'>
					<div style='overflow:hidden;'>
						<div class='wiersz'><div class='formularzkom1'>Imię: </div><div class='formularzkom2'><input type='text' name='imie_uczniowie' value='$imie_uczniowie' style='width:450px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>nazwisko: </div><div class='formularzkom2'><input type='text' name='nazwisko_uczniowie' value='$nazwisko_uczniowie' style='width:450px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>oddział: </div><div class='formularzkom2'>{$this->create_select_field_from_oddzial($idod,'idkl',$idkl)}</div></div>
						<div class='wiersz'><div class='formularzkom1'>klasa: </div><div class='formularzkom2'>{$this->create_select_field_from_klasa($idkl)}</div></div>
						<div class='wiersz'><div class='formularzkom1'>numer indeksu: </div><div class='formularzkom2'><input type='text' name='numer_indeksu' value='$numer_indeksu' style='width:150px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>&#160;</div><div class='formularzkom2'>&#160;</div></div>
						
						<div class='wiersz' id='opiekunowie_blocks'><div class='formularzkom1'>Opiekun:</div><div class='formularzkom2'>{$this->create_block_opiekunowie($ido_array)}</div></div>
						<div class='wiersz'>
							<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
									<button title='Anuluj' style='font-size:20px;float:right;' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
					</div>
					<input type='hidden' name='idu' value='$idu' />
				</form>";
			$rettext .= $this->update_select_field_from_oddzialy_js_script;
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($idu,$idkl,$imie_uczniowie,$nazwisko_uczniowie,$numer_indeksu,$ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun,$haslo,$haslo_confirm)
		{
			$rettext = "";

			#region insert opiekun
			if(is_array($ido))
			{
				foreach($ido as $key => $val)
				{
					if($ido[$key] == -1)
					{
						$ido[$key] = $this->page_obj->opiekunowie->insert($imie_opiekun[$key],$nazwisko_opiekun[$key],$telefon_opiekun[$key],$email_opiekun[$key],$haslo[$key],$haslo_confirm[$key]);
						if($ido[$key] > 0)
						{
							$rettext .= "Dodano nowego opiekuna {$ido[$key]} {$imie_opiekun[$key]} {$nazwisko_opiekun[$key]},<br />";
						}
						else
						{
							$rettext .= "Pominięto opiekuna {$imie_opiekun[$key]} {$nazwisko_opiekun[$key]} - e-mail jest już używany,<br />";
						}
					}
				}
			}
			#endregion

			//--------------------
			// zabezpieczam dane
			//--------------------
			$imie_uczniowie = $this->page_obj->text_obj->domysql($imie_uczniowie);
			$nazwisko_uczniowie = $this->page_obj->text_obj->domysql($nazwisko_uczniowie);
			$numer_indeksu = $this->page_obj->text_obj->domysql($numer_indeksu);
			//--------------------
			if( ($idu != "") && is_numeric($idu) && ($idu > 0) )
			{
				$zapytanie="update ".get_class($this)." set imie_uczniowie='$imie_uczniowie',nazwisko_uczniowie='$nazwisko_uczniowie',numer_indeksu='$numer_indeksu',idkl=$idkl where idu=$idu;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(imie_uczniowie,nazwisko_uczniowie,numer_indeksu,idkl)values('$imie_uczniowie','$nazwisko_uczniowie','$numer_indeksu',$idkl)";//nowy wpis
			}
			$rettext .= "";
			//--------------------
			if(!$_SESSION['antyrefresh'])
			{
				if($this->page_obj->database_obj->execute_query($zapytanie))
				{
					$_SESSION['antyrefresh']=true;
					if( !( ($idu != "") && is_numeric($idu) && ($idu > 0) ) )
					{
						$idu = $this->page_obj->database_obj->last_id();
					}

					$this->page_obj->uczniowie_opiekunowie->mark_usuniety($idu,"yes");
					if( is_array($ido) )
					{
						foreach($ido as $key => $val)
						{
							if($val > 0)
							{
								$rettext .= "Zapisano opiekuna {$idu} , {$ido[$key]},<br />";
								$this->page_obj->uczniowie_opiekunowie->insert($idu,$ido[$key]);
							}
						}
					}
					$rettext .= "Zapisane $idu<br />";
					$rettext.=$this->lista(0);
				}
				else
				{
					$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext.=$this->form($idu,$idkl,$imie_uczniowie,$nazwisko_uczniowie,$numer_indeksu,$ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun);
				}
			}
			else
			{
				$rettext .= $this->lista(0);
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region delete
		public function delete($idu,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idu=$idu;"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista(0);
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
					$rettext.=$this->lista(0);
				}
			}
			else
			{
				$rettext .= "This operation need confirm.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region restore
		public function restore($idu,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idu=$idu;"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista(0);
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd przywracania</span><br />";
					$rettext.=$this->lista(0);
				}
			}
			else
			{
				$rettext .= "This operation need confirm.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list
		public function get_list()
		{
			$rettext = array();
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select idu,imie_uczniowie,nazwisko_uczniowie from ".get_class($this)." where usuniety='nie' order by nazwisko_uczniowie;");
			if($wynik)
			{
				while(list($idu,$imie_uczniowie,$nazwisko_uczniowie)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idu, "$nazwisko_uczniowie $imie_uczniowie");
				}
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
			$wynik=$this->page_obj->database_obj->get_data("select idu,imie_uczniowie,nazwisko_uczniowie from ".get_class($this)." where usuniety='nie' and idkl=$idkl order by nazwisko_uczniowie;");
			if($wynik)
			{
				while(list($idu,$imie_uczniowie,$nazwisko_uczniowie)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idu, "$nazwisko_uczniowie $imie_uczniowie");
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
				$wynik=$this->page_obj->database_obj->get_data("select CONCAT(imie_uczniowie,' ',nazwisko_uczniowie) from ".get_class($this)." where idu=$idu");
				if($wynik)
				{
					list($imie_uczniowie_nazwisko_uczniowie)=$wynik->fetch_row();
				}
			}
			return $imie_uczniowie_nazwisko_uczniowie;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region create_select_field_from_klasa
		private function create_select_field_from_klasa($idkl)
		{
			$rettext="<select name='idkl' id='idkl'>";
			//--------------------
			foreach($this->page_obj->klasa->get_list() as $val)
			{
				$rettext .= "<option value='$val[0]' ".($val[0]=="$idkl"?"selected='selected'":"").">{$this->page_obj->oddzialy->get_name($val[1])} - $val[2]</option>";
			}
			//--------------------
			$rettext .= "</select>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region create_select_field_from_oddzial
		private function create_select_field_from_oddzial($idod,$select_id,$idkl)
		{
			$this->update_select_field_from_oddzialy_js_script="";
			$rettext="<select name='idod_tmp' onchange='document.getElementById(\"$select_id\").innerHTML = update_$select_id(this.value);'>";
			//--------------------
			$this->update_select_field_from_oddzialy_js_script .= "<script>";
			$this->update_select_field_from_oddzialy_js_script .= "function update_$select_id(idod){var opcje='';";
			$this->update_select_field_from_oddzialy_js_script .= "switch(idod){";
			foreach($this->page_obj->oddzialy->get_list() as $val)
			{
				$rettext .= "<option value='$val[0]' ".($val[0]==$idod?"selected='selected'":"").">{$val[1]}</option>";
				$this->update_select_field_from_oddzialy_js_script .= "case '$val[0]':";
				foreach($this->page_obj->klasa->get_list_for_idod($val[0]) as $kval)
				{
					$this->update_select_field_from_oddzialy_js_script .= "opcje=opcje+'<option value=\"$kval[0]\" ".($kval[0]==$idkl?"selected=\"selected\"":"").">$kval[2]</option>';";
				}
				$this->update_select_field_from_oddzialy_js_script .= "break;";
			};
			$this->update_select_field_from_oddzialy_js_script .= "};";
			$this->update_select_field_from_oddzialy_js_script .= "return opcje;};document.getElementById('$select_id').innerHTML = update_$select_id('$idod');</script>";
			//--------------------
			$rettext .= "</select>";
			
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region create_block_opiekunowie
		private function create_block_opiekunowie($opiekunowie_array)
		{
			$rettext="";
			//--------------------
			$rettext .= "<input type='button' value='+' onclick='opiekunowie.create_block();'/>";
			//--------------------
			$opcje_wyboru = "";
			foreach($this->page_obj->opiekunowie->get_list() as $val)
			{
				$opcje_wyboru .= "opiekunowie.select_opiekunowie_options.push([{$val[0]},'{$val[1]}']);\n";
			}
			//--------------------
			$rettext .= "<script>";
			$rettext.=$opcje_wyboru;
			foreach($opiekunowie_array as $val)
			{
				$rettext .= "opiekunowie.create_block($val[0]);";
			}
			$rettext .= "</script>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region szczegoly
		public function szczegoly($idu)
		{
			$rettext = "";
			//--------------------
			$is_deleted = $this->is_deleted($idu);
			//--------------------
			$rettext .= "<button class='button_add' title='Drukuj' type='button' onclick='var printWindow = window.open(\"".get_class($this).",raw,szczegoly_drukuj,$idu\",\"chaild\");printWindow.print();printWindow.onafterprint = function(){printWindow.close()};return false;'>Drukuj</button>&#160;";
			$rettext .= "<button class='button_add' style='width:240px;' title='Dodaj identyfikator płatności' type='button' onclick='window.location=\"iden_wyciagu,{$this->page_obj->template},form,$idu\"'>Dodaj identyfikator płatności</button>&#160;";
			$rettext .= "<button class='button_add' style='width:140px;' title='Wpłata gotówki' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},form_gotowka,$idu\"'>Wpłata gotówki</button>&#160;";
			$rettext .= "<b style='font-size:20px;'><u>" . $this->get_imie_uczniowie_nazwisko_uczniowie($idu) . "</u></b><br /><br /><br />";
			//-----
			$rettext .= "<script type='text/javascript' src='./js/opticaldiv.js'></script>";
			$rettext .= "<b style='font-size:16px;'><u>OPŁATY</u></b><br /><br />";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:35px;'>Lp.</td>
						<td>nazwa</td>
						<td>komentarz</td>
						<td style='width:100px;'>kwota</td>
						<td>rabat nazwa</td>
						<td style='width:100px;'>rabat kwota</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
			$oplaty_list = $this->page_obj->uczniowie_oplaty->get_liste_oplat_dla_ucznia($idu); //[iduop, idop, rabat_kwota, rabat_nazwa, zaplacone_online, comment]
			if(is_array($oplaty_list))
			{
				$suma_do_rozliczenia = 0;
				$suma_oplat = 0;
				$suma_rabat = 0;
				$oplata_counter = 1;
				foreach($oplaty_list as $row)
				{
					$oplata_nazwa = $this->page_obj->oplaty->get_name($row[1]);
					$oplata_kwota = $this->page_obj->oplaty->get_kwota($row[1]);
					$suma_oplat += $oplata_kwota;
					$oplata_rabat = $row[2]; //to jest w kwocie a nie w %
					$suma_rabat += $oplata_rabat;
					$edytuj_oplate_link = "<a href='uczniowie_oplaty,{$this->page_obj->template},formularz,$row[0]'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a>";
					if( $row[4] == false )
					{
					$suma_do_rozliczenia += ($oplata_kwota - $oplata_rabat);
					}
					$rettext .= "<tr>
										<td>$oplata_counter</td>
										<td>$oplata_nazwa</td>
										<td>{$row[5]}</td>
										<td>$oplata_kwota</td>
										<td>{$row[3]}</td>
										<td>{$row[2]}</td>
										<td></td>
										<td>$edytuj_oplate_link</td>
									</tr>";
									//	idop = $oplata_nazwa, {$row[1]} - rabat:  - {$row[3]} -  - $oplata_rabat = ".(($oplata_kwota - $oplata_rabat))." $edytuj_oplate_link<br />";
					$oplata_counter++;
				}
				$rettext .= "<tr>
									<td></td>
									<td></td>
									<td></td>
									<td style='border-top:1px solid gray;'>$suma_oplat</td>
									<td></td>
									<td style='border-top:1px solid gray;'>$suma_rabat</td>
									<td></td>
									<td></td>
								</tr>";
				$rettext .= "<tr>
									<td colspan='8'><b>Do zapłaty w sumie: $suma_do_rozliczenia </b></td>
								</tr>";
			}
			$rettext .= "</table><br /><br />";
			//----------------------------------------------------------------------------------------------------
			$rettext .= "<hr /><br />";
			//----------------------------------------------------------------------------------------------------

			//----------------------------------------------------------------------------------------------------
			// WYCIĄGI
			//----------------------------------------------------------------------------------------------------
			if($is_deleted)
			{
				$strike_tag_start = "<s>";
				$strike_tag_end = "</s>";
			}
			else
			{
				$strike_tag_start = "";
				$strike_tag_end = "";
			}

			$rettext .= "<b style='font-size:16px;'><u>WYCIĄGI</u></b><br /><br />";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:85px;'>Lp.</td>
						<td>tytuł</td>
						<td>nadawca</td>
						<td style='width:250px;'>data</td>
						<td style='width:100px;'>kwota</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
			
			$suma_rozliczen = 0;
			$oplata_counter = 1;
			$wyciagi_list = $this->page_obj->wyciagi_uczniowie->get_liste_wyciagow_dla_ucznia($idu);
			if(is_array($wyciagi_list))
			{
				
				foreach($wyciagi_list as $idw)
				{
					$kwota = $this->page_obj->wyciagi_uczniowie->get_kwota($idw);
					$tytul = $this->page_obj->wyciagi->get_tytul($idw);
					$data = $this->page_obj->wyciagi->get_date($idw);
					$nadawca = $this->page_obj->wyciagi->get_nadawce($idw);
					if(!is_nan($kwota))
					{
						$suma_rozliczen += $kwota;
					}
					$edytuj_oplate_link = "<a href='#'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a>";
					
					$rettext .= "<tr>
										<td>$strike_tag_start $oplata_counter ($idw) $strike_tag_end</td>
										<td>$strike_tag_start $tytul $strike_tag_end</td>
										<td>$strike_tag_start $nadawca $strike_tag_end</td>
										<td>$strike_tag_start $data $strike_tag_end</td>
										<td>$strike_tag_start $kwota $strike_tag_end</td>
										<td></td>
										<td>". ( (!$is_deleted) ? $edytuj_oplate_link : "" ) ."</td>
									</tr>";
					$oplata_counter++;
				}
			}
			//gotówka
			$wyciagi_list = $this->page_obj->iden_wyciagu->get_liste_wyciagow_dla_ucznia($idu);
			if(is_array($wyciagi_list))
			{
				foreach($wyciagi_list as $idw)
				{
					$kwota = $this->page_obj->wyciagi->get_kwota($idw);
					$tytul = $this->page_obj->wyciagi->get_tytul($idw);
					$data = $this->page_obj->wyciagi->get_date($idw);
					//$nadawca = $this->page_obj->wyciagi->get_nadawce($idw);
					$nadawca = "gotówka";
					if(!is_nan($kwota))
					{
						$suma_rozliczen += $kwota;
					}
					$edytuj_oplate_link = "<a href='#'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a>";
					$rettext .= "<tr>
										<td>$strike_tag_start $oplata_counter [$idw] $strike_tag_end</td>
										<td>$strike_tag_start $tytul $strike_tag_end</td>
										<td>$strike_tag_start $nadawca $strike_tag_end</td>
										<td>$strike_tag_start $data $strike_tag_end</td>
										<td>$strike_tag_start $kwota $strike_tag_end</td>
										<td></td>
										<td>$edytuj_oplate_link</td>
									</tr>";
					$oplata_counter++;
				}
			}

			$rettext .= "<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td style='border-top:1px solid gray;'>$strike_tag_start $suma_rozliczen $strike_tag_end</td>
									<td></td>
									<td></td>
								</tr>";
			$rettext .= "<tr>
									<td colspan='7'>$strike_tag_start <b>Rozliczono w sumie: $suma_rozliczen </b>$strike_tag_end</td>
								</tr>";
			$rettext .= "</table><br /><br />";
			//----------------------------------------------------------------------------------------------------
			$rettext .= "<hr /><br />";
			//----------------------------------------------------------------------------------------------------
			$rettext .= $this->platnosci_online($idu);
			//----------------------------------------------------------------------------------------------------
			if($suma_rozliczen > $suma_do_rozliczenia)
			{
				$rettext .= "<b style='font-size:16px;'>Nadpłata: ".($suma_rozliczen - $suma_do_rozliczenia)."</b><br />";
			}
			else if($suma_rozliczen < $suma_do_rozliczenia)
			{
				$rettext .= "<b style='font-size:16px;'>Niedopłata: ".($suma_do_rozliczenia - $suma_rozliczen)."</b><br />";
			}
			else
			{
				$rettext .= "<b style='font-size:16px;'>Rozliczone</b><br />";
			}
			//--------------------
			$rettext .= "<br /><hr /><br />";
			//--------------------
			$rettext .= "<b style='font-size:16px;'><u>IDENTYFIKATORY PŁATNOŚCI:</u></b><br /><br />";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:35px;'>Lp.</td>
						<td>identyfikator</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
			$lp = 1;
			foreach( $this->page_obj->iden_wyciagu->get_list($idu) as $key => $val)
			{
				if($val[1] == 'nie')
				{
					$operacja = "<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"iden_wyciagu,{$this->page_obj->template},usun,$idu,$val[2],yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
				}
				else
				{
					$operacja = "<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"iden_wyciagu,{$this->page_obj->template},przywroc,$idu,$val[2],yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:30px;'/></a>";
				}
				//--------------------
				// link do edycji: <a href='iden_wyciagu,{$this->page_obj->template},form,$idu,$val[2]'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a>
				$rettext .= "
					<tr style='font-weight:bold;".(($val[3] != 0)?"color:red;":"color:gray;")."'>
						<td>$lp.</td>
						<td>$val[0]</td>
						<td style='width:18px;'>".(($val[3] != 0)?"*":"")."</td>
						<td style='width:18px;'>$operacja</td>
					</tr>";
				$lp++;
			}
			$rettext .= "</table>";
			$rettext .= "<br /><hr /><br />";
			//--------------------
			// przypisane konta bankowe
			//--------------------
			$rettext .= "<b style='font-size:16px;'><u>PRZYPISANE NUMERY KONT:</u></b><br /><br />";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:35px;'>Lp.</td>
						<td>numer konta</td>
						<td>nazwa nadawcy</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
			$counter = 1;
			foreach($this->page_obj->uczniowie_konta_bankowe->get_list_for_idu($idu) as $key => $val) //[idukb, numer_konta]
			{
				$delete_link = "<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun_konto_bankowe,{$val[0]},yes,$idu\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
				$nazwanadawcy = $this->page_obj->wyciagi->get_nazwanadawcy_from_rachuneknadawcy($val[1]);
				$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:35px;'>$counter.</td>
						<td>{$val[1]}</td>
						<td>$nazwanadawcy</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'>$delete_link</td>
					</tr>";
				$counter++;
			}
			$rettext .= "</table>";
			$rettext .= "<br /><hr /><br />";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region szczegoly_drukuj
		private function szczegoly_drukuj($idu)
		{
			$rettext = "";
			//--------------------
			$rettext .= "<!DOCTYPE HTML PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>";
			$rettext .= "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='pl'>";
			$rettext .= "<head>";
			$rettext .= "<meta http-equiv='Content-Type' content='application/xhtml+xml; charset=utf-8' />";
			$rettext .= "</head>";
			$rettext .= "<html>";
			//--------------------
			$rettext .= "<b style='font-size:20px;'><u>" . $this->get_imie_uczniowie_nazwisko_uczniowie($idu) . "</u></b><br /><br /><br />";
			//-----
			$rettext .= "<script type='text/javascript' src='./js/opticaldiv.js'></script>";
			$rettext .= "<b style='font-size:16px;'><u>OPŁATY</u></b><br /><br />";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:35px;'>Lp.</td>
						<td>nazwa</td>
						<td>komentarz</td>
						<td style='width:100px;'>kwota</td>
						<td>rabat nazwa</td>
						<td style='width:100px;'>rabat kwota</td>
					</tr>";
			$oplaty_list = $this->page_obj->uczniowie_oplaty->get_liste_oplat_dla_ucznia($idu); // [iduop, idop, rabat_kwota, rabat_nazwa, zaplacone_online, comment]
			if(is_array($oplaty_list))
			{
				$suma_do_rozliczenia = 0;
				$suma_oplat = 0;
				$suma_rabat = 0;
				$oplata_counter = 1;
				foreach($oplaty_list as $row)
				{
					$oplata_nazwa = $this->page_obj->oplaty->get_name($row[1]);
					$oplata_kwota = $this->page_obj->oplaty->get_kwota($row[1]);
					$suma_oplat += $oplata_kwota;
					$oplata_rabat = $row[2]; //to jest w kwocie a nie w %
					$suma_rabat += $oplata_rabat;
					$edytuj_oplate_link = "<a href='uczniowie_oplaty,{$this->page_obj->template},formularz,$row[0]'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a>";
					if( $row[4] == false )
					{
						$suma_do_rozliczenia += ($oplata_kwota - $oplata_rabat);
					}
					$rettext .= "<tr>
										<td>$oplata_counter</td>
										<td>$oplata_nazwa</td>
										<td>{$row[5]}</td>
										<td>$oplata_kwota</td>
										<td>{$row[3]}</td>
										<td>{$row[2]}</td>
									</tr>";
									//	idop = $oplata_nazwa, {$row[1]} - rabat:  - {$row[3]} -  - $oplata_rabat = ".(($oplata_kwota - $oplata_rabat))." $edytuj_oplate_link<br />";
					$oplata_counter++;
				}
				$rettext .= "<tr>
									<td></td>
									<td></td>
									<td></td>
									<td style='border-top:1px solid gray;'>$suma_oplat</td>
									<td></td>
									<td style='border-top:1px solid gray;'>$suma_rabat</td>
								</tr>";
				$rettext .= "<tr>
									<td colspan='8'><b>Do zapłaty w sumie: $suma_do_rozliczenia </b></td>
								</tr>";
			}
			$rettext .= "</table><br /><br />";
			//----------------------------------------------------------------------------------------------------
			$rettext .= "<hr /><br />";
			//----------------------------------------------------------------------------------------------------
			$rettext .= "<b style='font-size:16px;'><u>WYCIĄGI</u></b><br /><br />";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:35px;'>Lp.</td>
						<td>tytuł</td>
						<td>nadawca</td>
						<td style='width:250px;'>data</td>
						<td style='width:100px;'>kwota</td>
					</tr>";
			$wyciagi_list = $this->page_obj->wyciagi_uczniowie->get_liste_wyciagow_dla_ucznia($idu);
			if(is_array($wyciagi_list))
			{
				$suma_rozliczen = 0;
				$oplata_counter = 1;
				foreach($wyciagi_list as $idw)
				{
					$kwota = $this->page_obj->wyciagi_uczniowie->get_kwota($idw);
					$tytul = $this->page_obj->wyciagi->get_tytul($idw);
					$data = $this->page_obj->wyciagi->get_date($idw);
					$nadawca = $this->page_obj->wyciagi->get_nadawce($idw);
					if(!is_nan($kwota))
					{
						$suma_rozliczen += $kwota;
					}
					$edytuj_oplate_link = "<a href='#'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a>";
					$rettext .= "<tr>
										<td>$oplata_counter</td>
										<td>$tytul</td>
										<td>$nadawca</td>
										<td>$data</td>
										<td>$kwota</td>
									</tr>";
					$oplata_counter++;
				}
			}
			//gotówka
			$wyciagi_list = $this->page_obj->iden_wyciagu->get_liste_wyciagow_dla_ucznia($idu);
			if(is_array($wyciagi_list))
			{
				foreach($wyciagi_list as $idw)
				{
					$kwota = $this->page_obj->wyciagi->get_kwota($idw);
					$tytul = $this->page_obj->wyciagi->get_tytul($idw);
					$data = $this->page_obj->wyciagi->get_date($idw);
					//$nadawca = $this->page_obj->wyciagi->get_nadawce($idw);
					$nadawca = "gotówka";
					if(!is_nan($kwota))
					{
						$suma_rozliczen += $kwota;
					}
					$edytuj_oplate_link = "<a href='#'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a>";
					$rettext .= "<tr>
										<td>$oplata_counter</td>
										<td>$tytul</td>
										<td>$nadawca</td>
										<td>$data</td>
										<td>$kwota</td>
									</tr>";
					$oplata_counter++;
				}
			}
			$rettext .= "<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td style='border-top:1px solid gray;'>$suma_rozliczen</td>
								</tr>";
			$rettext .= "<tr>
									<td colspan='7'><b>Rozliczono w sumie: $suma_rozliczen </b></td>
								</tr>";
			$rettext .= "</table><br /><br />";
			//----------------------------------------------------------------------------------------------------
			$rettext .= "<hr /><br />";
			//----------------------------------------------------------------------------------------------------
			$rettext .= $this->platnosci_online($idu);
			//----------------------------------------------------------------------------------------------------
			if($suma_rozliczen > $suma_do_rozliczenia)
			{
				$rettext .= "<b style='font-size:16px;'>Nadpłata: ".($suma_rozliczen - $suma_do_rozliczenia)."</b><br />";
			}
			else if($suma_rozliczen < $suma_do_rozliczenia)
			{
				$rettext .= "<b style='font-size:16px;'>Niedopłata: ".($suma_do_rozliczenia - $suma_rozliczen)."</b><br />";
			}
			else
			{
				$rettext .= "<b style='font-size:16px;'>Rozliczone</b><br />";
			}
			//--------------------
			$rettext .= "</html>";
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_kwota_do_zaplaty
		private function get_kwota_do_zaplaty($idu)
		{
			$rettext = array();
			$rettext[0] = 0;//suma_rabat
			$rettext[1] = 0;//suma_oplat
			$rettext[2] = 0;//suma_do_rozliczenia
			//--------------------
			$oplaty_list = $this->page_obj->uczniowie_oplaty->get_liste_oplat_dla_ucznia($idu); //[iduop, idop, rabat_kwota, rabat_nazwa, zaplacone_online]
			if(is_array($oplaty_list))
			{
				$suma_do_rozliczenia = 0;
				$suma_oplat = 0;
				$suma_rabat = 0;
				foreach($oplaty_list as $row)
				{
					$oplata_kwota = $this->page_obj->oplaty->get_kwota($row[1]);
					$suma_oplat += $oplata_kwota;
					$oplata_rabat = $row[2]; //to jest w kwocie a nie w %
					$suma_rabat += $oplata_rabat;
					$suma_do_rozliczenia += ($oplata_kwota - $oplata_rabat);
				}
				$rettext[0] = $suma_oplat;//suma_rabat
				$rettext[1] = $suma_rabat;//suma_oplat
				$rettext[2] = $suma_do_rozliczenia;//suma_do_rozliczenia
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_kwota_rozliczona
		private function get_kwota_rozliczona($idu)
		{
			$rettext = 0;
			//--------------------
			$wyciagi_list = $this->page_obj->wyciagi_uczniowie->get_liste_wyciagow_dla_ucznia($idu);
			if(is_array($wyciagi_list))
			{
				$suma_rozliczen = 0;
				foreach($wyciagi_list as $idw)
				{
					$kwota = $this->page_obj->wyciagi_uczniowie->get_kwota($idw);
					if(!is_nan($kwota))
					{
						$suma_rozliczen += $kwota;
					}
				}
				$rettext = $suma_rozliczen;
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region form_gotowka
		private function form_gotowka($idu, $kwota, $tytul)
		{
			$rettext = "";
			//--------------------
			$rettext .= "<b style='font-size:20px;'><u>" . $this->get_imie_uczniowie_nazwisko_uczniowie($idu) . "</u></b><br /><br /><br />";
			//--------------------
			$rettext .= "
				<style>
					div.wiersz{float:left;clear:left;}
					div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
					div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
				</style>";
			$rettext .= "
				<form method='post' action='".get_class($this).",{$this->page_obj->template},zapisz_gotowka'>
					<div style='overflow:hidden;'>
						<div class='wiersz'><div class='formularzkom1'>Tytuł: </div><div class='formularzkom2'><input type='text' name='tytul' value='$tytul' style='width:150px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>Kwota: </div><div class='formularzkom2'><input type='number' step='0.01' name='kwota' value='$kwota' style='width:150px;'/></div></div>
						<div class='wiersz'>
							<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
									<button title='Anuluj' style='font-size:20px;float:right;' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},szczegoly,$idu\"'>Anuluj</button>
								</div>
							</div>
						</div>
					</div>
					<input type='hidden' name='idu' value='$idu' />
				</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region zapisz_gotowka
		private function zapisz_gotowka($idu, $kwota, $tytul)
		{
			$rettext = "";
			//--------------------
			$wzor[1] = "/,/";
         $zamiany[1] = ".";
			$kwota = preg_replace($wzor, $zamiany, $kwota);
			//--------------------
			$idw = $this->page_obj->wyciagi->add_and_return_idw(0,$tytul, "gotowka", $kwota);
			if($idw != 0)
			{
				$nrreferencyjny = $this->page_obj->wyciagi->get_nrreferencyjny($idw);
				$this->page_obj->iden_wyciagu->insert_and_asign($idu,$idw,$nrreferencyjny);
			}
			else
				$rettext .= "Błąd";
			$rettext .= $this->szczegoly($idu);
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		private function platnosci_online($idu)
		{
			$rettext = "";
			//--------------------
			$rettext .= "<b style='font-size:16px;'><u>Płatności Blue Media</u></b><br /><br />";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:35px;'>Lp.</td>
						<td>tytuł</td>
						<td>order ID</td>
						<td style='width:250px;'>data</td>
						<td style='width:100px;'>kwota</td>
						
					</tr>";
			//--------------------
			if( isset($idu) && ($idu > 0) )
			{
				$zapytanie = "select uo.iduop, uo.idop, bm.orderID, bm.amount, bm.date from uczniowie_oplaty uo, blue_media_uczniowie_oplaty bmuo, blue_media bm where uo.idu = $idu and uo.iduop = bmuo.iduop and bmuo.idbm = bm.idbm and bm.status = 'oplacone';";
				$wynik = $this->page_obj->database_obj->get_data($zapytanie);
				if($wynik)
				{
					$oplata_counter = 1;
					while( list($iduop, $idop, $orderID, $amount, $date) = $wynik->fetch_row() )
					{
						$nazwa_oplaty = $this->page_obj->oplaty->get_name($idop);

						$rettext .= "<tr>
											<td>$oplata_counter</td>
											<td>$nazwa_oplaty</td>
											<td>$orderID</td>
											<td>$date</td>
											<td>$amount</td>
											
										</tr>";
						$oplata_counter++;
					}
				}
			}
			//--------------------
			$rettext .= "</table><br /><br />";
			//--------------------
			$rettext .= "<hr /><br />";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function usun_konto_bankowe($idukb, $confirm, $idu)
		{
			$rettext = "";
			//--------------------
			if($confirm == "yes")
			{
				// pobrać numer konta z uczniowie_konta_bankowe join konta_bankowe where idukb = $idukb
				$numer_konta = $this->page_obj->uczniowie_konta_bankowe->get_numer_konta_for_idukb($idukb);
				// pobrać wszystkie idw z wyciagi where rachuneknadawcy = powyższe 
				$idw_array = $this->page_obj->wyciagi->get_idw_list_for_rachuneknadawcy($numer_konta);
				foreach($idw_array as $key => $val)
				{
					// usunąć wszystkie wpisy z wyciagi_uczniowie where idu = idu and idw in (powyższe)
					$this->page_obj->wyciagi_uczniowie->delete_idw($val, $idu);
				}
				// usunąć z uczniowie_konta_bankowe where idukb
				$this->page_obj->uczniowie_konta_bankowe->delete($idukb);
			}
			//--------------------
			$rettext .= $this->szczegoly($idu);
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function is_deleted($idu)
		{
			$result = false;
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select usuniety from ".get_class($this)." where idu = $idu ;");
			if($wynik)
			{
				list($usuniety) = $wynik->fetch_row();
				if( $usuniety != 'nie')
				{
					$result = true;
				}
			}
			//--------------------
			return $result;
		}
		//----------------------------------------------------------------------------------------------------
		#region definicjabazy
		private function definicjabazy()
		{
			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy=get_class($this);
			$pola=array();
			
			//definicja tablicy
			$nazwa="idu";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idkl";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie','zablokowany')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="imie_uczniowie";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="nazwisko_uczniowie";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="numer_indeksu";
			$pola[$nazwa][0]="varchar(20)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//--------------------            
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
	die("Class exists: ".__FILE__);
?>