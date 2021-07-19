<?php
if(!class_exists('powiadomienia'))
{
	class powiadomienia
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
			$content_text="<p class='title'>WYCIĄGI</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template == "admin") || ($this->page_obj->template == "index") )
			{
				switch($this->page_obj->target)
				{
					case "processing":
						$content_text .= $this->processing();
						break;
					default:
						break;
				}
			}
			else if ($this->page_obj->template == "raw")
			{
				switch($this->page_obj->target)
				{
					case "refresh":
					default:
						$content_text .= $this->refresh();
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------		
		#region insert
		public function insert($idsop)
		{
			$rettext = "";
			//--------------------
			$zapytanie="insert into ".get_class($this)."(idsop,date,status)values($idsop,now(),'nowe')";//nowy wpis
			//--------------------
			if($this->page_obj->database_obj->execute_query($zapytanie))
			{
				$rettext .= "Zapisane<br />";
			}
			else
			{
				$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------		
		#region set_status
		public function set_status($idpo,$new_status)
		{
			$result = false;
			if( ($new_status == "nowe") || ($new_status == "wyslane") || ($new_status == "error") )
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set status='$new_status' where idpo=$idpo;"))
				{
					$result=true;
				}
			}
			//--------------------
			return $result;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list
		public function get_list($idsop)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idpo,data,status from ".get_class($this)." where idsop=$idsop;");
			if($wynik)
			{
				while(list($idpo,$data,$status)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idpo, $data, $status);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_detail
		public function get_detail($idpo)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select data,status from ".get_class($this)." where idpo=$idpo;");
			if($wynik)
			{
				while(list($data,$status)=$wynik->fetch_row())
				{
					$rettext[] = array($data, $status);
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
			$this->processing();
			return "refresh";
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region processing
		private function processing()
		{
			$rettext = "Auto processing system <br />";
			//--------------------
			// znajdz opłaty do wysłania i wyślij e-maile.
			//--------------------
			// potrzebuje wszystkie idop i idu których nie ma w powiadomieniach.
			// SELECT idop,idu FROM `uczniowie_oplaty` WHERE iduop not in (select iduop from powiadomienia) and usuniety = 'nie';
			// wstawić je do powiadomien ze statusem nowe
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("SELECT iduop FROM uczniowie_oplaty WHERE iduop not in (select iduop from ".get_class($this).") and usuniety = 'nie';");
			if( $wynik )
			{
				while( list($iduop) = $wynik->fetch_row() )
				{
					//pobrać idu i ido
					$wynik2 = $this->page_obj->database_obj->get_data("SELECT idu,idop FROM uczniowie_oplaty WHERE iduop = $iduop;");
					if( $wynik2 )
					{
						while( list($idu,$idop) = $wynik2->fetch_row() )
						{
							//pobrać listę opiekunów dla idu
							foreach($this->page_obj->uczniowie_opiekunowie->get_ido($idu) as $ido)
							{
								$zapytanie_sql = "insert into ".get_class($this)." (iduop,status,ido) values ($iduop,'nowe',$ido)";
								if( $this->page_obj->database_obj->execute_query($zapytanie_sql) )
								{
								}
								else
								{
									$rettext .= "Error: $zapytanie_sql";
								}
							}
						}
					}
				}
			}
			//--------------------

			//--------------------
			// pobrać wszystkie powiadomienia ze statusem nowe i wysłać na nie e-mail
			//--------------------
			// SELECT * FROM `powiadomienia` WHERE status = 'nowe';
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("SELECT idpo,iduop,ido FROM ".get_class($this)." WHERE status = 'nowe';");
			if( $wynik )
			{
				while( list($idpo,$iduop,$ido) = $wynik->fetch_row() )
				{
					//pobrać idu i ido
					$wynik2 = $this->page_obj->database_obj->get_data("SELECT idu,idop FROM uczniowie_oplaty WHERE iduop = $iduop;");
					if( $wynik2 )
					{
						while( list($idu,$idop) = $wynik2->fetch_row() )
						{
							//pobrać listę opiekunów dla idu
							foreach($this->page_obj->uczniowie_opiekunowie->get_ido($idu) as $ido)
							{
								//wysłać e-mails
								if($this->page_obj->sendmail_obj->sendhtmlmessage($this->page_obj->opiekunowie->get_email_opiekun($ido),"Powiadomienie o nowej opłacie.","TODO: zrobić treść powiadomienia"))
								{
									// zamarkować że wysłany
									$zapytanie_sql = "update ".get_class($this)." set status = 'wyslane', data = now() where idpo = $idpo;";
									if( $this->page_obj->database_obj->execute_query($zapytanie_sql) )
									{
									}
									else
									{
										$rettext .= "Error: $zapytanie_sql";
									}
								}
								else
								{
									// zamarkować że error
									$zapytanie_sql = "update ".get_class($this)." set status = 'error', data = now() where idpo = $idpo;";
									if( $this->page_obj->database_obj->execute_query($zapytanie_sql) )
									{
									}
									else
									{
										$rettext .= "Error: $zapytanie_sql";
									}
								}
							}
						}
					}
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
			$nazwatablicy=get_class($this);
			$pola=array();
			
			//definicja tablicy
			$nazwa="idpo";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="iduop";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
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
			
			$nazwa="status";
			$pola[$nazwa][0]="enum('nowe','wyslane','error')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nowe'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="ido";
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
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
	}
}//end if
else
	die("Class exists: ".__FILE__);
?>