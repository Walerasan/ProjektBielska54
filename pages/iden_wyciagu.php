<?php
if( !class_exists("iden_wyciagu") )
{
	class iden_wyciagu
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj = $page_obj;
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
			$content_text = "";
			$template_class_name = $this->page_obj->template."_template";
			//--------------------
			if($this->page_obj->template == "index")
			{
				switch($this->page_obj->target)
				{
					case "usun":
						$idu = isset($_GET['par1']) ? $_GET['par1'] : 0;
						$idiw = isset($_GET['par2']) ? $_GET['par2'] : 0;
						$confirm = isset($_GET['par3']) ? $_GET['par3'] : "";
						$content_text = $this->usun($idu,$idiw,$confirm);
						break;
					case "form":
						$idu = isset($_GET['par1']) ? $_GET['par1'] : 0;
						$idiw = isset($_GET['par2']) ? $_GET['par2'] : 0;
						$content_text = $this->form($idu,$idiw);
						break;
					case "save":
						$idu = isset($_POST['idu']) ? $_POST['idu'] : 0;
						$idiw = isset($_POST['idiw']) ? $_POST['idiw'] : 0;
						$identyfikator = isset($_POST['identyfikator']) ? $_POST['identyfikator'] : 0;
						$content_text = $this->save($idu,$idiw,$identyfikator);
						break;
					default:
						$content_text = "";
					break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region formularz
		public function form($idu,$idiw)
		{
			$rettext = "";
			//--------------------
			$identyfikator = "";
			if($idu > 0)
			{
				if( isset($idiw) && is_numeric($idiw) && ($idiw > 0) )
				{
					$wynik = $this->page_obj->database_obj->get_data("select identyfikator from ".get_class($this)." where idiw = $idiw;");
					if($wynik)
					{
						list($identyfikator) = $wynik->fetch_row();
					}
				}
				//--------------------
				$rettext .= "<b style='font-size:20px;'><u>" . $this->page_obj->uczniowie->get_imie_uczniowie_nazwisko_uczniowie($idu) . "</u></b><br /><br /><br />";
				//--------------------
				$rettext .= "<style>
									div.wiersz{float:left;clear:left;}
									div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
									div.formularzkom2{width:260px;text-align:left;margin-right:5px;float:left;margin:2px;}
								</style>";
				$rettext .= "
					<form method='post' action='".get_class($this).",{$this->page_obj->template},save'>
						<div style='overflow:hidden;'>
							<div class='wiersz'><div class='formularzkom1'>Identyfikator płatności: </div><div class='formularzkom2'><input type='text' name='identyfikator' value='$identyfikator' style='width:250px;'/></div></div>
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
									<button title='Anuluj' style='font-size:20px;float:right;' type='button' onclick='window.location=\"uczniowie,{$this->page_obj->template},szczegoly,$idu\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idu' value='$idu' />
						<input type='hidden' name='idiw' value='$idiw' />
					</form>";
			}
			else
			{
				$rettext .= "Zły identyfikator ucznia.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region save
		private function save($idu,$idiw,$identyfikator)
		{
			$rettext = "";
			//--------------------
			if( ($idu != "") && is_numeric($idu) && ($idu > 0) )
			{
				$wynik = $this->page_obj->database_obj->get_data("select idu,idiw,usuniety from ".get_class($this)." where identyfikator = '$identyfikator';");
				if($wynik)
				{
					list($idu_s,$idiw_s,$usuniety) = $wynik->fetch_row();
					if($usuniety == 'nie')
					{
						$rettext .= "Ten identyfikator jest już przypisany do ".($this->page_obj->uczniowie->get_imie_uczniowie_nazwisko_uczniowie($idu_s)).".<br />";
						$rettext .= $this->page_obj->uczniowie->szczegoly($idu);
					}
					else
					{
						$zapytanie = "update ".get_class($this)." set usuniety = 'nie', idu = $idu, idw = 0 where idiw = $idiw_s;";//poprawa wpisu
						if($this->page_obj->database_obj->execute_query($zapytanie))
						{
							$rettext .= $this->page_obj->uczniowie->szczegoly($idu);
						}
						else
						{
							$rettext .= "Błąd zapisu <br />";
							$rettext .= $this->page_obj->uczniowie->szczegoly($idu);
						}
					}
				}
				else
				{
					if( ($idiw != "") && is_numeric($idiw) && ($idiw > 0) )
					{
						$zapytanie = "update ".get_class($this)." set identyfikator = '$identyfikator' where idiw = $idiw and idu = $idu;";//poprawa wpisu
					}
					else
					{
						$zapytanie = "insert into ".get_class($this)."(identyfikator, idu, idiw) values ('$identyfikator', $idu, $idiw)";//nowy wpis
					}

					if($this->page_obj->database_obj->execute_query($zapytanie))
					{
						$rettext .= $this->page_obj->uczniowie->szczegoly($idu);
					}
					else
					{
						$rettext .= "Błąd zapisu <br />";
						$rettext .= $this->page_obj->uczniowie->szczegoly($idu);
					}
				}
			}
			else
			{
				$rettext .= "Zły identyfikator ucznia.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list
		public function get_list($idu)
		{
			$rettext = array();
			//--------------------
			if( isset($idu) && is_numeric($idu) && ($idu > 0) )
			{
					$wynik = $this->page_obj->database_obj->get_data("select identyfikator, usuniety, idiw, idw from ".get_class($this)." where idu = $idu and usuniety = 'nie';");
					if($wynik)
					{
						while($row = $wynik->fetch_row())
						{
							$rettext[] = $row;
						};
					}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region list_idiw
		public function list_idiw()
		{
			$rettext = array();
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select idiw,idu,identyfikator from ".get_class($this)." where usuniety = 'nie' and idw = 0;");
			if($wynik)
			{
				while($row = $wynik->fetch_row())
				{
					$rettext[] = $row;
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region mark_idiw_assigned
		public function mark_idiw_assigned($idu,$idiw,$idw)
		{
			if( isset($idu) && is_numeric($idu) && ($idu > 0) )
			{
				if( isset($idiw) && is_numeric($idiw) && ($idiw > 0) )
				{
					if( isset($idw) && is_numeric($idw) && ($idw > 0) )
					{
						$this->page_obj->database_obj->execute_query("update ".get_class($this)." set idw = $idw where idu = $idu and idiw = $idiw;");
					}
				}
			}
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region usun
		private function usun($idu,$idiw,$confirm)
		{
			$rettext = "";
			//--------------------
			if( ($idu != "") && is_numeric($idu) && ($idu > 0) )
			{
				if( isset($idiw) && is_numeric($idiw) && ($idiw > 0) )
				{
					if($confirm=="yes")
					{
						//pobrać idw
						$wynik = $this->page_obj->database_obj->get_data("select idw from ".get_class($this)." where idu = $idu and idiw = $idiw;");
						if($wynik)
						{
							list($idw) = $wynik->fetch_row();
							if($idw > 0)
							{
								$this->page_obj->database_obj->execute_query("update wyciagi_uczniowie set usuniety = 'tak' where idw = $idw and status = 'auto_iden';");//, status = 'not_asigned', idw = 0
							}
						}
						$this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety = 'tak', idw = 0 where idu = $idu and idiw = $idiw;");
					}
				}
				$rettext .= $this->page_obj->uczniowie->szczegoly($idu);
			}
			else
			{
				$rettext .= "Zły identyfikator ucznia.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region insert_and_asign
		public function insert_and_asign($idu,$idw,$identyfikator)
		{
			$rettext = "";
			//--------------------
			$idiw = 0;
			//--------------------
			if( ($idu != "") && is_numeric($idu) && ($idu > 0) )
			{
				$wynik = $this->page_obj->database_obj->get_data("select idu,idiw,usuniety from ".get_class($this)." where identyfikator = '$identyfikator';");
				if($wynik)
				{
					list($idu,$idiw,$usuniety) = $wynik->fetch_row();
					if($usuniety != 'nie')
					{
						$zapytanie = "update ".get_class($this)." set usuniety = 'nie', idu = $idu, idw = 0 where idiw = $idiw_s;";//poprawa wpisu
						$this->page_obj->database_obj->execute_query($zapytanie);
					}
				}
				else
				{
					$zapytanie = "insert into ".get_class($this)."(identyfikator, idu, idiw) values ('$identyfikator', $idu, $idiw)";//nowy wpis
					if($this->page_obj->database_obj->execute_query($zapytanie))
					{
						$idiw = $this->page_obj->database_obj->last_id();
					}
				}

				if( $idiw > 0 )
				{
					$this->page_obj->wyciagi->processing_iden_wyciagu();
					$this->mark_idiw_assigned($idu,$idiw,$idw);
				}
			}
			
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_liste_wyciagow_dla_ucznia
		public function get_liste_wyciagow_dla_ucznia($idu)
		{
			$rettext = array();
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select idw from ".get_class($this)." where idu = $idu and usuniety = 'nie';");
			if($wynik)
			{
				while(list($idw) = $wynik->fetch_row())
				{
					$rettext[] = (int)$idw;
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
			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy = get_class($this);
			$pola = array();
			
			//definicja tablicy
			$nazwa="idiw";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie','zablokowany')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="identyfikator";
			$pola[$nazwa][0]="varchar(250)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="idu";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="idw";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="0";//default
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