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
			$this->katalog=$page_obj->create_directory("./media/filehtml",debug_backtrace());
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
			$content_text="";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					case "dodajplik":
						if(isset($_FILES['filehtml']) && !empty($_FILES['filehtml'])){
							$content_text=$this->uploadfile($_FILES['filehtml']);
						}
					break;
					case "przetwarzanie":
						$content_text=$this->processingfile();
					break;
					case "przywroc":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->restore($idw,$confirm);
					break;
					case "usun":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->delete($idw,$confirm);
					break;
					case "zapisz":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$tytul=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['tytul'])?$_POST['tytul']:"");
						$data=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['data'])?$_POST['data']:"");
						$typ=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['typ'])?$_POST['typ']:"");
						$content_text=$this->add($idw,$tytul,$data,$typ);
					break;
					case "formularz":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$tytul=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['tytul'])?$_POST['tytul']:"");
						$data=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['data'])?$_POST['data']:"");
						$typ=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['typ'])?$_POST['typ']:"");
						$content_text=$this->form($idw,$tytul,$data,$typ);
					break;
					case "raporty":
						$content_text=$this->raporty();
					break;
					case "lista":
					default:
						$content_text=$this->lista();
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region lista
		public function lista()
		{
			$rettext="";
			//--------------------
			$rettext.="<button title='dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz\"'>Dodaj nowy</button> ";
			$rettext.="<button title='dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},raporty\"'>RAPORTY WYCIAGÓW</button><br />";
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idw,tytul,data,typ,usuniety from ".get_class($this).";");
			if($wynik)
			{
				$rettext.="<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext.="<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext.="<table style='width:100%;font-size:10pt;' cellspacing='0'>";
				$rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>numer konta</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=0;
				while(list($idw,$tytul,$data,$typ,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idw,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:15px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idw,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:15px;'/></a>";
					}
					//--------------------
					$rettext.="
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idw' onmouseover=\"setopticalwhite50('wiersz$idw')\" onmouseout=\"setoptical0('wiersz$idw')\">
							<td>$lp</td>
							<td>$tytul</td>
							<td>$data</td>
							<td>$typ</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idw'><img src='./media/ikony/edit.png' alt='' style='height:15px;'/></a></td>
							<td style='text-align:center;'>$operacja</td>
						</tr>";
				}
				$rettext.="</table>";
			}
			else
			{
				$rettext.="<br />Brak wpisów<br />";
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
			$rettext.="
					<form method='post' action='".get_class($this).",{$this->page_obj->template},zapisz'>
						<div style='overflow:hidden;'>							
							<div class='wiersz'><div class='formularzkom1'>Tytuł: </div><div class='formularzkom2'><input type='text' name='tytul' value='$tytul' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>Data: </div><div class='formularzkom2'><input type='text' name='data' value='$data' style='width:800px;'/></div></div>
							<div class='wiersz'>
								<div class='formularzkom1'>Typ: </div>
								<div class='formularzkom2'>
									<select name='typ' value='$typ' style='width:800px;'>
										<option value='bankowy'>bankowy</option>
										<option value='reczny'>ręczny</option>
										<option value='inny'>inny</option>
									</select>
								</div>
							</div>
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
									<button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idw' value='$idw' />
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
					$rettext.="Zapisane<br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext.="Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext.=$this->form($idw,$tytul,$data,$typ);
				}
			}
			else
			{
				$rettext.=$this->lista();
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region delete
		public function delete($idw,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idw=$idw;"))
				{
					//$rettext.="<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext.="<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
					$rettext.=$this->lista();
				}
			}
			else
			{
				$rettext.="This operation need confirm.";
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
					//$rettext.="<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext.="<span style='font-weight:bold;color:red;'>Błąd przywracania</span><br />";
					$rettext.=$this->lista();
				}
			}
			else
			{
				$rettext.="This operation need confirm.";
			}
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		#region input file to processing 
		public function processingfile()
		{
			$rettext="";
			//--------------------
			$rettext.="<h3>Przetwarzanie pliku HTML</h3><br>";
			$rettext.="<form method='post' action='".get_class($this).",{$this->page_obj->template},dodajplik' enctype='multipart/form-data'>";
			$rettext.="Pobierz HTML: <input type='file' name='filehtml'>";
			$rettext.="<br><input type='submit' name='submit' value='ZAŁADUJ HTML'>";
			$rettext.="</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region processing from HTML to SQL db
		public function przetwarzanie_htmlToSql($file)
		{
			$rettext="";
			$rettext.="<hr>";
        	$rettext.="<br>pobieram nazwę pliku: ".$file."<br>";
			/**
			* dokument przetwarza plik html i zapisuje do bazy
			* Hydrotrade Polska Rafał Płatkowski, Arkadiusz Waliczek
			*/

			//Tworzę nowy obiekt struktury pliku html "dom"
			$dom = new domDocument; 
			
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
			echo "Nr konta bankowego<b>".$nrkontaBaza.'</b><br/><br/>';
			$nrkontaBaza = $this->page_obj->text_obj->domysql($nrkontaBaza);

			//uchwyt do trzeciej tabeli
			$rowsTr3Tabela = $tables->item(2)->getElementsByTagName('tr');
			
			
			$tablicaData = [];
			foreach($rowsTr3Tabela as $tr){
				$cols = $tr->getElementsByTagName('td');
				if($cols->item(2)->nodeValue == "Przelew na rachunek"){		
					array_push($tablicaData,$cols->item(0)->nodeValue);//zawiera daty przelewów dla Przelewu na rachunek
				}
			}
			$dlugosc_tablicaData = sizeof($tablicaData)-1;
			$dataOd = $tablicaData[$dlugosc_tablicaData];
			$dataDo = $tablicaData[0];

			//------Dodaje do tablicy tymczasowej i porównuje je z tabelą stała wyciagi
			//-----------------------Tablica tymczasowa----------------------
						foreach($rowsTr3Tabela as $tr){
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
			$rettext.="<hr>Nazwa dokumentu: ".basename($file["name"])."<hr>";

			$uploadOk = 1;
			$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));     
			// Sprawdzam czy istnieje już plik o tej samej nazwie
			if (file_exists($target_file)) {
				$rettext.="<h5 class='warnigs'>Plik istnieje o podanej nazwie.</h5>";
				$uploadOk = 0;
			}
			// Sprawdzam rozmiar pliku
			if ($file["size"] > 500000) {
				$rettext.="<h5 class='warnigs'>Plik jest za duży......</h5>";
				$uploadOk = 0;
			}
			// Przetwarzam tylko pliki o rozszerzeniu html
			if($FileType != "html") {
				$rettext.="<h5 class='warnigs'>Przetwarzanie tylko dla plików o rozszerzeniu html.....</h5>";
				$uploadOk = 0;
			}
			// Sprawdzam jeżeli $uploadOk 1 to ok a jeżeli 0 to error
			if ($uploadOk == 0) {
				$rettext.="<h5 class='warnigs'>Nie można przesłać pliku.</h5>";
			} else {
				if (move_uploaded_file($file["tmp_name"], $target_file)) {
					$rettext.="Plik ". htmlspecialchars( basename( $file["name"])). " został przesłany.";
					$plik = htmlspecialchars( basename( $file["name"]));
					//uruchamiam funkcje do przetwarzania skryptu
					$this->przetwarzanie_htmlToSql($plik);
				//--------------------------------------------------------------------
				} else {
					$rettext.="<h5 class='warnigs'>błąd przesłania pliku na serwer.</h5>";
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region uploadfile
		public function raporty()
		{
			$rettext="";
			//--------------------
			// Raport 1 na podstawie kalendarza od daty do daty wybiera się zakres raportu: suma wpłat
			// Raport 2 zapisanie do formatu csv/xls/pdf jw.
			// Raport 3 suma wpłat dla nrreferencyjnego
			// Raport 4 wpłaty z danego rachunku + imie i nazwieko osoby wpłacającej (po przypisaniu rachunku do os wpłacajacej)

			$rettext="RAPORTY";

			return $rettext;
		}
		//-----------------------------------------------------------------------------------------------------
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
			$pola[$nazwa][0]="decimal";
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
			$pola[$nazwa][0]="enum('bankowy','reczny','inny')";
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
			$pola[$nazwa][0]="decimal";
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
			$pola[$nazwa][0]="enum('bankowy','reczny','inny')";
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