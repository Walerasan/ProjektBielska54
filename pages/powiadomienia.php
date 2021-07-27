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
			$zapytanie="insert into ".get_class($this)."(idsop,date,status)values($idsop,DATE_ADD(now(), INTERVAL 30 MINUTE),'nowe')";//nowy wpis
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
		#region mark_delete
		public function mark_delete($idop)
		{
			//don't delete with status != nowe
			#region execute
			$wynik = $this->page_obj->database_obj->get_data("SELECT iduop FROM uczniowie_oplaty WHERE idop = $idop;");
			if( $wynik )
			{
				while( list($iduop) = $wynik->fetch_row() )
				{
					$this->page_obj->database_obj->execute_query("update ".get_class($this)." set status='usuniety' where iduop=$iduop and status = 'nowe';");
				}
			}
			#endregion
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region mark_oplata_usunieta_from_nowe
		public function mark_oplata_usunieta_from_nowe($idop)
		{
			//don't change to oplata_usunieta when status !=  usuniety
			#region execute
			$wynik = $this->page_obj->database_obj->get_data("SELECT iduop FROM uczniowie_oplaty WHERE idop = $idop;");
			if( $wynik )
			{
				while( list($iduop) = $wynik->fetch_row() )
				{
					$this->page_obj->database_obj->execute_query("update ".get_class($this)." set status='oplata_usunieta' where iduop=$iduop and status = 'nowe';");
				}
			}
			#endregion
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region mark_nowe_from_oplata_usunieta
		public function mark_nowe_from_oplata_usunieta($idop)
		{
			//don't change to oplata_usunieta when status !=  usuniety
			#region execute
			$wynik = $this->page_obj->database_obj->get_data("SELECT iduop FROM uczniowie_oplaty WHERE idop = $idop;");
			if( $wynik )
			{
				while( list($iduop) = $wynik->fetch_row() )
				{
					$this->page_obj->database_obj->execute_query("update ".get_class($this)." set status='nowe' where iduop=$iduop and status = 'oplata_usunieta';");
				}
			}
			#endregion
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region synchronize_powiadomienia
		public function synchronize($idop,$idu)
		{
			$rettext = "";
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("SELECT iduop,usuniety FROM uczniowie_oplaty WHERE idu = $idu and idop = $idop;");
			if( $wynik )
			{
				while( list($iduop,$usuniety) = $wynik->fetch_row() )
				{
					foreach($this->page_obj->uczniowie_opiekunowie->get_ido($idu) as $ido)
					{
						//check exists
						$wynik2 = $this->page_obj->database_obj->get_data("select idpo,status from ".get_class($this)." where iduop = $iduop and ido = {$ido[0]};");
						if($this->page_obj->database_obj->result_count() == 0)
						{
							$zapytanie_sql = "insert into ".get_class($this)." (iduop, status, ido,data) values ($iduop, 'nowe', {$ido[0]},DATE_ADD(now(), INTERVAL 30 MINUTE))";
							$rettext .= $zapytanie_sql . "<br />";
							if( $this->page_obj->database_obj->execute_query($zapytanie_sql) )
							{
							}
							else
							{
							}
						}
						else
						{
							list($idpo,$status) = $wynik2->fetch_row();
							if($status == "usuniety")
							{
								$this->page_obj->database_obj->execute_query("update ".get_class($this)." set status = 'nowe', data = DATE_ADD(now(), INTERVAL 30 MINUTE) where idpo = $idpo");
								$rettext .= "update ".get_class($this)." set status = 'nowe' where idpo = $idpo" . "<br />";
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
		#region processing
		private function processing()
		{
			$rettext = "Auto processing system <br />";
			//--------------------
			// znajdz opłaty do wysłania i wyślij e-maile.
			//--------------------

			//--------------------
			// pobrać wszystkie powiadomienia ze statusem nowe i wysłać na nie e-mail
			//--------------------
			// SELECT * FROM `powiadomienia` WHERE status = 'nowe';
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("SELECT idpo,iduop,ido FROM ".get_class($this)." WHERE status = 'nowe' and data < now();");
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
			$pola[$nazwa][0]="enum('nowe','wyslane','error','usuniety','oplata_usunieta')";
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