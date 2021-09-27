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
			$content_text = "";//title is in function
			$template_class_name = $this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template == "admin") || ($this->page_obj->template == "index") )
			{
				switch($this->page_obj->target)
				{
					case "restore_password":
						$par1 = isset($_GET['par1']) ? $_GET['par1'] : "";
						$content_text .= $this->restore_password($par1);
						break;
					case "save_new_password":
						$current_pass = isset($_POST['current_pass']) ? $_POST['current_pass'] : "";
						$new_pass = isset($_POST['new_pass']) ? $_POST['new_pass'] : "";
						$new_pass_confirmation = isset($_POST['new_pass_confirmation']) ? $_POST['new_pass_confirmation'] : "";
						$content_text .= $this->save_new_password($current_pass, $new_pass, $new_pass_confirmation);
						break;
					case "change_password_form":
						$content_text .= $this->change_password_form("");
						break;
					default:
						$content_text .= "No access is available";
						break;
				}
			}
			else if( $this->page_obj->template == "change_password" )
			{
				switch($this->page_obj->target)
				{
					case "new_password_save":
						$new_pass = isset($_POST['r_password']) ? $_POST['r_password'] : "";
						$new_pass_confirmation = isset($_POST['r_password_conf']) ? $_POST['r_password_conf'] : "";
						$token = isset($_POST['token']) ? $_POST['token'] : "";
						$content_text .= $this->new_password_save($new_pass, $new_pass_confirmation, $token);
						break;
					case "new_password_form":
						$par1 = isset($_GET['par1']) ? $_GET['par1'] : "";
						$content_text .= $this->new_password_form($par1);
						break;
					default:
						$content_text .= "No access is available";
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
			$r_MySqlLogin = $this->page_obj->database_obj->get_handle();
			if(!$r_MySqlLogin)
				return -1;
			//mysql_select_db("presinfo");
			//pobranie hasla uzytkownika
			$r_wynik=$r_MySqlLogin->query("select ido,haslo from ".get_class($this)." where email_opiekun='$r_login';");
			//jezeli puste zglosic info o zmiane hasla jezeli brak to wylot na zewnatrz
			if($r_access == "no" && $r_MySqlLogin->affected_rows != 1)
			{
				return 0;
			}
			else
			{
				list($ido,$pas) = $r_wynik->fetch_row();
				if($pas == "")
				{
					//nie można się zalogować bez hasła
					//$r_ido = $ido;
					//$_SESSION['r_ido'] = $r_ido;
					//setcookie("r_access","oki",time() + session_cache_expire());
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
		#region change password form
		private function change_password_form($message)
		{
			$rettext = "";
			//--------------------
			$rettext .= "<p class='title'>ZMIANA HASŁA:</p>";
			//--------------------
			$rettext .= "<p style = 'color:red;' >".$message."<br /><br /></p>";
			//--------------------
			$rettext .= "	<style>
									div.wiersz{float:left;clear:left;}
									div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
									div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
								</style>";
			//--------------------
			$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},save_new_password'>";
			$rettext .= "<div style='overflow:hidden;'>";

			$rettext .= "	<div class='wiersz'>
									<div class='formularzkom1'>
										Aktualne hasło:
									</div>
									<div class='formularzkom2'>
										<input type='password' name='current_pass' />
									</div>
								</div>";

			$rettext .= "	<p><br /><br /><br /></p>";

			$rettext .= "	<div class='wiersz'>
									<div class='formularzkom1'>
										Nowe hasło:
									</div>
									<div class='formularzkom2'>
										<input type='password' name='new_pass' />
									</div>
								</div>";

			$rettext .= "	<div class='wiersz'>
									<div class='formularzkom1'>
										potwierdź:
									</div>
									<div class='formularzkom2'>
										<input type='password' name='new_pass_confirmation' />
									</div>
								</div>";

			$rettext .= "	<div class='wiersz'>
									<div class='formularzkom1'>
										&#160;
									</div>
									<div class='formularzkom2'>
										<input type='submit' name='' title='Zapisz' value='Zapisz' style='font-size:20px;'/>
									</div>
								</div>";
			$rettext .= "</div>";
			$rettext .= "</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region save_new_password
		private function save_new_password($current_pass, $new_pass, $new_pass_confirmation)
		{
			$rettext = "";
			//--------------------
			// check new_pass and new_pass_confirmation
			if( $new_pass != $new_pass_confirmation )
			{
				$rettext .= $this->change_password_form("Potwierdzenie nowego hasła jest niepoprawne. Spróbuj ponownie.");
				return $rettext;
			}

			// check current_pass
			$sql_result = $this->page_obj->database_obj->get_data("select ido from opiekunowie where haslo = PASSWORD('$current_pass') and ido = ".$this->get_login_ido().";");
			if( !$sql_result )
			{
				$rettext .= $this->change_password_form("Aktualne hasła jest niepoprawne. Spróbuj ponownie.");
				return $rettext;
			}

			// change password
			$sql_result = $this->page_obj->database_obj->execute_query("update opiekunowie set haslo = PASSWORD('$new_pass') where ido = ".$this->get_login_ido().";");
			if( !$sql_result )
			{
				$rettext .= $this->change_password_form("Błąd zapisu do bazy. Spróbuj ponownie za chwilę.");
				return $rettext;
			}

			$rettext .= "Hasło zmienione";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region cos
		private function restore_password($adres_email)
		{
			$rettext = "";
			//--------------------
			// sprawdzam czy podany adres e-mail istnieje w bazei
			$sql_result = $this->page_obj->database_obj->get_data("select ido from opiekunowie where email_opiekun = '$adres_email';");
			if( !$sql_result )
			{
				$rettext .= "Adres $adres_email nie został zarejestrowany w naszym systemie.";
			}
			else
			{
				list($ido) = $sql_result->fetch_row();
				$sql_result = $this->page_obj->database_obj->execute_query("update opiekunowie set password_change_token = CONCAT(UNIX_TIMESTAMP(NOW()),'_','".uniqid(true)."') where ido = $ido;");
				if( !$sql_result )
				{
					$rettext .= "Wystąpił problem z zapisem do bazy danych. Proszę o kontakt z placówką w celu zmiany hasła.";
				}
				else 
				{
					$sql_result = $this->page_obj->database_obj->get_data("select password_change_token from opiekunowie where ido = $ido;");
					if( !$sql_result )
					{
						$rettext .= "Wystąpił problem z odczytem bazy danych. Proszę o kontakt z placówką w celu zmiany hasła.";
					}
					else
					{
						$adres_from = "platnosci@nzpe.pl";
						list($password_change_token) = $sql_result->fetch_row();
						$content = "W celu zmiany hasła do platnosci.nzpe.pl proszę otworzyć link: <a href='https:\\\\platnosci.nzpe.pl\opiekunowie,change_password,new_password_form,$password_change_token'>https:\\\\platnosci.nzpe.pl\opiekunowie,change_password,new_password_form,$password_change_token</a> i postępować zgodnie z instrukcjami.";
						if ( $this->page_obj->sendmail_obj->sendhtmlmessage_from($adres_from, $adres_email, "Zmiana hasła do platnosci.nzpe.pl", $content) )
						{
							$rettext .= "Link do zmiany hasła został wysłany na adres:<br />".$par1;
						}
						else
						{
							$rettext .= "Wystąpił problem z wysłaniem linku. Proszę o kontakt z placówką w celu zmiany hasła.";
						}
					}
				}
			}
			
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region new_password_form
		private function new_password_form($token)
		{
			$rettext = "";
			//--------------------

			// pobrać ido jak się nie uda to msg
			$sql_result = $this->page_obj->database_obj->get_data("select ido,UNIX_TIMESTAMP(NOW()) from opiekunowie where password_change_token = '$token';");
			if( !$sql_result )
			{
				$rettext .= "Nieprawidłowy link. <br />";
				$rettext .= "<a href='.'>Powrót do strony głównej</a>";
				return $rettext;
			}
			
			// sprawdzić czas wygaśnięcia
			list($ido,$current_time_stamp) = $sql_result->fetch_row();
			$token_array = explode("_", $token);
			//$rettext .= "$current_time_stamp - {$token_array[0]} = ".($current_time_stamp - $token_array[0]);
			if( ($current_time_stamp - $token_array[0]) > 86400) //24 * 60 * 60 = 86 400
			{
				$rettext .= "Link wygasł.<br />";
				$rettext .= "<a href='.'>Powrót do strony głównej</a>";
				return $rettext;
			}

			// jak wszystko ok to formularz do zmiany hasła z ukrytym tokenem
			$rettext .= "<form method='post' action='".get_class($this).",{$this->page_obj->template},new_password_save'>";
			$rettext .= "<input type='password' class='login_form_input' name='r_password' placeholder='nowe hasło' /> <br />";
			$rettext .= "<input type='password' class='login_form_input' name='r_password_conf' placeholder='powtórz' /> <br />";
			$rettext .= "<input type='submit' class='login_form_submit' value='zapisz' /> <br /><br />";
			$rettext .= "<p style='clear:both;width:300px;text-align:center;font-size:16px;'>$message</p>";
			$rettext .= "<input type='hidden' name='token' value='$token' />";
			$rettext .= "</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region new_password_save
		private function new_password_save($new_pass,$new_pass_confirmation,$token)
		{
			$rettext = "";
			//--------------------

			if( $new_pass == "" )
			{
				$rettext .= "Hasło nie może być puste.";
				$rettext .= $this->new_password_form($token);
				return $rettext;
			}

			if( strlen($new_pass) < 6 )
			{
				$rettext .= "Hasło musi mieć minimum 6 znaków.";
				$rettext .= $this->new_password_form($token);
				return $rettext;
			}

			//sprawdzić new_pass == $new_pass_confirmation
			if( ($new_pass != $new_pass_confirmation) )
			{
				$rettext .= "Potwierdzenie hasła nieprawidłowe";
				$rettext .= $this->new_password_form($token);
				return $rettext;
			}

			// pobrać ido jak się nie uda to msg
			$sql_result = $this->page_obj->database_obj->get_data("select ido,UNIX_TIMESTAMP(NOW()) from opiekunowie where password_change_token = '$token';");
			if( !$sql_result )
			{
				$rettext .= "Nieprawidłowy link.<br />";
				$rettext .= "<a href='.'>Powrót do strony głównej</a>";
				return $rettext;
			}
			
			// sprawdzić czas wygaśnięcia
			list($ido,$current_time_stamp) = $sql_result->fetch_row();
			$token_array = explode("_", $token);
			//$rettext .= "$current_time_stamp - {$token_array[0]} = ".($current_time_stamp - $token_array[0]);
			if( ($current_time_stamp - $token_array[0]) > 86400) //24 * 60 * 60 = 86 400
			{
				$rettext .= "Link wygasł.<br />";
				$rettext .= "<a href='.'>Powrót do strony głównej</a>";
				return $rettext;
			}

			//jak wszystko ok to zapis nowego hasła do bazy
			$new_pass = $this->page_obj->text_obj->domysql($new_pass);
			$sql_result = $this->page_obj->database_obj->execute_query("update opiekunowie set password_change_token = '', haslo = PASSWORD('$new_pass') where ido = $ido;");
			if( !$sql_result )
			{
				$rettext .= "Wystąpił problem z zapisem do bazy danych. Proszę o kontakt z placówką w celu zmiany hasła.<br />";
				$rettext .= "<a href='.'>Powrót do strony głównej</a>";
			}
			else
			{
				$rettext .= "Hasło zostało zmienione.<br />";
				$rettext .= "<a href='.'>Powrót do strony głównej</a>";
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