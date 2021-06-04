<?php
if(!class_exists('opiekunowie'))
{
	class opiekunowie
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
		#region get_cntent
		public function get_content()
		{
			$content_text="<p class='title'>OPIEKUNOWIE</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					default:
						$content_text.="No access is available";
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_imie_opiekun_nazwisko_opiekun
		public function get_imie_opiekun_nazwisko_opiekun($ido)
		{
			$imie_opiekun_nazwisko_opiekun='';
			if($ido!="" && is_numeric($ido) && $ido>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select CONCAT(imie_opiekun,' ',nazwisko_opiekun) from ".get_class($this)." where usuniety='nie' and ido=$ido");
				if($wynik)
				{
					list($imie_opiekun_nazwisko_opiekun)=$wynik->fetch_row();
				}
			}
			return $imie_opiekun_nazwisko_opiekun;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_telefon_opiekun
		public function get_telefon_opiekun($ido)
		{
			$telefon_opiekun='';
			if($ido!="" && is_numeric($ido) && $ido>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select telefon_opiekun from ".get_class($this)." where usuniety='nie' and ido=$ido");
				if($wynik)
				{
					list($telefon_opiekun)=$wynik->fetch_row();
				}
			}
			return $telefon_opiekun;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_email_opiekun
		public function get_email_opiekun($ido)
		{
			$email_opiekun='';
			if($ido!="" && is_numeric($ido) && $ido>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select email_opiekun from ".get_class($this)." where usuniety='nie' and ido=$ido");
				if($wynik)
				{
					list($email_opiekun)=$wynik->fetch_row();
				}
			}
			return $email_opiekun;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region login
		public function login($page_obj,$r_login,$r_haslo)
		{
			//zabespieczenie przed cudzoslowiami
			$r_login=str_replace("'","&quot;",$r_login);
			$r_login=str_replace("\"","&quot;",$r_login);
			$r_haslo=str_replace("'","&quot;",$r_haslo);
			$r_haslo=str_replace("\"","&quot;",$r_haslo);
			// funkcja dokonuje loginu przez ustawienie w cookie r_access na oki oraz w sesji r_ido na numer id uzytkownika
			// funckja zwraca
			// -3 jezeli jakis user nie posiada hasla
			// -2 jezeli jakis user jest zalogowany
			// -1 jezeli wystapil blad loginu do bazy /polaczenia z silnik->baza/
			//  0 jezeli blad loginu badz hasla
			//	1 gdy wszystko oki i zostal zalogowany
			$r_access=isset($_COOKIE['r_access'])?$_COOKIE['r_access']:'';
			$r_ido=isset($_SESSION['r_ido'])?$_SESSION['r_ido']:'';
			if($r_access=="" || $r_ido==-1 || $r_ido=="")$r_access="no";
			if($this->is_login())
				return -2;
			$r_MySqlLogin=$page_obj->database_obj->get_handle();
			if(!$r_MySqlLogin)
				return -1;
			//mysql_select_db("presinfo");
			//pobranie hasla uzytkownika
			$r_wynik=$r_MySqlLogin->query("select ido,haslo from ".get_class($this)." where email_opiekun='$r_login';");
			//jezeli puste zglosic info o zmiane hasla jezeli brak to wylot na zewnatrz
			if($r_access=="no" && $r_MySqlLogin->affected_rows!=1)
			{
				return 0;
			}
			else
			{
				list($ido,$pas)=$r_wynik->fetch_row();
				if($pas == "")
				{
					$r_ido=$ido;
					$_SESSION['r_ido']=$r_ido;
					setcookie("r_access","oki",time()+session_cache_expire());
					return -3;
				};
			}
			//jezeli nie puste to sprawdzamy
			$r_wynik=$r_MySqlLogin->query("select ido from ".get_class($this)." where email_opiekun='$r_login' and haslo=old_PASSWORD('$r_haslo');");
			if($r_MySqlLogin->affected_rows<1)//jezeli nie ma starego sprawdzamy nowe ?
			$r_wynik=$r_MySqlLogin->query("select ido from ".get_class($this)." where email_opiekun='$r_login' and haslo=PASSWORD('$r_haslo');");
			if($r_access=="no" && $r_MySqlLogin->affected_rows!=1)
			{
				return 0;
			};
			//-------------------------
			if($r_access=="no")
			{
				$r_ido=$r_wynik->fetch_row();
				$r_ido=$r_ido[0];
				$_SESSION['r_ido']=$r_ido;
				setcookie("r_access","oki",time()+session_cache_expire());
				$_SESSION['timeoflogon']=time();
				$page_obj->database_obj->execute_query("update ".get_class($this)." set ostatnielogowanie=now() where ido=$r_ido");
				return 1;
			};
			echo("out of range !!");
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region logout
		public function logout()
		{
			$_SESSION['r_ido']=-1;
			$_SESSION['timeoflogon']="";
			setcookie("r_access","no",time()+session_cache_expire());
			session_destroy();
			header("Location: .");
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region is_login
		public function is_login()
		{
			//jezeli jest zalogowany uzytkownik zwraca true jezeli nie zwraca false
			$r_ido=isset($_SESSION['r_ido'])?$_SESSION['r_ido']:'';
			if($r_ido!=-1 && $r_ido!="")
				return true;
			else
				return false;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_login_ido
		public function get_login_ido()
		{
			return isset($_SESSION['r_ido'])?$_SESSION['r_ido']:'';
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_login_imie_nazwisko
		public function get_login_imie_nazwisko()
		{
			$rettext = "";
			//--------------------
			$rettext .= $this->get_imie_opiekun_nazwisko_opiekun($this->get_login_ido());
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