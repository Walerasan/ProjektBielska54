<?php
if(!class_exists('oplaty'))
{
	class oplaty
	{
		var $page_obj;
		var $javascript_select_uczniowie;
		var $update_select_field_from_oddzialy_js_script;
		var $update_select_field_from_klasa_js_script;
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
					case "formularz_podsumowanie_oddzial":
						$rok = isset($_POST['rok']) ? $_POST['rok'] : date("Y");
						$miesiac = isset($_POST['miesiac']) ? $_POST['miesiac'] : date("m");
						$store = isset($_POST['store']) ? $_POST['store'] == "true" : false;
						$idod = isset($_POST['idod']) ? $_POST['idod'] : 1;
						$content_text .= $this->formularz_podsumowanie_oddzial($rok, $miesiac, $store, $idod);
						break;
					case "formularz_podsumowanie_miesiaca":
						$rok = isset($_POST['rok']) ? $_POST['rok'] : date("Y");
						$miesiac = isset($_POST['miesiac']) ? $_POST['miesiac'] : date("m");
						$store = isset($_POST['store']) ? $_POST['store'] == "true" : false;
						$content_text .= $this->formularz_podsumowanie_miesiaca($rok, $miesiac, $store);
						break;
					case "formularz_zestawienie":
						$od = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['od'])?$_POST['od']:date("Y-m-d"));
						$do = isset($_GET['par2'])?$_GET['par2']:(isset($_POST['do'])?$_POST['do']:date("Y-m-d"));
						$store_od_do = isset($_GET['par3'])?$_GET['par3']:(isset($_POST['store_od_do'])?$_POST['store_od_do']:"no");
						$content_text .= $this->formularz_zestawienie($od,$do,$store_od_do);
						break;
					case "przywroc":
						$idop = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$confirm = isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text .= $this->restore($idop,$confirm);
					break;
					case "usun":
						$idop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->delete($idop,$confirm);
					break;
					case "zapisz":
						$idop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$idto=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idto'])?$_POST['idto']:0);
						$nazwa=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
						$kwota=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['kwota'])?$_POST['kwota']:"");
						//$content_text=$this->add($idop,$idto,$nazwa,$kwota);
						$content_text .= "Blokada"; //delete this line to unlock
					break;
					case "zapisz_uczen":
						$idop = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$nazwa = isset($_GET['par2'])?$_GET['par2']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
						$kwota = isset($_GET['par3'])?$_GET['par3']:(isset($_POST['kwota'])?$_POST['kwota']:"");
						$idto = isset($_GET['par4'])?$_GET['par4']:(isset($_POST['idto'])?$_POST['idto']:0);
						$selected_uczniowie = isset($_GET['par5'])?$_GET['par5']:(isset($_POST['selected_uczniowie'])?$_POST['selected_uczniowie']:0);
						$data = isset($_GET['par6'])?$_GET['par6']:(isset($_POST['data'])?$_POST['data']:0);
						$content_text.=$this->zapisz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie,$data);
					break;
					case "formularz_uczen":
						$idop = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$nazwa = isset($_GET['par2'])?$_GET['par2']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
						$kwota = isset($_GET['par3'])?$_GET['par3']:(isset($_POST['kwota'])?$_POST['kwota']:"");
						$idto = isset($_GET['par4'])?$_GET['par4']:(isset($_POST['idto'])?$_POST['idto']:0);
						$selected_uczniowie = isset($_GET['par5'])?$_GET['par5']:(isset($_POST['selected_uczniowie'])?$_POST['selected_uczniowie']:0);
						$data = isset($_GET['par6'])?$_GET['par6']:(isset($_POST['data'])?$_POST['data']:0);
						$content_text.=$this->formularz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie,$data);
						break;
					break;
					case "lista":
					default:
						$aktualnailosc = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['aktualnailosc'])?$_POST['aktualnailosc']:0);
						$content_text.=$this->lista($aktualnailosc);
						break;
				}
			}
			else if( ($this->page_obj->template == "raw") )
			{
				switch($this->page_obj->target)
				{
					case "podsumowanie_oddzial_drukuj":
						$content_text .= $this->podsumowanie_oddzial_drukuj();
						break;
					case "podsumowanie_miesiaca_drukuj":
						$content_text .= $this->podsumowanie_miesiaca_drukuj();
						break;
					case "zestawienie_drukuj":
						$content_text .= $this->zestawienie_drukuj();
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
			$rettext .= "<button class='button_add' title='Dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz_uczen\"'>Dodaj nowy</button>&#160;";
			$rettext .= "<button class='button_raport' title='Zestawienie' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz_zestawienie\"'>Zestawienie</button>&#160;";
			$rettext .= "<button class='button_raport' style='width:200px;' title='Podsumowanie miesiąca' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz_podsumowanie_miesiaca\"'>Podsumowanie miesiąca</button>&#160;";
			$rettext .= "<button class='button_raport' style='width:200px;' title='Podsumowanie oddziału' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz_podsumowanie_oddzial\"'>Podsumowanie oddziału</button>&#160;";
			$rettext .= "<br />";
			//--------------------
			if($aktualnailosc == "") $aktualnailosc = 0;
			$this->page_obj->database_obj->get_data("select idop,idto,nazwa,kwota,usuniety from ".get_class($this).";");
			$iloscwszystkich=$this->page_obj->database_obj->result_count();
			$iloscnastronie = 15;
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idop,idto,nazwa,kwota,usuniety from ".get_class($this)." limit $aktualnailosc,$iloscnastronie;");
			if($wynik)
			{
				$rettext .= "<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext .= "<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
				$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>tytuł</td>
						<td>kwota</td>
						<td>komentarz</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=0;
				while(list($idop,$idto,$nazwa,$kwota,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idop,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idop,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:30px;'/></a>";
					}
					//--------------------
					$rettext .= "
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idop' onmouseover=\"setopticalwhite50('wiersz$idop')\" onmouseout=\"setoptical0('wiersz$idop')\">
							<td style='text-align:right;padding-right:10px;color:#555555;'>".($aktualnailosc + $lp).".</td>
							<td>$nazwa</td>
							<td>$kwota</td>
							<td>{$this->page_obj->typy_oplat->get_name($idto)}</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz_uczen,$idop'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a></td>
							<td style='text-align:center;'>$operacja</td>
						</tr>";
				}
				$rettext .= "</table>";
				$rettext .= "<div style='text-align:center;clear:both;'>".$this->page_obj->subpages->create($iloscwszystkich,$iloscnastronie,$aktualnailosc,get_class($this).",".$this->page_obj->template.",lista")."</div>";
			}
			else
			{
				$rettext .= "<br />Brak wpisów<br />";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region form
		public function form($idop,$idto,$nazwa,$kwota)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($idop!="" && is_numeric($idop) && $idop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idto,nazwa,kwota from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($idto,$nazwa,$kwota)=$wynik->fetch_row();
				}
			}
			//--------------------
			$nazwa=$this->page_obj->text_obj->doedycji($nazwa);
			$kwota=$this->page_obj->text_obj->doedycji($kwota);
			//--------------------
			$rettext="
					<style>
						div.wiersz{float:left;clear:left;}
						div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
						div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
					</style>";
			$rettext .= "
					<form method='post' action='".get_class($this).",{$this->page_obj->template},zapisz'>
						<div style='overflow:hidden;'>
							<div class='wiersz'><div class='formularzkom1'>Nazwa: </div><div class='formularzkom2'><input type='text' name='nazwa' value='$nazwa' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>typ: </div><div class='formularzkom2'>{$this->create_select_field_from_typy_oplat($idto)}</div></div>
							<div class='wiersz'><div class='formularzkom1'>kwota: </div><div class='formularzkom2'><input type='number' step='0.01' name='kwota' value='$kwota' style='width:800px;'/></div></div>
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
									<button title='Anuluj' style='font-size:20px;float:right;' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idop' value='$idop' />
					</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($idop,$idto,$nazwa,$kwota)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$nazwa = $this->page_obj->text_obj->domysql($nazwa);
			$kwota = $this->page_obj->text_obj->domysql($kwota);
			//--------------------
			if( ($idop != "") && is_numeric($idop) && ($idop > 0) )
			{
				$zapytanie="update ".get_class($this)." set nazwa='$nazwa', kwota=$kwota, idto=$idto where idop=$idop;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(nazwa,kwota,idto)values('$nazwa',$kwota,$idto)";//nowy wpis
			}
			//--------------------
			if(!$_SESSION['antyrefresh'])
			{
				if($this->page_obj->database_obj->execute_query($zapytanie))
				{
					$_SESSION['antyrefresh']=true;
					$rettext .= "Zapisane<br />";
					$rettext.=$this->lista(0);
				}
				else
				{
					$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext.=$this->form($idop,$idto,$nazwa,$kwota);
				}
			}
			else
			{
				$rettext.=$this->lista(0);
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region delete
		public function delete($idop,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idop=$idop;"))
				{
					$this->page_obj->powiadomienia->mark_oplata_usunieta_from_nowe($idop);
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
		public function restore($idop,$confirm)
		{
			//dorobić by przywracał powiadomienia
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idop=$idop;"))
				{
					$this->page_obj->powiadomienia->mark_nowe_from_oplata_usunieta($idop);
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
		#region create_select_field_from_typy_oplat
		private function create_select_field_from_typy_oplat($idto)
		{
			$rettext="<select name='idto'>";
			//--------------------
			foreach($this->page_obj->typy_oplat->get_list() as $val)
			{
				$rettext .= "<option value='$val[0]' ".($val[0]=="$idto"?"selected='selected'":"").">$val[1]</option>";
			}
			//--------------------
			$rettext .= "</select>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list
		public function get_list()
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idop,idto,nazwa,kwota from ".get_class($this)." where usuniety='nie';");
			if($wynik)
			{
				while(list($idop,$idto,$nazwa,$kwota)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idop,(int)$idto,$nazwa,(float)$kwota);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_name
		public function get_name($idop)
		{
			$nazwa='';
			if($idop!="" && is_numeric($idop) && $idop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select nazwa from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($nazwa)=$wynik->fetch_row();
				}
			}
			return $nazwa;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_kwota
		public function get_kwota($idop)
		{
			$kwota=NAN;
			if($idop!="" && is_numeric($idop) && $idop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select kwota from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($kwota)=$wynik->fetch_row();
				}
			}
			return $kwota;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_idto
		public function get_idto($idop)
		{
			$idto=0;
			if($idop!="" && is_numeric($idop) && $idop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idto from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($idto)=$wynik->fetch_row();
				}
			}
			return $idto;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region formularz_uczen
		private function formularz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie,$data)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if( isset($idop) && ($idop != "") && is_numeric($idop) && ($idop > 0) )
			{
				$wynik=$this->page_obj->database_obj->get_data("select idto,nazwa,kwota,data from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($idto,$nazwa,$kwota,$data)=$wynik->fetch_row();
				}
			}
			//--------------------
			$nazwa = $this->page_obj->text_obj->doedycji($nazwa);
			$kwota = $this->page_obj->text_obj->doedycji($kwota);
			$data = substr($data,0,10);
			//--------------------
			$rettext="
					<style>
						div.wiersz{float:left;clear:left;}
						div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
						div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
					</style>";
					$rettext .= "
					<form method='post' action='".get_class($this).",{$this->page_obj->template},zapisz_uczen'>
						<div style='overflow:hidden;'>
							<div class='wiersz'><div class='formularzkom1'>Nazwa: </div><div class='formularzkom2'><input type='text' name='nazwa' value='$nazwa' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>typ: </div><div class='formularzkom2'>{$this->create_select_field_from_typy_oplat($idto)}</div></div>
							<div class='wiersz'><div class='formularzkom1'>kwota: </div><div class='formularzkom2'><input type='text' name='kwota' value='$kwota' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>na dzień: </div><div class='formularzkom2'><input type='date' name='data' value='$data' style='width:150px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>oddział: </div><div class='formularzkom2'>{$this->create_select_field_for_oddzial('klasa_select')}</div></div>
							<div class='wiersz'><div class='formularzkom1'>klasa: </div><div class='formularzkom2'>{$this->create_select_field_for_klasa('klasa_select','uczniowie_select','selected_uczniowie')}</div></div>
							<div class='wiersz'>
								<div class='formularzkom1'>uczniowie: </div>
								<div class='formularzkom2'>
									<br />
									{$this->create_select_field_from_uczniowie($idop,'uczniowie_select','selected_uczniowie')}
								</div>
							</div>
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' onclick='selectAll();' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
									<button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"' style='font-size:20px;float:right;'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idop' value='$idop' />
					</form>
					{$this->update_select_field_from_oddzialy_js_script}
					{$this->update_select_field_from_klasa_js_script}
					{$this->javascript_select_uczniowie}";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region zapisz_uczen
		private function zapisz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie,$data)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$nazwa = $this->page_obj->text_obj->domysql($nazwa);
			$kwota = $this->page_obj->text_obj->domysql($kwota);

			$wzor[1]="/,/";
         $zamiany[1]=".";
			$kwota = preg_replace($wzor, $zamiany, $kwota);
			//--------------------
			if ( !is_numeric($kwota) )
			{
				$rettext .= "Nieprawidłowa zawartość pola kwota.<br />";
				$rettext .= $this->formularz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie,$data);
			}
			//--------------------
			if( ($idop != "") && is_numeric($idop) && ($idop > 0) )
			{
				$zapytanie="update ".get_class($this)." set nazwa='$nazwa', kwota=$kwota, idto=$idto, data='$data' where idop=$idop;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(nazwa,kwota,idto,data)values('$nazwa',$kwota,$idto,'$data')";//nowy wpis
			}
			//--------------------
			if(!$_SESSION['antyrefresh'])
			{
				if($this->page_obj->database_obj->execute_query($zapytanie))
				{
					$rettext .= "Zapisane<br />";
					#region save users
					if( ($idop == "") || !is_numeric($idop) || ($idop <= 0) )
					{
						$idop = $this->page_obj->database_obj->last_id();
					}
					$this->page_obj->uczniowie_oplaty->mark_delete($idop);
					$this->page_obj->powiadomienia->mark_delete($idop);
					if(isset($selected_uczniowie) && is_array($selected_uczniowie))
					{
						foreach($selected_uczniowie as $val)
						{
							$rettext .= "$val <br />";
							$rettext .= $this->page_obj->uczniowie_oplaty->synchronize($idop,$val);
							$rettext .= $this->page_obj->powiadomienia->synchronize($idop,$val);

						}
					}
					#endregion
					$_SESSION['antyrefresh']=true;
					$rettext.=$this->lista(0);
				}
				else
				{
					$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext .= "|" . $this->page_obj->database_obj->show_report_message() ."|";
					$rettext .= $this->formularz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie,$data);
				}
			}
			else
			{
				$rettext.=$this->lista(0);
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region create_select_field_from_uczniowie
		private function create_select_field_from_uczniowie($idop,$uczniowie_select_id,$selected_uczniowie_select_id)
		{
			$rettext="";
			//--------------------
			$selected_uczniowie = "";
			$uczniowie = "";
			$lista_uczniow = $this->page_obj->uczniowie->get_list();
			$lista_idu_w_oplacie = $this->page_obj->uczniowie_oplaty->get_idu_list($idop);
			foreach($lista_uczniow as $val)
			{
				if(in_array($val[0], $lista_idu_w_oplacie))
				{
					$selected_uczniowie .= "selected_option.push([{$val[0]},'{$val[1]}']);\n";
				}
				else
				{
					$uczniowie .= "available_option.push([{$val[0]},'{$val[1]}']);\n";
				}
			}
			//--------------------
			$rettext .= "<div style='float:left;'>
								<label for='selected_uczniowie' style='display:block;'>Wybrani uczniowie:</label>
								<select multiple='multiple' id='$selected_uczniowie_select_id' name='selected_uczniowie[]' style='display:block;width:200px;height:250px;'></select>
							</div>";
			$rettext .= "<div style='float:left;width:50px;text-align:center;height:250px;position: relative;'>
								<div style='display:block;position: absolute;top:25px;text-align:center;width:100%;'><a href='#' onclick='add_uczen_to_select();' style='font-size:30px;font-weight:bold;text-decoration:none;color:black;'> &lt;-</a></div>
								<div style='display:block;position: absolute;bottom:0px;text-align:center;width:100%;'><a href='#' onclick='remov_uczen_from_select();' style='font-size:30px;font-weight:bold;text-decoration:none;color:black;'> -&gt;</a></div>
							</div>";
			$rettext .= "<div style='float:left;'>
								<label for='selected_uczniowie' style='display:block;'>uczniowie:</label>
								<select multiple='multiple' id='$uczniowie_select_id' style='display:block;width:200px;height:250px;'></select>
							</div>";
			$rettext .= "";
			//--------------------
			$this->javascript_select_uczniowie="<script>
																var selected_option = new Array();
																var available_option = new Array();
																$selected_uczniowie
																$uczniowie
																function reload_selected_option()
																{
																	var select_field=document.getElementById(\"$selected_uczniowie_select_id\");
																	for(i = (select_field.options.length - 1); i >= 0; i--) select_field.remove(i);
																	for(i = 0; i < selected_option.length; i++)
																	{
																		select_field.options[select_field.options.length] = new Option(selected_option[i][1],selected_option[i][0]);
																	}
																};
																function reload_available_option()
																{
																	var select_field=document.getElementById(\"$uczniowie_select_id\");
																	for(i = (select_field.options.length - 1); i >= 0; i--) select_field.remove(i);
																	for(i = 0; i < available_option.length; i++)
																	{
																		select_field.options[select_field.options.length] = new Option(available_option[i][1],available_option[i][0]);
																	}
																};
																function add_uczen_to_select()
																{
																	var uczniowie=document.getElementById(\"$uczniowie_select_id\");
																	if ( uczniowie.selectedIndex >= 0 )
																	{
																		for ( var i = 0; i < uczniowie.options.length; i++ )
																		{
																			if ( uczniowie.options[ i ].selected )
																			{
																				var option_value = uczniowie.options[i].value;
																				selected_option.push(available_option.splice(available_option_find_position(option_value),1)[0]);
																			}
																		}
																	}
																	reload_available_option();
																	reload_selected_option();
																};
																function remov_uczen_from_select()
																{
																	var selected_uczniowie=document.getElementById(\"$selected_uczniowie_select_id\");
																	if ( selected_uczniowie.selectedIndex >= 0 )
																	{
																		for ( var i = 0; i < selected_uczniowie.options.length; i++ )
																		{
																			if ( selected_uczniowie.options[ i ].selected )
																			{
																				var option_value = selected_uczniowie.options[i].value;
																				available_option.push(selected_option.splice(selected_option_find_position(option_value),1)[0]);
																			}
																		}
																	}
																	reload_available_option();
																	reload_selected_option();
																};
																function selectAll()
																{
																	var selected_uczniowie=document.getElementById(\"$selected_uczniowie_select_id\");
																	for ( i=0; i<selected_uczniowie.options.length; i++)
																	{
																		selected_uczniowie.options[i].selected = 'true';
																	}
																};
																function available_option_find_position(id)
																{
																	for(i = 0; i < available_option.length; i++)
																	{
																		if(available_option[i][0] == id) return i;
																	}
																	return -1;
																}
																function selected_option_find_position(id)
																{
																	for(i = 0; i < selected_option.length; i++)
																	{
																		if(selected_option[i][0] == id) return i;
																	}
																	return -1;
																}
																reload_selected_option();
																reload_available_option();
															</script>";
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region create_select_field_for_oddzial
		private function create_select_field_for_oddzial($select_id)
		{
			$rettext = "<select onchange='document.getElementById(\"$select_id\").innerHTML = update_$select_id(this.value);document.getElementById(\"$select_id\").selectedIndex = 0; document.getElementById(\"$select_id\").dispatchEvent(new Event(\"change\"));'>";
			$this->update_select_field_from_oddzialy_js_script = "<script>";
			$this->update_select_field_from_oddzialy_js_script .= "function update_$select_id(idod){var opcje='';";
			$this->update_select_field_from_oddzialy_js_script .= "switch(idod){";
			$this->update_select_field_from_oddzialy_js_script .= "case '0':";
			$this->update_select_field_from_oddzialy_js_script .= "opcje=opcje+'<option value=\"0\" >wszystkie</option>';";
			foreach($this->page_obj->klasa->get_list() as $kval)
			{
				$this->update_select_field_from_oddzialy_js_script .= "opcje=opcje+'<option value=\"$kval[0]\" >$kval[2]</option>';";
			}
			$this->update_select_field_from_oddzialy_js_script .= "break;";
			//-----
			$rettext .= "<option value='0'>wszystkie</option>";
			//-----
			$lista_oddzialow = $this->page_obj->oddzialy->get_list();
			foreach($lista_oddzialow as $val)
			{
				$rettext .= "<option value='{$val[0]}'>{$val[1]}</option>";
				//-----
				$this->update_select_field_from_oddzialy_js_script .= "case '$val[0]':";
				foreach($this->page_obj->klasa->get_list_for_idod($val[0]) as $kval)
				{
					$this->update_select_field_from_oddzialy_js_script .= "opcje=opcje+'<option value=\"$kval[0]\" >$kval[2]</option>';";
				}
				$this->update_select_field_from_oddzialy_js_script .= "break;";
			}
			//-----
			$this->update_select_field_from_oddzialy_js_script .= "};";
			$this->update_select_field_from_oddzialy_js_script .= "return opcje;};</script>";
			//-----
			$rettext .= "</select>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region create_select_field_for_klasa
		private function create_select_field_for_klasa($select_id,$uczniowie_select_id,$selected_uczniowie_select_id)
		{
			$this->update_select_field_from_klasa_js_script = "";
			$this->update_select_field_from_klasa_js_script = "<script>";
			$this->update_select_field_from_klasa_js_script .= "function update_$uczniowie_select_id(idkl){var opcje='';available_option.splice(0,available_option.length);";
			$this->update_select_field_from_klasa_js_script .= "switch(idkl){";
			$this->update_select_field_from_klasa_js_script .= "case '0':";
			
			//for all uczniowie
			foreach($this->page_obj->uczniowie->get_list() as $val)
			{
				$this->update_select_field_from_klasa_js_script .= "if(selected_option_find_position({$val[0]}) == -1) available_option.push([{$val[0]},'{$val[1]}']);\n";
			}
			$this->update_select_field_from_klasa_js_script .= "break;";
			
			//for selected klasa
			foreach($this->page_obj->klasa->get_list() as $val)
			{
				$this->update_select_field_from_klasa_js_script .= "case '{$val[0]}':";
				foreach($this->page_obj->uczniowie->get_list_for_klasa($val[0]) as $val2)
				{
					$this->update_select_field_from_klasa_js_script .= "if(selected_option_find_position({$val2[0]}) == -1) available_option.push([{$val2[0]},'{$val2[1]}']);\n";
				}
				$this->update_select_field_from_klasa_js_script .= "break;";
			}

			$this->update_select_field_from_klasa_js_script .= "};";
			$this->update_select_field_from_klasa_js_script .= "reload_selected_option();
			reload_available_option();};</script>";
			//--------------------
			$rettext = "<select id='$select_id' onchange='update_$uczniowie_select_id(this.value);'>";
			$rettext .= "<option value='0'>wszystkie</option>";
			$lista_oddzialow = $this->page_obj->klasa->get_list();
			foreach($lista_oddzialow as $val)
			{
				$rettext .= "<option value='{$val[0]}'>{$val[2]}</option>";
			}
			$rettext .= "<select>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region formularz_zestawienie
		private function formularz_zestawienie($od,$do,$store_od_do)
		{
			$rettext = "";
			//--------------------
			$rettext .= "<button class='button_add' title='Drukuj' type='button' onclick='var printWindow = window.open(\"".get_class($this).",raw,zestawienie_drukuj\",\"chaild\");printWindow.print();printWindow.onafterprint = function(){printWindow.close()};return false;'>Drukuj</button>&#160;";
			//--------------------
			if($store_od_do == "do_store")
			{
				if(isset($od))
					$_SESSION['oplaty_zestawienie_od'] = $od;
				if(isset($do))
					$_SESSION['oplaty_zestawienie_do'] = $do;
			}
			if(isset($_SESSION['oplaty_zestawienie_od']))
				$od = $_SESSION['oplaty_zestawienie_od'];
			if(isset($_SESSION['oplaty_zestawienie_do']))
				$do = $_SESSION['oplaty_zestawienie_do'];
			if($od == "") $od = date("Y-m-d");
			if($do == "") $do = date("Y-m-d");
			//--------------------
			$rettext .= "<style>
								div.wiersz{float:left;clear:left;}
								div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
								div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
							</style>";
			$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},formularz_zestawienie'>
								<div style='overflow:hidden;'>
									<div class='wiersz'>Od: <input type='date' name='od' value='$od' class='date_field'/> do: <input type='date' name='do' value='$do' class='date_field'/> <input type='submit' class='button_raport' name='' title='Generuj zestawienie' value='Generuj zestawienie' style='font-size:16px;'/></div>
									<input type='hidden' name='store_od_do' value='do_store' />
								</div>
							</form>";
			//--------------------
			$rettext .= "<div style='overflow:hidden;'>";
			$rettext .= "<br />";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "<tr>
								<td style='width:30px;'>lp.</td>
								<td style='height:30px;'>Data</td>
								<td>nazwa</td>
								<td>kwota</td>
								<td>nazwa rabatu</td>
								<td>kwota rabatu</td>
								<td>imie, nazwisko</td>
							</tr>";
			$wynik = $this->page_obj->database_obj->get_data("select uo.iduop,o.data,o.nazwa,o.kwota,uo.rabat_nazwa,uo.rabat_kwota,u.imie_uczniowie,u.nazwisko_uczniowie from oplaty o, uczniowie_oplaty uo, uczniowie u where o.idop = uo.idop and u.idu = uo.idu and o.usuniety = 'nie' and uo.usuniety = 'nie' and u.usuniety = 'nie' and data >= '$od' and data <= '$do' order by data;");
			$suma = 0;
			$suma_rabat = 0;
			if($wynik)
			{
				$lp = 1;
				while(list($iduop,$data,$nazwa,$kwota,$rabat_nazwa,$rabat_kwota,$imie_uczniowie,$nazwisko_uczniowie)=$wynik->fetch_row())
				{
					$rettext .= "<tr id='wiersz$iduop' onmouseover=\"setopticalwhite50('wiersz$iduop')\" onmouseout=\"setoptical0('wiersz$iduop')\">
										<td style='text-align:right;padding-right:10px;'>$lp.</td>
										<td style='height:30px;'>".substr($data,0,10)."</td>
										<td>$nazwa</td>
										<td>$kwota</td>
										<td>$rabat_nazwa</td>
										<td>$rabat_kwota</td>
										<td>$imie_uczniowie $nazwisko_uczniowie</td>
									</tr>";
					$suma += $kwota;
					$suma_rabat += $rabat_kwota;
					$lp++;
				}
			}
			$rettext .= "<tr><td><br /></td></tr>";
			$rettext .= "<tr><td><td></td></td><td></td><td>Suma opłat: $suma </td><td></td><td> suma rabatu: $suma_rabat </td><td> kwota wpływu: ".($suma - $suma_rabat)."</td></tr>";
			$rettext .= "</table>";
			$rettext .= "</div>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region zestawienie_drukuj
		private function zestawienie_drukuj()
		{
			$rettext = "";
			//--------------------
			if(isset($_SESSION['oplaty_zestawienie_od']))
				$od = $_SESSION['oplaty_zestawienie_od'];
			if(isset($_SESSION['oplaty_zestawienie_do']))
				$do = $_SESSION['oplaty_zestawienie_do'];
			if($od == "") $od = date("Y-m-d");
			if($do == "") $do = date("Y-m-d");
			//--------------------
			$rettext .= "<p>Od: $od do: $do</p>";
			$rettext .= "<table style='width:100%;font-size:16px;' cellspacing='0'>";
			$rettext .= "<tr>
								<td style='width:30px;'>lp.</td>
								<td style='height:30px;'>Data</td>
								<td>nazwa</td>
								<td>kwota</td>
								<td>nazwa rabatu</td>
								<td>kwota rabatu</td>
								<td>imie, nazwisko</td>
							</tr>";
			$wynik = $this->page_obj->database_obj->get_data("select uo.iduop,o.data,o.nazwa,o.kwota,uo.rabat_nazwa,uo.rabat_kwota,u.imie_uczniowie,u.nazwisko_uczniowie from oplaty o, uczniowie_oplaty uo, uczniowie u where o.idop = uo.idop and u.idu = uo.idu and o.usuniety = 'nie' and uo.usuniety = 'nie' and u.usuniety = 'nie' and data >= '$od' and data <= '$do' order by data;");
			$suma = 0;
			$suma_rabat = 0;
			if($wynik)
			{
				$lp = 1;
				while(list($iduop,$data,$nazwa,$kwota,$rabat_nazwa,$rabat_kwota,$imie_uczniowie,$nazwisko_uczniowie)=$wynik->fetch_row())
				{
					$rettext .= "<tr id='wiersz$iduop' onmouseover=\"setopticalwhite50('wiersz$iduop')\" onmouseout=\"setoptical0('wiersz$iduop')\">
										<td style='text-align:right;padding-right:10px;'>$lp.</td>
										<td style='height:30px;'>".substr($data,0,10)."</td>
										<td>$nazwa</td>
										<td>$kwota</td>
										<td>$rabat_nazwa</td>
										<td>$rabat_kwota</td>
										<td>$imie_uczniowie $nazwisko_uczniowie</td>
									</tr>";
					$suma += $kwota;
					$suma_rabat += $rabat_kwota;
					$lp++;
				}
			}
			$rettext .= "<tr><td><br /></td></tr>";
			$rettext .= "<tr><td></td><td></td><td></td><td>Suma opłat: $suma </td><td></td><td> suma rabatu: $suma_rabat </td><td> kwota wpływu: ".($suma - $suma_rabat)."</td></tr>";
			$rettext .= "</table>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region formularz_podsumowanie_miesiaca
		private function formularz_podsumowanie_miesiaca($rok,$miesiac,$store)
		{
			$rettext = "";
			//--------------------
			$rettext .= "<button class='button_add' title='Drukuj' type='button' onclick='var printWindow = window.open(\"".get_class($this).",raw,podsumowanie_miesiaca_drukuj\",\"chaild\");printWindow.print();printWindow.onafterprint = function(){printWindow.close()};return false;'>Drukuj</button>&#160;";
			//--------------------
			if( $store )
			{
				if( isset($rok) )
					$_SESSION['podsumowanie_miesiaca_rok'] = $rok;
				if( isset($miesiac) )
					$_SESSION['podsumowanie_miesiaca_miesiac'] = $miesiac;
			}
			//--------------------
			if(isset($_SESSION['podsumowanie_miesiaca_rok']))
			{
				$rok = $_SESSION['podsumowanie_miesiaca_rok'];
			}
			if(isset($_SESSION['podsumowanie_miesiaca_miesiac']))
			{
				$miesiac = $_SESSION['podsumowanie_miesiaca_miesiac'];
			}
			if ( $rok == "" ) 
			{
				$rok = date("Y");
			}
			if ( $miesiac == "" )
			{
				$miesiac = date("m");
			}
			if($miesiac == 12)
			{
				$rok2 = $rok + 1;
				$miesiac2 = 1;
			}
			else
			{
				$rok2 = $rok;
				$miesiac2 = $miesiac + 1;
			}
			//--------------------
			$rettext .= "<style>
								div.wiersz{float:left;clear:left;}
								div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
								div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
							</style>";
			$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},formularz_podsumowanie_miesiaca'>
								<div style='overflow:hidden;'>
									<div class='wiersz'>
										Rok: 
											<select name='rok' >
												<option value = '2021' ".($rok == 2021 ? "selected='selected'" : "").">2021</option>
												<option value = '2022' ".($rok == 2022 ? "selected='selected'" : "").">2022</option>
												<option value = '2023' ".($rok == 2023 ? "selected='selected'" : "").">2023</option>
												<option value = '2024' ".($rok == 2024 ? "selected='selected'" : "").">2024</option>
												<option value = '2025' ".($rok == 2025 ? "selected='selected'" : "").">2025</option>
												<option value = '2026' ".($rok == 2026 ? "selected='selected'" : "").">2026</option>
												<option value = '2027' ".($rok == 2027 ? "selected='selected'" : "").">2027</option>
												<option value = '2028' ".($rok == 2028 ? "selected='selected'" : "").">2028</option>
												<option value = '2029' ".($rok == 2029 ? "selected='selected'" : "").">2029</option>
												<option value = '2030' ".($rok == 2030 ? "selected='selected'" : "").">2030</option>
												<option value = '2031' ".($rok == 2031 ? "selected='selected'" : "").">2031</option>
												<option value = '2032' ".($rok == 2032 ? "selected='selected'" : "").">2032</option>
												<option value = '2033' ".($rok == 2033 ? "selected='selected'" : "").">2033</option>
												<option value = '2034' ".($rok == 2034 ? "selected='selected'" : "").">2034</option>
												<option value = '2035' ".($rok == 2035 ? "selected='selected'" : "").">2035</option>
												<option value = '2036' ".($rok == 2036 ? "selected='selected'" : "").">2036</option>
												<option value = '2037' ".($rok == 2037 ? "selected='selected'" : "").">2037</option>
												<option value = '2038' ".($rok == 2038 ? "selected='selected'" : "").">2038</option>
												<option value = '2039' ".($rok == 2039 ? "selected='selected'" : "").">2039</option>
												<option value = '2040' ".($rok == 2040 ? "selected='selected'" : "").">2040</option>
												<option value = '2041' ".($rok == 2041 ? "selected='selected'" : "").">2041</option>
												<option value = '2042' ".($rok == 2042 ? "selected='selected'" : "").">2042</option>
												<option value = '2043' ".($rok == 2043 ? "selected='selected'" : "").">2043</option>
												<option value = '2044' ".($rok == 2044 ? "selected='selected'" : "").">2044</option>
												<option value = '2045' ".($rok == 2045 ? "selected='selected'" : "").">2045</option>
												<option value = '2046' ".($rok == 2046 ? "selected='selected'" : "").">2046</option>
												<option value = '2047' ".($rok == 2047 ? "selected='selected'" : "").">2047</option>
												<option value = '2048' ".($rok == 2048 ? "selected='selected'" : "").">2048</option>
												<option value = '2049' ".($rok == 2049 ? "selected='selected'" : "").">2049</option>
												<option value = '2050' ".($rok == 2050 ? "selected='selected'" : "").">2050</option>
											</select>
										miesiąć: 
											<select name='miesiac' >
											<option value = '1' ".($miesiac == 1 ? "selected='selected'" : "").">styczeń</option>
											<option value = '2' ".($miesiac == 2 ? "selected='selected'" : "").">luty</option>
											<option value = '3' ".($miesiac == 3 ? "selected='selected'" : "").">marzec</option>
											<option value = '4' ".($miesiac == 4 ? "selected='selected'" : "").">kwiecień</option>
											<option value = '5' ".($miesiac == 5 ? "selected='selected'" : "").">maj</option>
											<option value = '6' ".($miesiac == 6 ? "selected='selected'" : "").">czerwiec</option>
											<option value = '7' ".($miesiac == 7 ? "selected='selected'" : "").">lipiec</option>
											<option value = '8' ".($miesiac == 8 ? "selected='selected'" : "").">sierpień</option>
											<option value = '9' ".($miesiac == 9 ? "selected='selected'" : "").">wrzesień</option>
											<option value = '10' ".($miesiac == 10 ? "selected='selected'" : "").">październik</option>
											<option value = '11' ".($miesiac == 11 ? "selected='selected'" : "").">listopad</option>
											<option value = '12' ".($miesiac == 12 ? "selected='selected'" : "").">grudzień</option>
											</select> 
										<input type='submit' class='button_raport' name='' title='Generuj zestawienie' value='Generuj zestawienie' style='font-size:16px;'/></div>
									<input type='hidden' name='store' value='true' />
								</div>
							</form>";
			//--------------------
			//pobrać oddziały
			//dla każdego oddziału zrobić sumę dla każdego typu
			$suma_rabat = 0;
			$suma_calkowita = 0;
			$rettext .= "<br /><br /><b>PODSUMOWANIE OPŁAT W MIESIĄCU: " . $this->month_to_pl_string($miesiac) . " " . $rok . "</b><br /><br />";
			$rettext .= "<table cellpadding='5' cellspacing='0'>";
			foreach($this->page_obj->oddzialy->get_list() as $oddzialy_array) // [idod, nazwa]
			{
				$suma_razem = 0;
				$rettext .= "<tr><td colspan = '2' ><b>" . $oddzialy_array[1] . "</b></td></tr>";
				foreach($this->page_obj->typy_oplat->get_list() as $typy_oplat_array) // [idto,nazwa]
				{
					//$rettext .= "select * from oplaty o, typy_oplat t, uczniowie_oplaty uo, uczniowie u, klasa k where o.idto = t.idto and uo.idop = o.idop and uo.idu = u.idu and k.idkl = u.idkl and o.usuniety = 'nie' and t.usuniety = 'nie' and uo.usuniety = 'nie' and u.usuniety = 'nie' and k.usuniety = 'nie' and k.idod = {$oddzialy_array[0]} and t.idto = {$typy_oplat_array[0]} and o.data >= '".$rok."-".$miesiac."-01 00:00:00' and o.data < '".$rok2."-".$miesiac2."-01 00:00:00';" . "<br />";
					$wynik = $this->page_obj->database_obj->get_data("select sum(o.kwota), sum(uo.rabat_kwota) from oplaty o, typy_oplat t, uczniowie_oplaty uo, uczniowie u, klasa k where o.idto = t.idto and uo.idop = o.idop and uo.idu = u.idu and k.idkl = u.idkl and o.usuniety = 'nie' and t.usuniety = 'nie' and uo.usuniety = 'nie' and u.usuniety = 'nie' and k.usuniety = 'nie' and k.idod = {$oddzialy_array[0]} and t.idto = {$typy_oplat_array[0]} and o.data >= '".$rok."-".$miesiac."-01 00:00:00' and o.data < '".$rok2."-".$miesiac2."-01 00:00:00';");
					if($wynik)
					{
						list($suma,$rabat) = $wynik->fetch_row();
						$rettext .= "<tr>
											<td style='border-bottom:1px solid gray;'>&nbsp;&nbsp;&nbsp;&nbsp;" . $typy_oplat_array[1] . ":</td>
											<td style='border-bottom:1px solid gray;border-left:1px solid gray;'> " .($suma - $rabat) . "</td>
										</tr>";
						$suma_razem += ($suma - $rabat);
					}
				}
				$rettext .= "<tr><td collspan = '2' >razem:</td><td style='border-left:1px solid gray;'>$suma_razem</td></tr>";
				$rettext .= "<tr><td collspan = '2' ></td><td><br /></td></tr>";
				$suma_calkowita += $suma_razem;
			}

			$rettext .= "<tr><td collspan = '2' >W sumie:</td><td style='border-left:1px solid gray;'>$suma_calkowita</td></tr>";

			$rettext .= "</table>";
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region podsumowanie_miesiaca_drukuj
		private function podsumowanie_miesiaca_drukuj()
		{
			$rettext = "";
			//--------------------
			if(isset($_SESSION['podsumowanie_miesiaca_rok']))
			{
				$rok = $_SESSION['podsumowanie_miesiaca_rok'];
			}
			if(isset($_SESSION['podsumowanie_miesiaca_miesiac']))
			{
				$miesiac = $_SESSION['podsumowanie_miesiaca_miesiac'];
			}
			if ( !isset($rok)  || ($rok == "") ) 
			{
				$rok = date("Y");
			}
			if ( !isset($miesiac)  ||  ($miesiac == "") )
			{
				$miesiac = date("m");
			}
			if($miesiac == 12)
			{
				$rok2 = $rok + 1;
				$miesiac2 = 1;
			}
			else
			{
				$rok2 = $rok;
				$miesiac2 = $miesiac + 1;
			}
			//--------------------
			//pobrać oddziały
			//dla każdego oddziału zrobić sumę dla każdego typu
			$suma_rabat = 0;
			$suma_calkowita = 0;
			$rettext .= "<br /><br /><b>PODSUMOWANIE OPŁAT W MIESIĄCU: " . $this->month_to_pl_string($miesiac) . " " . $rok . "</b><br /><br />";
			$rettext .= "<table cellpadding='5' cellspacing='0'>";
			foreach($this->page_obj->oddzialy->get_list() as $oddzialy_array) // [idod, nazwa]
			{
				$suma_razem = 0;
				$rettext .= "<tr><td colspan = '2' ><b>" . $oddzialy_array[1] . "</b></td></tr>";
				foreach($this->page_obj->typy_oplat->get_list() as $typy_oplat_array) // [idto,nazwa]
				{
					//$rettext .= "select * from oplaty o, typy_oplat t, uczniowie_oplaty uo, uczniowie u, klasa k where o.idto = t.idto and uo.idop = o.idop and uo.idu = u.idu and k.idkl = u.idkl and o.usuniety = 'nie' and t.usuniety = 'nie' and uo.usuniety = 'nie' and u.usuniety = 'nie' and k.usuniety = 'nie' and k.idod = {$oddzialy_array[0]} and t.idto = {$typy_oplat_array[0]} and o.data >= '".$rok."-".$miesiac."-01 00:00:00' and o.data < '".$rok2."-".$miesiac2."-01 00:00:00';" . "<br />";
					$wynik = $this->page_obj->database_obj->get_data("select sum(o.kwota), sum(uo.rabat_kwota) from oplaty o, typy_oplat t, uczniowie_oplaty uo, uczniowie u, klasa k where o.idto = t.idto and uo.idop = o.idop and uo.idu = u.idu and k.idkl = u.idkl and o.usuniety = 'nie' and t.usuniety = 'nie' and uo.usuniety = 'nie' and u.usuniety = 'nie' and k.usuniety = 'nie' and k.idod = {$oddzialy_array[0]} and t.idto = {$typy_oplat_array[0]} and o.data >= '".$rok."-".$miesiac."-01 00:00:00' and o.data < '".$rok2."-".$miesiac2."-01 00:00:00';");
					if($wynik)
					{
						list($suma,$rabat) = $wynik->fetch_row();
						$rettext .= "<tr>
											<td style='border-bottom:1px solid gray;'>&nbsp;&nbsp;&nbsp;&nbsp;" . $typy_oplat_array[1] . ":</td>
											<td style='border-bottom:1px solid gray;border-left:1px solid gray;'> " .($suma - $rabat) . "</td>
										</tr>";
						$suma_razem += ($suma - $rabat);
					}
				}
				$rettext .= "<tr><td collspan = '2' >razem:</td><td style='border-left:1px solid gray;'>$suma_razem</td></tr>";
				$rettext .= "<tr><td collspan = '2' ></td><td><br /></td></tr>";
				$suma_calkowita += $suma_razem;
			}

			$rettext .= "<tr><td collspan = '2' >W sumie:</td><td style='border-left:1px solid gray;'>$suma_calkowita</td></tr>";

			$rettext .= "</table>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		private function formularz_podsumowanie_oddzial($rok, $miesiac, $store, $idod)
		{
			$rettext = "";
			//--------------------
			$rettext .= "<button class='button_add' title='Drukuj' type='button' onclick='var printWindow = window.open(\"".get_class($this).",raw,podsumowanie_oddzial_drukuj\",\"chaild\");printWindow.print();printWindow.onafterprint = function(){printWindow.close()};return false;'>Drukuj</button>&#160;";
			//--------------------
			if( $store )
			{
				if( isset($rok) )
					$_SESSION['podsumowanie_oddzial_rok'] = $rok;
				if( isset($miesiac) )
					$_SESSION['podsumowanie_oddzial_miesiac'] = $miesiac;
				if( isset($idod) )
					$_SESSION['podsumowanie_oddzial_idod'] = $idod;
			}
			//--------------------
			if(isset($_SESSION['podsumowanie_oddzial_rok']))
			{
				$rok = $_SESSION['podsumowanie_oddzial_rok'];
			}
			if(isset($_SESSION['podsumowanie_oddzial_miesiac']))
			{
				$miesiac = $_SESSION['podsumowanie_oddzial_miesiac'];
			}
			if(isset($_SESSION['podsumowanie_oddzial_idod']))
			{
				$idod = $_SESSION['podsumowanie_oddzial_idod'];
			}
			if ( $rok == "" ) 
			{
				$rok = date("Y");
			}
			if ( $miesiac == "" )
			{
				$miesiac = date("m");
			}
			if($miesiac == 12)
			{
				$rok2 = $rok + 1;
				$miesiac2 = 1;
			}
			else
			{
				$rok2 = $rok;
				$miesiac2 = $miesiac + 1;
			}
			//--------------------
			$rettext .= "<style>
								div.wiersz{float:left;clear:left;}
								div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
								div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
							</style>";
			$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},formularz_podsumowanie_oddzial'>
								<div style='overflow:hidden;'>
									<div class='wiersz'>
										Rok: 
											<select name='rok' >
												<option value = '2021' ".($rok == 2021 ? "selected='selected'" : "").">2021</option>
												<option value = '2022' ".($rok == 2022 ? "selected='selected'" : "").">2022</option>
												<option value = '2023' ".($rok == 2023 ? "selected='selected'" : "").">2023</option>
												<option value = '2024' ".($rok == 2024 ? "selected='selected'" : "").">2024</option>
												<option value = '2025' ".($rok == 2025 ? "selected='selected'" : "").">2025</option>
												<option value = '2026' ".($rok == 2026 ? "selected='selected'" : "").">2026</option>
												<option value = '2027' ".($rok == 2027 ? "selected='selected'" : "").">2027</option>
												<option value = '2028' ".($rok == 2028 ? "selected='selected'" : "").">2028</option>
												<option value = '2029' ".($rok == 2029 ? "selected='selected'" : "").">2029</option>
												<option value = '2030' ".($rok == 2030 ? "selected='selected'" : "").">2030</option>
												<option value = '2031' ".($rok == 2031 ? "selected='selected'" : "").">2031</option>
												<option value = '2032' ".($rok == 2032 ? "selected='selected'" : "").">2032</option>
												<option value = '2033' ".($rok == 2033 ? "selected='selected'" : "").">2033</option>
												<option value = '2034' ".($rok == 2034 ? "selected='selected'" : "").">2034</option>
												<option value = '2035' ".($rok == 2035 ? "selected='selected'" : "").">2035</option>
												<option value = '2036' ".($rok == 2036 ? "selected='selected'" : "").">2036</option>
												<option value = '2037' ".($rok == 2037 ? "selected='selected'" : "").">2037</option>
												<option value = '2038' ".($rok == 2038 ? "selected='selected'" : "").">2038</option>
												<option value = '2039' ".($rok == 2039 ? "selected='selected'" : "").">2039</option>
												<option value = '2040' ".($rok == 2040 ? "selected='selected'" : "").">2040</option>
												<option value = '2041' ".($rok == 2041 ? "selected='selected'" : "").">2041</option>
												<option value = '2042' ".($rok == 2042 ? "selected='selected'" : "").">2042</option>
												<option value = '2043' ".($rok == 2043 ? "selected='selected'" : "").">2043</option>
												<option value = '2044' ".($rok == 2044 ? "selected='selected'" : "").">2044</option>
												<option value = '2045' ".($rok == 2045 ? "selected='selected'" : "").">2045</option>
												<option value = '2046' ".($rok == 2046 ? "selected='selected'" : "").">2046</option>
												<option value = '2047' ".($rok == 2047 ? "selected='selected'" : "").">2047</option>
												<option value = '2048' ".($rok == 2048 ? "selected='selected'" : "").">2048</option>
												<option value = '2049' ".($rok == 2049 ? "selected='selected'" : "").">2049</option>
												<option value = '2050' ".($rok == 2050 ? "selected='selected'" : "").">2050</option>
											</select>
										miesiąć: 
											<select name='miesiac' >
											<option value = '1' ".($miesiac == 1 ? "selected='selected'" : "").">styczeń</option>
											<option value = '2' ".($miesiac == 2 ? "selected='selected'" : "").">luty</option>
											<option value = '3' ".($miesiac == 3 ? "selected='selected'" : "").">marzec</option>
											<option value = '4' ".($miesiac == 4 ? "selected='selected'" : "").">kwiecień</option>
											<option value = '5' ".($miesiac == 5 ? "selected='selected'" : "").">maj</option>
											<option value = '6' ".($miesiac == 6 ? "selected='selected'" : "").">czerwiec</option>
											<option value = '7' ".($miesiac == 7 ? "selected='selected'" : "").">lipiec</option>
											<option value = '8' ".($miesiac == 8 ? "selected='selected'" : "").">sierpień</option>
											<option value = '9' ".($miesiac == 9 ? "selected='selected'" : "").">wrzesień</option>
											<option value = '10' ".($miesiac == 10 ? "selected='selected'" : "").">październik</option>
											<option value = '11' ".($miesiac == 11 ? "selected='selected'" : "").">listopad</option>
											<option value = '12' ".($miesiac == 12 ? "selected='selected'" : "").">grudzień</option>
											</select>
											<select name='idod' >
												{$this->page_obj->oddzialy->creat_options($idod)}
											</select>
										<input type='submit' class='button_raport' name='' title='Generuj zestawienie' value='Generuj zestawienie' style='font-size:16px;'/></div>
									<input type='hidden' name='store' value='true' />
								</div>
							</form>";
			//--------------------
			// pobrać typy opłat
			// dla każdej klasy
				//dla każdego ucznia
					//dla każdego typu oplaty
						//pobrać opłaty
						//--------------------
			$rettext .= "<br />";
			$rettext .= "<b>" . $this->page_obj->oddzialy->get_name($idod) . " - " . $this->month_to_pl_string($miesiac) . " $rok</b><br /><hr />";
			$rettext .= "<table border = '0' cellspacing = '0' cellpadding = '8'>";
			
			$suma_all = array();
			$typy_oplat = $this->page_obj->typy_oplat->get_list();
			foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
			{
				$suma_all[$key] = 0;
			}

			foreach($this->page_obj->klasa->get_list_for_idod($idod) as $klasy_array) //[idkl, idod, nazwa]
			{
				$rettext .= "<tr><td colspan='6'><b>" . $klasy_array[2] . "</b></td></tr>";

				$rettext .= "	<tr>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				
				foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
				{
					$typy_oplat[$key][2] = 0;
					$rettext .= "		<td style='border-bottom:1px solid gray;border-left:1px solid gray;'>{$typy_oplat_array[1]}</td>";
				}
				$rettext .= "	</tr>";

				$suma_klasa = 0;
				foreach($this->page_obj->uczniowie->get_list_for_klasa($klasy_array[0]) as $uczniowie_array) //[idu, imie_nazwisko]
				{
					$rettext .= "<tr>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'>{$uczniowie_array[1]} &#160;</td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";

					foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
					{
						$rettext .= "<td style='border-bottom:1px solid gray;border-left:1px solid gray;vertical-align:bottom;width:200px;'>";
						$rettext .= "<table cellspacing='0' style='width:100%;'>";
						$suma = 0;
						foreach($this->page_obj->uczniowie_oplaty->get_liste_oplat_dla_ucznia_i_typu_oplaty($uczniowie_array[0],$typy_oplat_array[0], $rok."-".$miesiac."-01 00:00:00", $rok2."-".$miesiac2."-01 00:00:00") as $oplaty_array) //[iduop, idop, rabat_kwota, rabat_nazwa]
						{
							$kwota = $this->page_obj->oplaty->get_kwota($oplaty_array[1]);
							$suma += $kwota - $oplaty_array[2];
							if($oplaty_array[2] != 0)
							{
								$rettext .= "<tr><td>" . $this->page_obj->oplaty->get_name($oplaty_array[1]) . "</td><td style='padding-left:15px;text-align:right;width:100px;'>$kwota - {$oplaty_array[2]}</td></tr>";
							}
							else
							{
								$rettext .= "<tr><td>" . $this->page_obj->oplaty->get_name($oplaty_array[1]) . "</td><td style='padding-left:15px;text-align:right;width:100px;'>$kwota</td></tr>";
							}
						}
						if($suma != 0)
						{
							$rettext .= "<tr><td>Suma:</td><td style='text-align:right;padding-left:15px;border-top:1px solid gray;width:60px;'>".(number_format($suma, 2))."</td></tr>";
						}
						$rettext .= "</table>";
						$rettext .= "</td>";
						$typy_oplat[$key][2] += $suma;
					}
					$rettext .= "</tr>";
				}
				$rettext .= "<tr>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
					{
						$rettext .= "	<td style='border-bottom:1px solid gray;border-left:1px solid gray;padding-left:15px;text-align:right;'>{$typy_oplat[$key][2]}</td>";
						$suma_all[$key] += $typy_oplat[$key][2];
					}
					$rettext .= "</tr>";

				$rettext .= "<tr><td colspan='6'>&#160;</td></tr>";
			}

			$rettext .= "<tr><td colspan='6'><b>Podsumowanie:</b></td></tr>";

				$rettext .= "	<tr>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				
				foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
				{
					$typy_oplat[$key][2] = 0;
					$rettext .= "		<td style='border-bottom:1px solid gray;border-left:1px solid gray;'>{$typy_oplat_array[1]}</td>";
				}
				$rettext .= "	</tr>";

				$rettext .= "<tr>";
				$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
				$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
				$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
				foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
				{
					$rettext .= "	<td style='border-bottom:1px solid gray;border-left:1px solid gray;padding-left:15px;text-align:right;'>{$suma_all[$key]}</td>";
				}
				$rettext .= "</tr>";

			$rettext .= "</table>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		#region podsumowanie_oddzial_drukuj
		private function podsumowanie_oddzial_drukuj()
		{
			$rettext = "";
			//--------------------
			if(isset($_SESSION['podsumowanie_oddzial_rok']))
			{
				$rok = $_SESSION['podsumowanie_oddzial_rok'];
			}
			if(isset($_SESSION['podsumowanie_oddzial_miesiac']))
			{
				$miesiac = $_SESSION['podsumowanie_oddzial_miesiac'];
			}
			if(isset($_SESSION['podsumowanie_oddzial_idod']))
			{
				$idod = $_SESSION['podsumowanie_oddzial_idod'];
			}
			if ( ( !isset($rok) ) || ( $rok == "" ) ) 
			{
				$rok = date("Y");
			}
			if (  ( !isset($miesiac) ) || ( $miesiac == "" ) )
			{
				$miesiac = date("m");
			}
			if($miesiac == 12)
			{
				$rok2 = $rok + 1;
				$miesiac2 = 1;
			}
			else
			{
				$rok2 = $rok;
				$miesiac2 = $miesiac + 1;
			}
			if ( ( !isset($idod) ) || ( $idod == "" ) ) 
			{
				$idod = 1;
			}
			//--------------------
			$rettext .= "<b>" . $this->page_obj->oddzialy->get_name($idod) . " - " . $this->month_to_pl_string($miesiac) . " $rok</b><br /><hr />";
			$rettext .= "<table border = '0' cellspacing = '0' cellpadding = '8'>";
			
			$suma_all = array();
			$typy_oplat = $this->page_obj->typy_oplat->get_list();
			foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
			{
				$suma_all[$key] = 0;
			}

			foreach($this->page_obj->klasa->get_list_for_idod($idod) as $klasy_array) //[idkl, idod, nazwa]
			{
				$rettext .= "<tr><td colspan='6'><b>" . $klasy_array[2] . "</b></td></tr>";

				$rettext .= "	<tr>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				
				foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
				{
					$typy_oplat[$key][2] = 0;
					$rettext .= "		<td style='border-bottom:1px solid gray;border-left:1px solid gray;'>{$typy_oplat_array[1]}</td>";
				}
				$rettext .= "	</tr>";

				$suma_klasa = 0;
				foreach($this->page_obj->uczniowie->get_list_for_klasa($klasy_array[0]) as $uczniowie_array) //[idu, imie_nazwisko]
				{
					$rettext .= "<tr>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'>{$uczniowie_array[1]} &#160;</td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";

					foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
					{
						$rettext .= "<td style='border-bottom:1px solid gray;border-left:1px solid gray;vertical-align:bottom;width:200px;'>";
						$rettext .= "<table cellspacing='0' style='width:100%;'>";
						$suma = 0;
						foreach($this->page_obj->uczniowie_oplaty->get_liste_oplat_dla_ucznia_i_typu_oplaty($uczniowie_array[0],$typy_oplat_array[0], $rok."-".$miesiac."-01 00:00:00", $rok2."-".$miesiac2."-01 00:00:00") as $oplaty_array) //[iduop, idop, rabat_kwota, rabat_nazwa]
						{
							$kwota = $this->page_obj->oplaty->get_kwota($oplaty_array[1]);
							$suma += $kwota - $oplaty_array[2];
							if($oplaty_array[2] != 0)
							{
								$rettext .= "<tr><td>" . $this->page_obj->oplaty->get_name($oplaty_array[1]) . "</td><td style='padding-left:15px;text-align:right;width:100px;'>$kwota - {$oplaty_array[2]}</td></tr>";
							}
							else
							{
								$rettext .= "<tr><td>" . $this->page_obj->oplaty->get_name($oplaty_array[1]) . "</td><td style='padding-left:15px;text-align:right;width:100px;'>$kwota</td></tr>";
							}
						}
						if($suma != 0)
						{
							$rettext .= "<tr><td>Suma:</td><td style='text-align:right;padding-left:15px;border-top:1px solid gray;width:60px;'>".(number_format($suma, 2))."</td></tr>";
						}
						$rettext .= "</table>";
						$rettext .= "</td>";
						$typy_oplat[$key][2] += $suma;
					}
					$rettext .= "</tr>";
				}
				$rettext .= "<tr>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
					{
						$rettext .= "	<td style='border-bottom:1px solid gray;border-left:1px solid gray;padding-left:15px;text-align:right;'>{$typy_oplat[$key][2]}</td>";
						$suma_all[$key] += $typy_oplat[$key][2];
					}
					$rettext .= "</tr>";

				$rettext .= "<tr><td colspan='6'>&#160;</td></tr>";
			}

			$rettext .= "<tr><td colspan='6'><b>Podsumowanie:</b></td></tr>";

				$rettext .= "	<tr>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				$rettext .= "		<td style='border-bottom:1px solid gray;'>&#160;</td>";
				
				foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
				{
					$typy_oplat[$key][2] = 0;
					$rettext .= "		<td style='border-bottom:1px solid gray;border-left:1px solid gray;'>{$typy_oplat_array[1]}</td>";
				}
				$rettext .= "	</tr>";
			$rettext .= "<tr>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					$rettext .= "	<td style='border-bottom:1px solid gray;'></td>";
					foreach($typy_oplat as $key => $typy_oplat_array) //[idto,nazwa]
					{
						$rettext .= "	<td style='border-bottom:1px solid gray;border-left:1px solid gray;padding-left:15px;text-align:right;'>{$suma_all[$key]}</td>";
					}
					$rettext .= "</tr>";

			$rettext .= "</table>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region month_to_pl_string
		private function month_to_pl_string($mc)
		{
			switch($mc)
			{
				case 1: return "styczeń";
				case 2: return "luty";
				case 3: return "marzec";
				case 4: return "kwiecień";
				case 5: return "maj";
				case 6: return "czerwiec";
				case 7: return "lipiec";
				case 8: return "sierpień";
				case 9: return "wrzesień";
				case 10: return "październik";
				case 11: return "listopad";
				case 12: return "grudzień";
				default: return "";
			}
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region definicjabazy
		private function definicjabazy()
		{
			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy=get_class($this);
			$pola=array();
			
			//definicja tablicy
			$nazwa="idop";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="idto";
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
			
			$nazwa="nazwa";
			$pola[$nazwa][0]="varchar(150)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="kwota";
			$pola[$nazwa][0]="decimal(8,2)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="data";
			$pola[$nazwa][0]="timestamp";
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
	}
}//end if
else
	die("Class exists: ".__FILE__);
?>