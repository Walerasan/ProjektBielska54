<?php
if(!class_exists('wyciagi'))
{
	class wyciagi
	{
		var $page_obj;
		var $katalog;//katalog do uploud dokumentów do przetwarzania
		var $javascript_select_uczniowie;
		var $update_select_field_from_oddzialy_js_script;
		var $update_select_field_from_klasa_js_script;
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj = $page_obj;
			$this->definicjabazy();
			$this->katalog = $page_obj->create_directory("./media/filehtml",debug_backtrace());
			$this->javascript_select_uczniowie = "";
			$this->update_select_field_from_oddzialy_js_script = "";
			$this->update_select_field_from_klasa_js_script = "";
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
			$content_text="<p class='title'>WYCIĄGI</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template == "admin") || ($this->page_obj->template == "index") )
			{
				switch($this->page_obj->target)
				{
					case "processing":
						$content_text .= $this->processing_iden_wyciagu();
						$content_text .= $this->processing();
						break;
					case "assign_write":
						$idw = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$selected_uczniowie = isset($_GET['par2'])?$_GET['par2']:(isset($_POST['selected_uczniowie'])?$_POST['selected_uczniowie']:0);
						$aktualnailosc=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['aktualnailosc'])?$_POST['aktualnailosc']:0);
						$content_text .= $this->assign_select_idu_write($idw,$selected_uczniowie,$aktualnailosc);
						break;
					case "assign_select_idu":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$aktualnailosc=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['aktualnailosc'])?$_POST['aktualnailosc']:0);
						$content_text .= $this->assign_select_idu_form($idw,$aktualnailosc);
						break;
					case "dodajplik":
						if(isset($_FILES['filehtml']) && !empty($_FILES['filehtml']))
						{
							$content_text .= $this->uploadfile($_FILES['filehtml']);
						}
					break;
					case "przetwarzanie":
						$content_text .= $this->processingfile();
					break;
					case "ukryj":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text .= $this->ukryj($idw,$confirm);
						break;
					case "odkryj":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text .= $this->odkryj($idw,$confirm);
						break;
					case "przywroc":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text .= $this->restore($idw,$confirm);
					break;
					case "usun":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text .= $this->delete_idw($idw,$confirm);
					break;
					case "zapisz":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$tytul=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['tytul'])?$_POST['tytul']:"");
						$data=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['data'])?$_POST['data']:"");
						$typ=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['typ'])?$_POST['typ']:"");
						$content_text .= $this->add($idw,$tytul,$data,$typ);
					break;
					case "formularz":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$tytul=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['tytul'])?$_POST['tytul']:"");
						$data=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['data'])?$_POST['data']:"");
						$typ=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['typ'])?$_POST['typ']:"");
						$content_text .= $this->form($idw,$tytul,$data,$typ);
					break;
					case "raporty":
						$content_text .= $this->raporty();
					break;
					case "lista":
					default:
						$aktualnailosc = isset($_GET['par1']) ? $_GET['par1'] : (isset($_POST['aktualnailosc']) ? $_POST['aktualnailosc'] : 0);
						$action = isset($_POST['action']) ? $_POST['action'] : "";
						$hidde_ready = isset($_POST['hidde_ready']) ? $_POST['hidde_ready'] : "";
						$show_hidden = isset($_POST['show_hidden']) ? $_POST['show_hidden'] : "";
						
						$content_text .= $this->lista($aktualnailosc,$action,$hidde_ready,$show_hidden);
						break;
				}
			}
			else if ($this->page_obj->template == "raw")
			{
				switch($this->page_obj->target)
				{
					case "refresh":
					default:
						$content_text = $this->refresh();
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region lista
		public function lista($aktualnailosc,$action,$hidde_ready,$show_hidden)
		{
			$rettext="";
			//--------------------
			if($action == "filtruj")
			{
				$_SESSION[get_class($this)."_filtruj"] = $hidde_ready == "on";
				$_SESSION[get_class($this)."_show_hidden"] = $show_hidden == "on";
			}
			if(!isset($_SESSION[get_class($this)."_filtruj"])) $_SESSION[get_class($this)."_filtruj"] = true;
			$hidde_assigned = $_SESSION[get_class($this)."_filtruj"];
			if(!isset($_SESSION[get_class($this)."_show_hidden"])) $_SESSION[get_class($this)."_show_hidden"] = false;
			$show_hidden = $_SESSION[get_class($this)."_show_hidden"];
			//--------------------
			//$rettext .= "<button title='Dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz\"'>Dodaj nowy</button> ";
			$rettext .= "<button title='Wgraj plik eksportu' type='button' class='button_add' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},przetwarzanie\"'>Wgraj plik eksportu</button> ";
			$rettext .= "<button title='Raport wyciągów' type='button' class='button_add' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},raporty\"'>Raport wyciągów</button> ";
			$rettext .= "<button title='Processing' type='button' class='button_add' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},processing\"'>Processing</button><br />";
			//--------------------
			$rettext .= "<br />";
			$rettext .= "<fieldset style='border:1px solid black;width:500px;'>";
			$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},lista'>";
			$rettext .= "<input type='checkbox' name='hidde_ready' onclick='this.form.submit();' ".($hidde_assigned?"checked='checked'":"")."> Ukryj przypisane";
			$rettext .= "<input type='checkbox' name='show_hidden' onclick='this.form.submit();' ".($show_hidden?"checked='checked'":"")."> Pokaż ukryte";
			$rettext .= "<input type='hidden' name='action' value='filtruj'>";
			$rettext .= "</form>";
			$rettext .= "</fieldset><br><br>";
			//--------------------
			if($aktualnailosc=="")$aktualnailosc=0;

			$show_hidden_filtr = "";
			if($show_hidden)
			{
				$show_hidden_filtr = "or w.usuniety = 'ukryty' ";
			}
			if($hidde_assigned)
			{
				//$this->page_obj->database_obj->get_data("select w.idw from ".get_class($this)." w, wyciagi_uczniowie wu where w.usuniety='nie' and w.idw = wu.idw");
				$this->page_obj->database_obj->get_data("select w.idw from ".get_class($this)." w where (w.usuniety = 'nie' $show_hidden_filtr) and w.idw not in (select idw from wyciagi_uczniowie wu where wu.usuniety='nie');");
			}
			else
			{
				$this->page_obj->database_obj->get_data("select w.idw from ".get_class($this)." w where (w.usuniety = 'nie' $show_hidden_filtr)");
			}
			

			$iloscwszystkich=$this->page_obj->database_obj->result_count();
			$iloscnastronie = 15;
			//--------------------
			if($hidde_assigned)
			{
				//$wynik=$this->page_obj->database_obj->get_data("select idw,tytul,dataoperacji,typ,usuniety from ".get_class($this)." w where w.usuniety='nie' limit $aktualnailosc,$iloscnastronie;");
				$wynik=$this->page_obj->database_obj->get_data("select idw,tytul,dataoperacji,typ,usuniety from ".get_class($this)." w where (w.usuniety = 'nie' $show_hidden_filtr) and w.idw not in (select idw from wyciagi_uczniowie wu where wu.usuniety='nie')  limit $aktualnailosc,$iloscnastronie;");
			}
			else
			{
				$wynik=$this->page_obj->database_obj->get_data("select w.idw,w.tytul,w.dataoperacji,w.typ,w.usuniety from ".get_class($this)." w where (w.usuniety = 'nie' $show_hidden_filtr) limit $aktualnailosc,$iloscnastronie;");
			}
			if($wynik)
			{
				$rettext .= "<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext .= "<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext .= "<table style='width:100%;font-size:10pt;' cellspacing='0'>";
				$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>Tytuł</td>
						<td>Data</td>
						<td>Typ</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
						<!--<td style='width:18px;'></td>-->
						<!--<td style='width:18px;'></td>-->
					</tr>";
				$lp=0;
				while(list($idw,$tytul,$data,$typ,$usuniety)=$wynik->fetch_row())
				{
					$is_assigned = $this->page_obj->wyciagi_uczniowie->is_assigned_wyciagi($idw) ? "<img src='./media/ikony/users.png' alt='' style='height:30px;'/>" : "";
					//--------------------
					$lp++;
					//--------------------
					if($usuniety == "nie")
					{
						$operacja = "<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idw,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
						$link_ukryj_odkryj = "<a href='javascript:potwierdzenie(\"Czy napewno ukryć wyciąg?\",\"".get_class($this).",{$this->page_obj->template},ukryj,$idw,yes\",window)'><img src='./media/ikony/eye_on.png' alt='' style='height:30px;'/></a>";
					}
					else if($usuniety == "tak")
					{
						$operacja = "<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idw,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:30px;'/></a>";
						$link_ukryj_odkryj = "";
					}
					else if($usuniety == "ukryty")
					{
						$link_ukryj_odkryj = "<a href='javascript:potwierdzenie(\"Czy napewno przywrócić wyciąg?\",\"".get_class($this).",{$this->page_obj->template},odkryj,$idw,yes\",window)'><img src='./media/ikony/eye_off.png' alt='' style='height:30px;'/></a>";
						$operacja = "";
					}

					$link_edytuj = "<a href='".get_class($this).",{$this->page_obj->template},formularz,$idw'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a>";
					//blokada linku edytuj
					$link_edytuj = "";
					//--------------------
					$rettext .= "
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idw' onmouseover=\"setopticalwhite50('wiersz$idw')\" onmouseout=\"setoptical0('wiersz$idw')\">
							<td style='text-align:right;padding-right:5px;'>".($aktualnailosc + $lp).".</td>
							<td>$tytul</td>
							<td>".substr($data,0,10)."</td>
							<td>$typ</td>
							<td style='text-align:center;'>$is_assigned</td>
							<td style='text-align:center;'>$link_ukryj_odkryj</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},assign_select_idu,$idw,$aktualnailosc'><img src='./media/ikony/niegotowe.png' alt='' style='height:30px;'/></a></td>
							<!--<td style='text-align:center;'>$link_edytuj</td>-->
							<!--<td style='text-align:center;'>$operacja</td>-->
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
		public function form($idw,$tytul,$data,$typ)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($idw!="" && is_numeric($idw) && $idw>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select tytul,data,typ from ".get_class($this)." where usuniety='nie' and idw=$idw");
				if($wynik)
				{
					list($tytul,$data,$typ)=$wynik->fetch_row();
				}
			}
			//--------------------
			$tytul=$this->page_obj->text_obj->doedycji($tytul);
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
							<div class='wiersz'><div class='formularzkom1'>Tytuł: </div><div class='formularzkom2'><input type='text' name='tytul' value='$tytul' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>Data: </div><div class='formularzkom2'><input type='text' name='data' value='$data' style='width:800px;'/></div></div>
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
									<button title='Anuluj' style='font-size:20px;float:right;' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idw' value='$idw' />
						<input type='hidden' name='typ' value='reczny' />
						
					</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($idw,$tytul,$data,$typ)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$tytul = $this->page_obj->text_obj->domysql($tytul);
			//--------------------
			if( ($idw != "") && is_numeric($idw) && ($idw > 0) )
			{
				$zapytanie="update ".get_class($this)." set tytul='$tytul', data='$data', typ='$typ' where idw=$idw;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(tytul,data,typ)values('$tytul','$data','$typ')";//nowy wpis
			}
			//--------------------
			if(!$_SESSION['antyrefresh'])
			{
				if($this->page_obj->database_obj->execute_query($zapytanie))
				{
					$_SESSION['antyrefresh']=true;
					$rettext .= "Zapisane<br />";
					$rettext.=$this->lista("","","","");
				}
				else
				{
					$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext.=$this->form($idw,$tytul,$data,$typ);
				}
			}
			else
			{
				$rettext.=$this->lista("","","","");
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region delete_idw
		public function delete_idw($idw,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idw=$idw;"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista("","","","");
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
					$rettext.=$this->lista("","","","");
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
		public function restore($idw,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idw=$idw;"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista("","","","");
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd przywracania</span><br />";
					$rettext.=$this->lista("","","","");
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
		#region input file to processing 
		public function processingfile()
		{
			$rettext="";
			//--------------------
			$rettext .= "<h3>Przetwarzanie pliku XML</h3><br>";
			$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},dodajplik' enctype='multipart/form-data'>";
			$rettext .= "Pobierz XML: <input type='file' name='filehtml'>";
			$rettext .= "<br><input type='submit' name='submit' value='ZAŁADUJ XML'>";
			$rettext .= "</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region processing from HTML to SQL db
		public function przetwarzanie_htmlToSql($file)
		{
			$rettext = "";
			$rettext .= "<hr>";
			$rettext .= "<br>pobieram nazwę pliku: " . $file . "<br>";
			/**
			* dokument przetwarza plik html i zapisuje do bazy
			* Hydrotrade Polska Rafał Płatkowski, Arkadiusz Waliczek
			*/
			
			//Tworzę nowy obiekt struktury pliku html "dom"
			$dom = new domDocument(); 
			
			//dynamicznie pobieram dokument do przetwarzania
			$dom->loadHTMLFile("./media/filehtml/$file");

			//Zezwalamy na usuwanie nadmiarowych białych znaków z dokumentu HTML
			$dom->preserveWhiteSpace = false;
			
			//tworzę uchwyt do znaczników <table> (jest ich w obenym dokumencie 3)
			$tables = $dom->getElementsByTagName('table');
				
			//dla pierwszej tabeli, pierwszy wiersz, dwie komórki
			$rows = $tables->item(0)->getElementsByTagName('tr');
			$nrkonta = $rows[0]->getElementsByTagName('td');

			$nrkontaBaza = $nrkonta->item(1)->nodeValue;
			$rettext .= "Nr konta bankowego<b>".$nrkontaBaza.'</b><br/><br/>';
			$nrkontaBaza = $this->page_obj->text_obj->domysql($nrkontaBaza);

			//uchwyt do trzeciej tabeli
			$rowsTr3Tabela = $tables->item(2)->getElementsByTagName('tr');
			
			
			$tablicaData = array();
			foreach($rowsTr3Tabela as $tr){
				$cols = $tr->getElementsByTagName('td');
				if($cols->item(2)->nodeValue == "Przelew na rachunek")
				{
					array_push($tablicaData,$cols->item(0)->nodeValue);//zawiera daty przelewów dla Przelewu na rachunek
				}
			}
			$dlugosc_tablicaData = sizeof($tablicaData)-1;
			$dataOd = $tablicaData[$dlugosc_tablicaData];
			$dataDo = $tablicaData[0];

			//------Dodaje do tablicy tymczasowej i porównuje je z tabelą stała wyciagi
			//-----------------------Tablica tymczasowa----------------------
						foreach($rowsTr3Tabela as $tr)
						{
							$cols = $tr->getElementsByTagName('td');
							if($cols->item(2)->nodeValue == "Przelew na rachunek"){
								
								$data = $cols->item(0)->nodeValue;//zawiera datę przelewu
								$opis = $cols->item(3)->nodeValue;//zawiera całą komórkę wraz z opisem
								$opisDane = explode(":", $opis);//rozdzielam na tablice $opisDane...
								
								$rachunekNadawcy = substr($opisDane[1], 0, strpos($opisDane[1], "Nazwa nadawcy"));
								//echo "Rachunek Nadawcy: ".$rachunekNadawcy.'<br>';
								
								$NazwaNadawcy = substr($opisDane[2], 0, strpos($opisDane[2], "Adres nadawcy"));
								//echo "3: ".$NazwaNadawcy.'<br>';
								
								$AdresNadawcy = substr($opisDane[3], 0, strpos($opisDane[3], "Tytuł"));
								//echo "4: ".$AdresNadawcy."<br>";
								
								if(isset($opisDane[4])){
			
									if(strpos($opisDane[4], "Referencje własne zleceniodawcy")){
									$tytul = substr($opisDane[4], 0, strpos($opisDane[4], "Referencje własne zleceniodawcy"));
									//echo "5: ".$tytul."<br>";
									} else {
									$tytul = $opisDane[4];
									//echo "5: ".$tytul."<br>";
									}
									
									//jeśli "Referencje własne zleceniodawcy" to pokaż nr:....
									if(strpos($opisDane[4], "Referencje własne zleceniodawcy")){
									$Referencje = $opisDane[5]; 
									//echo "6: ".$Referencje."<br>";
									} else {
										$Referencje = 0;
									}
								}
								$wplyw = $cols->item(4)->nodeValue;//zawiera całą komórkę wraz z kwota
			
								$tytul = $this->page_obj->text_obj->domysql($tytul);
								$rachunekNadawcy = $this->page_obj->text_obj->domysql($rachunekNadawcy);
								$AdresNadawcy = $this->page_obj->text_obj->domysql($AdresNadawcy);
								$wplyw = $this->page_obj->text_obj->domysql($wplyw);
								$data = $this->page_obj->text_obj->domysql($data);
								$NazwaNadawcy = $this->page_obj->text_obj->domysql($NazwaNadawcy);
								$Referencje = $this->page_obj->text_obj->domysql($Referencje);
			
								$tytul = trim($tytul);
								$rachunekNadawcy = trim($rachunekNadawcy);
								$NazwaNadawcy = trim($NazwaNadawcy);
								$Referencje = trim($Referencje);
								$AdresNadawcy = trim($AdresNadawcy);
								
								$tytul = preg_replace('/\t/', '', $tytul);
								//$rachunekNadawcy = preg_replace('/\s/', '', $rachunekNadawcy);
								$NazwaNadawcy = preg_replace('/\t/', '', $NazwaNadawcy);
								$AdresNadawcy = preg_replace('/\t/', '', $AdresNadawcy);

								//pobieram nr konta jesli nie mam to wpierw dodaje do bazy--------------------------------------
								$wynik=$this->page_obj->database_obj->get_data("select idnk from nr_konta where numer_konta='$nrkontaBaza' limit 1;");
								if($wynik)
								{
									list($idnk)=$wynik->fetch_row();
								} else {
									$zapytanie="insert into nr_konta(numer_konta,dataod,datado) values('$nrkontaBaza','$dataOd','$dataDo')";
									$this->page_obj->database_obj->execute_query($zapytanie);
									$idnk =  $this->page_obj->database_obj->last_id();
								}
								//---------------------------------------------------------------------------------------------
								//dodaj do tabeli "dokumenthtml" i pobierz lastid dokumenthtml (idhtml, nazwa)
								
								$wynik_dokumenthtml=$this->page_obj->database_obj->get_data("select idhtml from dokumenthtml where nazwa='$file' limit 1;");
								if($wynik_dokumenthtml)
								{
									list($iddokumenthtml)=$wynik_dokumenthtml->fetch_row();
								} else {
									$zapytanie2="insert into dokumenthtml(nazwa) values('$file')";
									$this->page_obj->database_obj->execute_query($zapytanie2);
									$iddokumenthtml =  $this->page_obj->database_obj->last_id();
								}
								
								//-----------------------------------------------------------------------------

								$zapytanie="insert into wyciagi_template(tytul, typ, rachuneknadawcy, adresnadawcy, kwota, dataoperacji, nazwanadawcy,nrreferencyjny,id_nr_konta,nazwapliku_id)
								values('$tytul','bankowy','$rachunekNadawcy','$AdresNadawcy',$wplyw,'$data','$NazwaNadawcy','$Referencje',$idnk,$iddokumenthtml)";
								$this->page_obj->database_obj->execute_query($zapytanie);	
							}	
						}

			
					$wynik=$this->page_obj->database_obj->get_data("SELECT idt, rachuneknadawcy, dataoperacji, kwota, id_nr_konta, tytul, adresnadawcy, nazwanadawcy, nrreferencyjny, nazwapliku_id FROM wyciagi_template;");
					if($wynik)
					{
						
						while(list($idt, $rachuneknadawcy, $dataoperacji, $kwota, $id_nr_konta, $tytul, $adresnadawcy, $nazwanadawcy, $nrreferencyjny, $nazwapliku_id)=$wynik->fetch_row()){
							
							$wynik_wyciagi_template=$this->page_obj->database_obj->get_data("SELECT COUNT(idt) FROM wyciagi_template WHERE rachuneknadawcy='$rachuneknadawcy' AND dataoperacji='$dataoperacji' AND kwota=$kwota;");
							if($wynik_wyciagi_template)
							{
								list($ilosc_wyciagi_template)=$wynik_wyciagi_template->fetch_row();
							} else {
								echo "błąd ilość rekordów tabeli wyciagi_template";
							}
													
							$wynik_wyciagi=$this->page_obj->database_obj->get_data("SELECT COUNT(idw) FROM wyciagi WHERE rachuneknadawcy='$rachuneknadawcy' AND dataoperacji='$dataoperacji' AND kwota=$kwota;");
							if($wynik_wyciagi)
							{
								list($ilosc_wyciagi)=$wynik_wyciagi->fetch_row();
							} else {
								echo "błąd ilość rekordów tabeli wyciagi";
							}

							//test !!!
							echo("$ilosc_wyciagi_template : $ilosc_wyciagi<br>");
							//jeśli ilosc wyciagi < wyciagi_template to dodajemy do tabeli wyciagi
							
							if(($ilosc_wyciagi_template > $ilosc_wyciagi)){
								echo("ok<br>");
								// dodajemy do bazy danych
								$zapytanie_wyciagi="insert into wyciagi(tytul, typ, rachuneknadawcy, adresnadawcy, kwota, dataoperacji, nazwanadawcy,nrreferencyjny,id_nr_konta,nazwapliku_id)
								values('$tytul','bankowy','$rachuneknadawcy','$adresnadawcy',$kwota,'$dataoperacji','$nazwanadawcy','$nrreferencyjny',$id_nr_konta,$nazwapliku_id)";
								$this->page_obj->database_obj->execute_query($zapytanie_wyciagi);
							} else {
								echo("baza posiada wpisy<br>");
							}
							
						}
							//czyszczenie tablicy tymczasowej
							//truncate wyciagi_template
							$truncate_wyciagi_template="TRUNCATE TABLE wyciagi_template";
							$this->page_obj->database_obj->execute_query($truncate_wyciagi_template);
					}
					//------------------------------------------------------------------------------
			//--------------------
			/*
			testy
				TRUNCATE wyciagi;
				TRUNCATE nr_konta;
			TRUNCATE wyciagi_template;
				TRUNCATE dokumenthtml;
				SELECT count(*) FROM wyciagi;
			*/
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region uploadfile
		public function uploadfile($file)
		{
			$rettext="";
			//--------------------
			$target_dir = "./media/filehtml/";
			$target_file = $target_dir . basename($file["name"]);
			$rettext .= "<hr>Nazwa dokumentu: ".basename($file["name"])."<hr>";

			$uploadOk = 1;
			$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));     
			// Sprawdzam czy istnieje już plik o tej samej nazwie
			// TODO: odblokować to sprawdzanie
			if (file_exists($target_file) && false)
			{
				$rettext .= "<h5 class='warnigs'>Plik istnieje o podanej nazwie.</h5>";
				$uploadOk = 0;
			}
			// Sprawdzam rozmiar pliku
			if ($file["size"] > 500000)
			{
				$rettext .= "<h5 class='warnigs'>Plik jest za duży......</h5>";
				$uploadOk = 0;
			}
			// Przetwarzam tylko pliki o rozszerzeniu xml
			if($FileType != "xml")
			{
				$rettext .= "<h5 class='warnigs'>Przetwarzanie tylko dla plików o rozszerzeniu xml.....</h5>";
				$uploadOk = 0;
			}
			// Sprawdzam jeżeli $uploadOk 1 to ok a jeżeli 0 to error
			if ($uploadOk == 0)
			{
				$rettext .= "<h5 class='warnigs'>Nie można przesłać pliku.</h5>";
			}
			else
			{
				if (move_uploaded_file($file["tmp_name"], $target_file))
				{
					$rettext .= "Plik " . htmlspecialchars( basename( $file["name"])) . " został przesłany.";
					$plik = htmlspecialchars( basename($file["name"]) );
					//uruchamiam funkcje do przetwarzania skryptu
					//$this->przetwarzanie_htmlToSql($plik);
					$rettext .= $this->przetwarzanie_xmlToSql($plik);
				//--------------------------------------------------------------------
				}
				else
				{
					$rettext .= "<h5 class='warnigs'>błąd przesłania pliku na serwer.</h5>";
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		private function przetwarzanie_xmlToSql($uploaded_file)
		{
			$rettext = "";
			//--------------------
			$rettext .= "<hr />";

			$xml = simplexml_load_file("./media/filehtml/" . $uploaded_file);
			if ($xml === false)
			{
				$rettext .= "Błąd składni pliku XML:<br>";
				foreach( libxml_get_errors() as $error )
				{
					$rettext .= $error->message . "<br />";
				}
			}
			else
			{
				//--------------------
				// account number
				//--------------------
				if ( isset($xml->search) && isset($xml->search->account) )
				{
					$account_number = $this->page_obj->text_obj->domysql($xml->search->account);
					//TODO: po co to jak kolejny wpis już tego nie zamiesci - co autora miał na myśli
					$dataOd = $this->page_obj->text_obj->domysql($xml->search->date['since']);
					$dataDo = $this->page_obj->text_obj->domysql($xml->search->date['to']);
					//~~~~~~~~~~~~~~~~~~~~~~~~
					//$rettext .= "$account_number : $dataOd : $dataDo <br />";

					$wynik = $this->page_obj->database_obj->get_data("select idnk from nr_konta where numer_konta = '$account_number' limit 1;");
					if($wynik)
					{
						list($idnk) = $wynik->fetch_row();
					}
					else
					{
						$this->page_obj->database_obj->execute_query("insert into nr_konta(numer_konta,dataod,datado) values('$account_number','$dataOd','$dataDo')");
						$idnk =  $this->page_obj->database_obj->last_id();
					}

					$uploaded_file_sql = $this->page_obj->text_obj->domysql($uploaded_file);
					$wynik_dokumenthtml = $this->page_obj->database_obj->get_data("select idhtml from dokumenthtml where nazwa = '$uploaded_file_sql' limit 1;");
					if($wynik_dokumenthtml)
					{
						list($iddokumenthtml)=$wynik_dokumenthtml->fetch_row();
					}
					else
					{
						$this->page_obj->database_obj->execute_query("insert into dokumenthtml(nazwa) values('$uploaded_file_sql')");
						$iddokumenthtml =  $this->page_obj->database_obj->last_id();
					}

				}
				else
				{
					return "Plik nieprawidłowy. Nie znaleziono numeru konta.";
				}

				//--------------------
				// operations
				//--------------------
				if ( isset($xml->operations) )
				{
					// clear temporary table
					$this->page_obj->database_obj->execute_query("TRUNCATE TABLE wyciagi_template");
					$licznik_odczytanych_wpisow = 0;
					foreach($xml->operations->children() as $operation)
					{
						if($operation->type == "Przelew na rachunek")
						{
							$rachunekNadawcy = "";
							$NazwaNadawcy = "";
							$AdresNadawcy = "";
							$tytul = "";
							$Referencje = "";
							$wplyw = 0;
							$data = "2020-01-01";

							//[description] => 
								//Rachunek nadawcy: 10 1020 2528 0000 0902 0408 9249 
								//Nazwa nadawcy: DOROTA MARIA SIKORA Adres nadawcy: UL. KAZIMIERZA WIELKIEGO 32 M.6 43-200 PSZCZYNA 
								//Tytuł: WRZESIEŃ + PAŹDZIERNIK CZESNE FRANC ISZEK SIKORA 
								//Referencje własne zleceniodawcy: 172614825767 

							//for debug
							//$rettext .= $operation->type . "<br />";
							//$rettext .= $operation->{'exec-date'} . "<br />";
							//$rettext .= $operation->{'order-date'} . "<br />";
							//$rettext .= $operation->description . "<br />"; 
							//$rettext .= $operation->amount . "<br />";
							//$rettext .= $operation->{'ending-balance'} . "<br />";
							//$rettext .= "<hr />";*/

							//add to temporary table

							$description_array = explode("\n",$operation->description);
							foreach($description_array as $val)
							{
								if ( strpos($val, "Rachunek nadawcy:") !== false )
								{
									$rachunekNadawcy = $this->page_obj->text_obj->domysql( trim( substr($val, 18) ) );
								}

								if ( strpos($val, "Nazwa nadawcy:") !== false )
								{
									$NazwaNadawcy = $this->page_obj->text_obj->domysql( trim( substr($val, 15) ) );
								}

								if ( strpos($val, "Adres nadawcy:") !== false )
								{
									$AdresNadawcy = $this->page_obj->text_obj->domysql( trim( substr($val, 15) ) );
								}

								if ( strpos($val, "Tytuł:") !== false )
								{
									$tytul = $this->page_obj->text_obj->domysql( trim( substr($val, 7) ) );
								}

								if ( strpos($val, "Referencje własne zleceniodawcy:") !== false )
								{
									$Referencje = $this->page_obj->text_obj->domysql( trim( substr($val, 33) ) );
								}
							}
							$wplyw = floatval( trim( $operation->amount ) );
							$data = $this->page_obj->text_obj->domysql( trim( $operation->{'exec-date'} ) );

							//for debug
							
							//$rettext .= $wplyw . " (".$operation->amount.")<br />";
							//$rettext .= $data . "<br />";
							//$rettext .= $rachunekNadawcy . "<br />";
							//$rettext .= $NazwaNadawcy . "<br />";
							//$rettext .= $AdresNadawcy . "<br />";
							//$rettext .= $tytul . "<br />";
							//$rettext .= $Referencje . "<br />";
							//$rettext .= "<hr />";

							$this->page_obj->database_obj->execute_query("insert into wyciagi_template(tytul, typ, rachuneknadawcy, adresnadawcy, kwota, dataoperacji, nazwanadawcy,nrreferencyjny,id_nr_konta,nazwapliku_id) values ('$tytul', 'bankowy', '$rachunekNadawcy', '$AdresNadawcy', $wplyw, '$data', '$NazwaNadawcy', '$Referencje', $idnk, $iddokumenthtml)");
							$licznik_odczytanych_wpisow++;
						}
					}

					//merge temporary table with destination table
					$licznik_wstawionych_wpisow = 0;
					$wynik = $this->page_obj->database_obj->get_data("SELECT idt, rachuneknadawcy, dataoperacji, kwota, id_nr_konta, tytul, adresnadawcy, nazwanadawcy, nrreferencyjny, nazwapliku_id FROM wyciagi_template;");
					if($wynik)
					{
						while( list($idt, $rachuneknadawcy, $dataoperacji, $kwota, $id_nr_konta, $tytul, $adresnadawcy, $nazwanadawcy, $nrreferencyjny, $nazwapliku_id) = $wynik->fetch_row() )
						{
							$wynik_wyciagi_template = $this->page_obj->database_obj->get_data("SELECT COUNT(idt) FROM wyciagi_template WHERE rachuneknadawcy='$rachuneknadawcy' AND dataoperacji='$dataoperacji' AND kwota=$kwota;");
							if($wynik_wyciagi_template)
							{
								list($ilosc_wyciagi_template)=$wynik_wyciagi_template->fetch_row();
							}
							else
							{
								$rettext .= "błąd ilość rekordów tabeli wyciagi_template <br />";
							}
							$wynik_wyciagi = $this->page_obj->database_obj->get_data("SELECT COUNT(idw) FROM wyciagi WHERE rachuneknadawcy='$rachuneknadawcy' AND dataoperacji='$dataoperacji' AND kwota=$kwota;");
							if($wynik_wyciagi)
							{
								list($ilosc_wyciagi)=$wynik_wyciagi->fetch_row();
							}
							else
							{
								$rettext .= "błąd ilość rekordów tabeli wyciagi <br />";
							}

							//$rettext .= "$ilosc_wyciagi_template : $ilosc_wyciagi<br>";
							//jeśli ilosc wyciagi < wyciagi_template to dodajemy do tabeli wyciagi

							if( $ilosc_wyciagi_template > $ilosc_wyciagi )
							{
								//$rettext .= "ok <br />";
								// dodajemy do bazy danych
								$zapytanie_wyciagi = "insert into wyciagi(tytul, typ, rachuneknadawcy, adresnadawcy, kwota, dataoperacji, nazwanadawcy,nrreferencyjny,id_nr_konta,nazwapliku_id)
								values('$tytul','bankowy','$rachuneknadawcy','$adresnadawcy',$kwota,'$dataoperacji','$nazwanadawcy','$nrreferencyjny',$id_nr_konta,$nazwapliku_id)";
								$this->page_obj->database_obj->execute_query($zapytanie_wyciagi);
								$licznik_wstawionych_wpisow++;
							}
							else
							{
								$rettext .= "baza posiada wpisy <br />";
							}
						}
					}

					$rettext .= "Odczytano $licznik_odczytanych_wpisow wpisów. <br />";
					$rettext .= "Wprowadzono $licznik_wstawionych_wpisow nowych wpisów. <br />";
					$rettext .= "Duplikatów:". ($licznik_odczytanych_wpisow - $licznik_wstawionych_wpisow) . "<br />";

					//czyszczenie tablicy tymczasowej
					$this->page_obj->database_obj->execute_query("TRUNCATE TABLE wyciagi_template");
				}
				else
				{
					$rettext .= "Plik nie zawiera operacji płatniczych  (error 1)<br>";
				}
			}
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		#region raporty
		public function raporty()
		{
			$rettext="";
			//--------------------
			// Raporty------------
			$rettext .= "
			<script src=\"./media/wyszukiwarka/jQuery/jquery.min.js\"></script>
        	<link rel=\"stylesheet\" href=\"./media/wyszukiwarka/jQuery/jquery-ui.css\">
    		<script src=\"./media/wyszukiwarka/jQuery/jquery-ui.min.js\"></script>
    		<script>
				$(function() {
					$(\"#nrkonta_szukaj\").autocomplete({
						source: \"./media/wyszukiwarka/szukaj.php\",
					});
				});
    		</script>
			";

			$rettext .= "
			<style>
			.formraport input{
				font-size: inherit;
				padding: 0.3em;
				margin: 0.2em 0.4em;
				-moz-box-sizing: content-box;
				-webkit-box-sizing: content-box;
				box-sizing: content-box;
			}
			</style>
			";
			$rettext .= "<h3>RAPORTY WYCIĄGÓW</h3><hr>";
			$rettext .= "<h3>
			<form action='".get_class($this).",{$this->page_obj->template},raporty' method='post' class='formraport'>
				Szukaj wg. nr konta: <input type='text' name='nrkonta' id='nrkonta_szukaj' placeholder='znajdź nr konta...' style='width:230px;'/>
				<input type='submit' name='submitnrkonta' value='POKAŻ RAPORT'>
				</form>
			</h3><hr>";
			$rettext .= "<form action='".get_class($this).",{$this->page_obj->template},raporty' method='post' class='formraport'>
			Data od: <input type='date' name='dataod'> 
			Data do: <input type='date' name='datado'> 
			<input type='submit' name='submitdata' value='POKAŻ RAPORT' class='formsubmit'>
			</form><br>";

			if(isset($_POST['submitdata'])){
				if(!empty($_POST['dataod']) && !empty($_POST['datado'])){
					$rettext .= "Data od: {$_POST['dataod']} Data do: {$_POST['datado']}";
					$od = $_POST['dataod'];
					$do = $_POST['datado'];
					
					$raport1=$this->page_obj->database_obj->get_data("SELECT sum(kwota) FROM wyciagi WHERE dataoperacji >= '$od' AND dataoperacji <= '$do';");
					if($raport1)
					{
						list($suma_wplat)=$raport1->fetch_row();
						if($suma_wplat > 0){
							$rettext .= "<h4>Suma wpłat: $suma_wplat zł <button><a href='".get_class($this).",{$this->page_obj->template},raporty,$od,$do,$suma_wplat' style='text-decoration:none;color:black;'>LISTA</a></button></h4><hr>";
						} else {
							$rettext .= "<h4>Suma wpłat: 0 zł </h4><hr>";
						}
					
					} else {
						echo "błąd raportu";
					}
				}
			}

			//lista dla zakresu daty od do---------------------------------------------
			if(isset($_GET['par1']) && isset($_GET['par2']) && isset($_GET['par3'])){
				
				$od = $_GET['par1']; $do = $_GET['par2']; $kw = $_GET['par3'];
				$rettext .= "<h4>Suma wpłat: <span style='color:blue'>$kw zł</span>, Od $od - Do $do 
				<br><br>
				<form action='./media/pdf/tworzpdf.php' method='post'>
					<input type='hidden' name='od' value='$od'>
					<input type='hidden' name='do' value='$do'>
					<input type='hidden' name='kwota' value='$kw'> 
					<input type='submit' value='DO PDF' name='submitpdf'>
				</form> <br>
				
				<form action='./media/csv/csv.php' method='post'>
					<input type='hidden' name='od' value='$od'>
					<input type='hidden' name='do' value='$do'> 
					<input type='submit' value='DO CSV' name='submitcsv'>
				</form> <br>";

				$rettext .= "<table style='width:100%;font-size:10pt;' cellspacing='0' border='1'><tbody>";
				$rettext .= "<tr style='font-weight:bold;'>";
					$rettext .= "<td>Lp.</td><td>Wpływ</td><td>Tytuł</td><td>Data</td><td>Typ</td>";
				$rettext .= "</tr>";
				$raport2=$this->page_obj->database_obj->get_data("SELECT kwota,tytul,dataoperacji,typ FROM wyciagi WHERE dataoperacji >= '$od' AND dataoperacji <= '$do' ORDER BY dataoperacji ASC;");
					if($raport2)
					{
						$lp=1;
						while(list($kwota,$tytul,$dataoperacji,$typ)=$raport2->fetch_row()){
							$data = date("Y-m-d",strtotime($dataoperacji));  
							$rettext .= "<tr>";
								$rettext .= "<td>$lp</td><td>$kwota</td><td>$tytul</td><td>$data</td><td>$typ</td>";
							$rettext .= "</tr>";
							$lp++;
						}
					}
				$rettext .= "<tbody></table>";
			}

			//wyszukiwarka po nr konta----------------------
			if(isset($_POST['submitnrkonta'])){
				if(!empty($_POST['nrkonta'])){
					$nrkonta = $_POST['nrkonta'];
					

					$rettext .= "<table style='width:100%;font-size:10pt;' cellspacing='0' border='1'><tbody>";
					$rettext .= "<tr style='font-weight:bold;'>";
						$rettext .= "<td>Lp.</td><td>NR KONTA</td><td>Tytuł</td><td>Data</td><td>Kwota</td>";
					$rettext .= "</tr>";


					$raport1=$this->page_obj->database_obj->get_data("SELECT idw, rachuneknadawcy, tytul, dataoperacji, kwota,typ  FROM wyciagi WHERE rachuneknadawcy = '$nrkonta';");
					if($raport1)
					{
						$lp=1;
						while(list($idw, $rachuneknadawcy, $tytul, $dataoperacji, $kwota,$typ)=$raport1->fetch_row()){
							$data = date("Y-m-d",strtotime($dataoperacji));  
							$rettext .= "<tr>";
								$rettext .= "<td>$lp</td><td>$kwota zł</td><td>$tytul</td><td>$data</td><td>$typ</td>";
							$rettext .= "</tr>";
							$lp++;
						}
					} else {
						echo "brak danych dla nr konta";
					}
					$rettext .= "<tbody></table>";

					$raport2=$this->page_obj->database_obj->get_data("SELECT sum(kwota) FROM wyciagi WHERE rachuneknadawcy='$nrkonta';");
					if($raport2)
					{
						list($suma_wplat)=$raport2->fetch_row();
						if($suma_wplat > 0){
							$rettext .= "<h4>Suma wpłat: $suma_wplat zł </h4><hr>";
						} else {
							$rettext .= "<h4>Suma wpłat: 0 zł </h4><hr>";
						}
					
					} else {
						echo "błąd raportu";
					}
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region refresh
		private function refresh()
		{
			$this->page_obj->syslog(debug_backtrace(),"Execute - ".date("Y-m-d H:i:s"));
			$this->processing_iden_wyciagu();
			$this->processing();
			return "refresh";
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region assign_select_idu_form
		private function assign_select_idu_form($idw,$aktualnailosc)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			$rettext="
					<style>
						div.wiersz{float:left;clear:left;}
						div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
						div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
					</style>";
			$rettext .= "
					<form method='post' action='".get_class($this).",{$this->page_obj->template},assign_write'>
						<div style='overflow:hidden;'>
							
							<div style = 'font-size:18px;'>
								Przypisz uczniów do wyciągu: <br />
								<p style='text-indent:20px;font-weight:bold;'>{$this->get_nadawce($idw)} <span style='font-size:26px;'>|</span> {$this->get_tytul($idw)} <span style='font-size:26px;'>|</span> {$this->get_date($idw)} <span style='font-size:26px;'>|</span> {$this->get_kwota($idw)} zł</p>
							</div>

							<br /><br />
							<div class='wiersz'><div class='formularzkom1'>Oddział: </div><div class='formularzkom2'>{$this->create_select_field_for_oddzial('klasa_select')}</div></div>
							<div class='wiersz'><div class='formularzkom1'>klasa: </div><div class='formularzkom2'>{$this->create_select_field_for_klasa('klasa_select','uczniowie_select','selected_uczniowie')}</div></div>
						
							<div class='wiersz'>
								<div class='formularzkom1'>uczniowie: </div>
								<div class='formularzkom2'>
									<br />
									".$this->create_uczniowie_select_field($idw,'uczniowie_select','selected_uczniowie')."
								</div>
							</div>
						
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' onclick='selectAll();' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
									<button title='Anuluj' style='font-size:20px;float:right;' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista,$aktualnailosc\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idw' value='$idw' />
						<input type='hidden' name='aktualnailosc' value='$aktualnailosc' />
					</form>
					{$this->update_select_field_from_oddzialy_js_script}
					{$this->update_select_field_from_klasa_js_script}
					{$this->javascript_select_uczniowie}";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region assign_select_idu_write
		private function assign_select_idu_write($idw,$selected_uczniowie,$aktualnailosc)
		{
			$rettext = "Save account number to student.";
			//get rachunek_nadawcy from wyciagi
			$rachunek_nadawcy = $this->get_rachunek_nadawcy($idw);
			//get idk or insert new from konta_bankowe where numer_konta == rachunek_nadawcy
			$idk = $this->page_obj->konta_bankowe->get_idk_konta($rachunek_nadawcy);
			if($idk > -1)
			{
				#region mark deleted
				$this->page_obj->uczniowie_konta_bankowe->mark_delete_for_konto($idk);
				#endregion

				//synchronize uczniowie_konta_bankowe for idu and idk
				if(isset($selected_uczniowie) && is_array($selected_uczniowie))
				{
					foreach($selected_uczniowie as $idu)
					{
						$this->page_obj->uczniowie_konta_bankowe->synchronize($idk,$idu);
					}
				}
				else
				{
					$rettext = "selected_uczniowie is not an array";
				}
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region create_uczniowie_select_field
		private function create_uczniowie_select_field($idw,$uczniowie_select_id,$selected_uczniowie_select_id)
		{
			$selected_uczniowie = "";
			$uczniowie = "";
			
			$lista_uczniow = $this->page_obj->uczniowie->get_list();
			//$lista_idu_w_wyciagach = $this->page_obj->wyciagi_uczniowie->get_idu_list_for_idw($idw);
			$lista_idu_w_wyciagach = $this->page_obj->uczniowie_konta_bankowe->get_idu_list_for_idw($idw);
			foreach($lista_uczniow as $val)
			{
				if(in_array($val[0], $lista_idu_w_wyciagach))
				{
					$selected_uczniowie .= "selected_option.push([{$val[0]},'{$val[1]}']);\n";
				}
				else
				{
					$uczniowie .= "available_option.push([{$val[0]},'{$val[1]}']);\n";
				}
			}
			//--------------------
			$rettext = "<div style='float:left;'>
								<label for='selected_uczniowie' style='display:block;'>Wybrani uczniowie:</label>
								<select multiple='multiple' id='$selected_uczniowie_select_id' name='selected_uczniowie[]' style='display:block;width:200px;height:250px;'></select>
							</div>";
		
			$rettext .= "<div style='float:left;width:50px;text-align:center;height:250px;position: relative;'>
								<div style='display:block;position: absolute;top:25px;text-align:center;width:100%;'><a href='#' onclick='remov_uczen_from_select();' style='font-size:30px;font-weight:bold;text-decoration:none;color:black;'> -&gt;</a></div>
								<div style='display:block;position: absolute;bottom:0px;text-align:center;width:100%;'><a href='#' onclick='add_uczen_to_select();' style='font-size:30px;font-weight:bold;text-decoration:none;color:black;'> &lt;-</a></div>
							</div>";
			$rettext .= "<div style='float:left;'>
								<label for='selected_uczniowie' style='display:block;'>uczniowie:</label>
								<select multiple='multiple' id='$uczniowie_select_id' style='display:block;width:200px;height:250px;'></select>
								</div>";
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
			/*$rettext = "<select name='idu'>";
			$uczniowie_array = $this->page_obj->uczniowie->get_list();
			foreach($uczniowie_array as $idu)
			{
				$rettext .= "<option name='$idu[0]'>".$this->page_obj->uczniowie->get_imie_uczniowie_nazwisko_uczniowie($idu[0])."</option>";
			}
			$rettext .= "</select>";*/
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
		#region get_rachunek_nadawcy
		private function get_rachunek_nadawcy($idw)
		{
			$rachuneknadawcy = "";
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select rachuneknadawcy from ".get_class($this)." where idw=$idw;");
			if($wynik) list($rachuneknadawcy)=$wynik->fetch_row();
			//--------------------
			return $rachuneknadawcy;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region processing_iden_wyciagu
		public function processing_iden_wyciagu()
		{
			$rettext = "Auto processing_iden_wyciagu system <br />";
			//--------------------
			foreach($this->page_obj->iden_wyciagu->list_idiw() as $row) //[idiw][idu][identyfikator]
			{
				//$rettext .= "SELECT idw FROM `wyciagi` WHERE wyciagi.nrreferencyjny = '{$row[2]}' and idw not in (select idw from wyciagi_uczniowie where usuniety = 'nie' and status = 'auto_iden');<br />";
				$wynik = $this->page_obj->database_obj->get_data("SELECT idw FROM `wyciagi` WHERE wyciagi.nrreferencyjny = '{$row[2]}' and idw not in (select idw from wyciagi_uczniowie where usuniety = 'nie' and status = 'auto_iden');");
				if($wynik)
				{
					while(list($idw) = $wynik->fetch_row())
					{
						//sprawdzić czy nie ma użytego w auto (wyciagi_uczniowie)
						foreach($this->page_obj->wyciagi_uczniowie->get_idwu_list_for_idw($idw) as $idwu)
						{
							$rettext .= $this->page_obj->wyciagi_uczniowie->delete_idwu($idwu);
						}
						$this->page_obj->wyciagi_uczniowie->synchronize($row[1],$idw,true,true);
						$this->page_obj->iden_wyciagu->mark_idiw_assigned($row[1],$row[0],$idw);
					}
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region processing
		private function processing()
		{
			$rettext = "Auto processing system <br />";
			//--------------------
			// znajdz numery kont przypisane do uczniów
			foreach($this->page_obj->uczniowie_konta_bankowe->get_list_of_nr_konta() as $row)
			{
				$rettext .= $row[0]." | ".$row[2]."<br />";
				// znajdz wyciągi dla danego konta
				$idw_list = $this->get_wyciagi_for_nr_konta_and_ucznia($row[2],$row[0]);
				if(is_array($idw_list))
				{
					foreach($idw_list as $idw)
					{
						$rettext .= "____ ".$idw[0]."<br />";
						// przypisz wyciag do ucznia
						$this->page_obj->wyciagi_uczniowie->synchronize($row[0],$idw[0],true,false);
					}
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get wyciagi for nr konta
		public function get_wyciagi_for_nr_konta_and_ucznia($nr_konta,$idu)
		{
			$rettext = array();
			//--------------------
			//$wynik = $this->page_obj->database_obj->get_data("select idw from ".get_class($this)." where rachuneknadawcy = '$nr_konta' and usuniety = 'nie';");
			//$wynik = $this->page_obj->database_obj->get_data("select idw from ".get_class($this)." where rachuneknadawcy = '$nr_konta' and usuniety = 'nie' and idw not in (select idw from wyciagi_uczniowie where idu = $idu and usuniety = 'nie');");
			$wynik = $this->page_obj->database_obj->get_data("select idw from ".get_class($this)." where rachuneknadawcy = '$nr_konta' and usuniety = 'nie' and idw not in (select idw from wyciagi_uczniowie where idu = $idu and usuniety = 'nie') and idw not in (select idw from iden_wyciagu);");
			if($wynik)
			{
				while(list($idw)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idw);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_kwota
		public function get_kwota($idw)
		{
			$rettext = NAN;
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select kwota from ".get_class($this)." where idw = $idw and usuniety = 'nie';");
			if($wynik)
			{
				list($rettext)=$wynik->fetch_row();
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_tytul
		public function get_tytul($idw)
		{
			$rettext = "";
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select tytul from ".get_class($this)." where idw = $idw and usuniety = 'nie';");
			if($wynik)
			{
				list($rettext)=$wynik->fetch_row();
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_date
		public function get_date($idw)
		{
			$rettext = "";
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select dataoperacji from ".get_class($this)." where idw = $idw and usuniety = 'nie';");
			if($wynik)
			{
				list($rettext) = $wynik->fetch_row();
			}
			//--------------------
			return substr($rettext,0,10);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_nadawce
		public function get_nadawce($idw)
		{
			$rettext = "";
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select nazwanadawcy from ".get_class($this)." where idw = $idw and usuniety = 'nie';");
			if($wynik)
			{
				list($rettext)=$wynik->fetch_row();
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region ukryj
		private function ukryj($idw,$confirm)
		{
			$rettext = "";
			//--------------------
			if($confirm == "yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety = 'ukryty' where idw = $idw and usuniety = 'nie';"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext .= $this->lista("","","","");
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
					$rettext .= $this->lista("","","","");
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
		#region odkryj
		private function odkryj($idw,$confirm)
		{
			$rettext = "";
			//--------------------
			if($confirm == "yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety = 'nie' where idw = $idw and usuniety = 'ukryty';"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext .= $this->lista("","","","");
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
					$rettext .= $this->lista("","","","");
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
		#region add
		public function add_and_return_idw($idw,$tytul,$typ,$kwota)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$tytul = $this->page_obj->text_obj->domysql($tytul);
			//--------------------
			if( ($idw != "") && is_numeric($idw) && ($idw > 0) )
			{
				$result = $this->page_obj->database_obj->execute_query("update ".get_class($this)." set tytul='$tytul', dataoperacji = NOW(), typ='$typ', kwota = $kwota where idw=$idw;");
			}
			else
			{
				$result = $this->page_obj->database_obj->execute_query("insert into ".get_class($this)."(tytul,dataoperacji,typ,kwota,id_nr_konta,rachuneknadawcy,adresnadawcy,nazwanadawcy,nazwapliku_id)values('$tytul',NOW(),'$typ',$kwota, 0, '0', ' ', 'sekretariat',0)");
				if($result)
				{
					$idw = $this->page_obj->database_obj->last_id();
					$nrreferencyjny = time()."_".$idw;
					$result = $this->page_obj->database_obj->execute_query("update ".get_class($this)." set nrreferencyjny = '$nrreferencyjny' where idw = $idw;");
					if( !$result )
					{
						$idw = 0;
					}
				}
			}
			return $idw;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_nrreferencyjny
		public function get_nrreferencyjny($idw)
		{
			$rettext = "";
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select nrreferencyjny from ".get_class($this)." where idw = $idw and usuniety = 'nie';");
			if($wynik)
			{
				list($rettext) = $wynik->fetch_row();
			}
			//--------------------
			return $rettext;
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
			$nazwa="idw";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="id_nr_konta";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie','zablokowany','ukryty')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="tytul";
			$pola[$nazwa][0]="varchar(250)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="rachuneknadawcy";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="adresnadawcy";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="dataoperacji";
			$pola[$nazwa][0]="timestamp";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="kwota";
			$pola[$nazwa][0]="decimal(8,2)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="0";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="nazwanadawcy";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="nrreferencyjny";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="0";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="typ";
			$pola[$nazwa][0]="enum('bankowy','reczny','inny','gotowka')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'inny'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="nazwapliku_id";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//--------------------

			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy="dokumenthtml";
			$pola=array();
			//definicja tablicy
			$nazwa="idhtml";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="nazwa";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);

			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy="wyciagi_template";
			$pola=array();
			//definicja tablicy
			$nazwa="idt";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="id_nr_konta";
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
			
			$nazwa="tytul";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="rachuneknadawcy";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="adresnadawcy";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="dataoperacji";
			$pola[$nazwa][0]="timestamp";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="kwota";
			$pola[$nazwa][0]="decimal(8,2)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="0";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="nazwanadawcy";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="nrreferencyjny";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="0";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="typ";
			$pola[$nazwa][0]="enum('bankowy','reczny','inny','gotowka')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'inny'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="nazwapliku_id";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);

		}
		#endregion
		//----------------------------------------------------------------------------------------------------
	}
}//end if
else
	die("Class exists: ".__FILE__);
?>