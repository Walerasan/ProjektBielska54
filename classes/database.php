<?php
if(!class_exists('database'))
{
    /*
    $_SESSION['database']['operationtime'] - function execution time in ms
	 	
    CREATE DATABASE labnode CHARACTER SET UTF8;
    CREATE USER user1@localhost IDENTIFIED BY 'password1';
    GRANT ALL PRIVILEGES ON *.* TO 'user1'@localhost IDENTIFIED BY 'password1'; 
    FLUSH PRIVILEGES;
    */
    class database
	{
	    var $database_cfg_obj;		
		var $connection_handel;		
		var $connection_error;
		var $show_query_without_error;
		var $show_query_with_error;
		var $log_type;
		var $report_message;
		var $connection_state_message;
		var $event_number;
		//----------------------------------------------------------------------------------------------------
		public function __construct($database_cfg_obj,$logtype)
		{
			$start_time=microtime(true);
			
			$this->database_cfg_obj=$database_cfg_obj;			
			$this->log_type=$logtype;//all, error, good 
		
			$_SESSION['database']['operationtime']=microtime(true)-$start_time;
		}
		//----------------------------------------------------------------------------------------------------
		public function get_handle()
		{
		    return $this->connection_handel;
		}
		//----------------------------------------------------------------------------------------------------
		public function connect()
		{
			//polaczenie z baza
			//zwraca 1 gdy wszystko ok
			//zwraca -1 gdy nie udało się przyłączyć do serwer
			//zwraca -2 gdy nie udało się wybrać bazy danych
			$old_reporting=error_reporting(0);
			$this->connection_handel=mysqli_connect($this->database_cfg_obj->get_server(),$this->database_cfg_obj->get_login(),$this->database_cfg_obj->get_password(),$this->database_cfg_obj->get_database_name());
			if( ($this->connection_handel==null) || ($this->connection_handel==false) || ($this->connection_handel->connect_errno!=0) )
			{			    
			    $ret=-1;
			    $this->connection_handel=null;
			}
			else
			{
			    $this->connection_handel->query("SET NAMES utf8");
			    $ret=1;
			    $this->save_log('',$this->checkpoint(debug_backtrace()),true);				
			}
			error_reporting($old_reporting);
			$this->connection_error=$ret;
			//--------------------
			switch($ret)
			{
				case 1:
				    $this->connection_state_message="Połączenie ok";
					break;
				case -2:
				    $this->connection_state_message="Błąd wyboru bazy danych";
					break;
				case -1:
				    $this->connection_state_message="Błąd połączenia do serwera";
					break;
			}
		}
		//--------------------------------------------------------
		public function disconnect()
		{
		    if($this->connection_handel)
			{
			    $this->connection_handel->close();
			    $this->connection_handel=null;
			}
		}
		//----------------------------------------------------------------------------------------------------
		public function show_report_message()
		{
			$rettext="";
			if( (!$this->connection_handel) && ($this->show_query_with_error) )
			    $rettext .= "<div style='clear:both;'>Brak połączenia z bazą: ".$this->connection_state_message."</div>";
				$rettext .= "<div style='clear:both;'>".$this->report_message."</div>";
			return $rettext;
		}
		//--------------------------------------------------------
		public function get_data($zapytanie,$sql=1,$err=1) 
		{
		    $this->generate_event_number();
			$przed=microtime(true);
			if($this->connection_handel)
			{
			    $rettext=$this->connection_handel->query($zapytanie);
			    if($this->connection_handel->affected_rows>0)
				{
				    if($this->show_query_without_error)
					    $this->message($zapytanie,$sql,$err);
					$_SESSION['baza']['operationtime']=microtime(true)-$przed;
					$this->save_log($zapytanie,$this->checkpoint(debug_backtrace()));
					return $rettext;
				}
				$this->message($zapytanie,$sql,$err);
				$_SESSION['baza']['operationtime']=microtime(true)-$przed;
				$this->save_log($zapytanie,$this->checkpoint(debug_backtrace()));
			}
			return 0;
		}
		//----------------------------------------------------------------------------------------------------
		public function execute_query($zapytanie,$sql=1,$err=1) 
		{
		    $this->generate_event_number();
			$rettext=false;
			//tak mozna to zapisac do wywolania
			//echo("Dodaje baner: ".$baza->operacja("insert into banery(link)values('costam');"));
			$przed=microtime(true);
			if($this->connection_handel)
			{
			    if($this->connection_handel->query($zapytanie))
				{
				    if($this->connection_handel->affected_rows>0)
					{
					    if($this->show_query_without_error)
						    $this->message($zapytanie,$sql,$err);
						$_SESSION['baza']['operationtime']=microtime(true)-$przed;
						$this->save_log($zapytanie,$this->checkpoint(debug_backtrace()));
						$rettext=true;
					}
					elseif($this->error()=="")//jezeli nie ma bledu to tez zwracam true
					{
					    if($this->show_query_without_error)
						    $this->message($zapytanie,$sql,$err);
						$_SESSION['baza']['operationtime']=microtime(true)-$przed;
						$this->save_log($zapytanie,$this->checkpoint(debug_backtrace()));
						$rettext=true;
					}
					else
					{
					    if($this->show_query_without_error)
						    $this->message($zapytanie,$sql,$err);
						$_SESSION['baza']['operationtime']=microtime(true)-$przed;
						$this->save_log($zapytanie,$this->checkpoint(debug_backtrace()));
						$rettext=false;
					}
				}
				else
				{
				    $this->message($zapytanie,$sql,$err);
					$_SESSION['baza']['operationtime']=microtime(true)-$przed;
					$this->save_log($zapytanie,$this->checkpoint(debug_backtrace()));
					$rettext=false;
				}
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function last_id()
		{
			$id="";
			$zapytanie="select last_insert_id();";
			$przed=microtime(true);
			$wynik=$this->get_data($zapytanie);
			if($wynik)
			    list($id)=$wynik->fetch_row();
			$_SESSION['baza']['operationtime']=microtime(true)-$przed;
			$this->save_log($zapytanie,$this->checkpoint(debug_backtrace()));
			return $id;
		}
		//----------------------------------------------------------------------------------------------------
		public function install($nazwatablicy,$pola,$sql=1,$err=1,$primarykey="")
		{
		    if($this->connection_handel)
			{
			    $this->connection_handel->query("describe $nazwatablicy");
			    if($this->connection_handel->affected_rows<=0)
				{
					//jezeli nie ma to po prostu zalozyc
					$regulkatworzaca="create table $nazwatablicy (";
					foreach($pola as $i=>$val)
					{
						if($pola[$i][3]!="")$pola[$i][3]="default ".$pola[$i][3];
						$regulkatworzaca.="$i ".$pola[$i][0]." ".$pola[$i][1]." ".$pola[$i][2]." ".$pola[$i][3]." ".$pola[$i][4].",";
					}
					$regulkatworzaca=substr($regulkatworzaca,0,-1);
					if($primarykey!="")$regulkatworzaca.=",PRIMARY KEY ($primarykey)";
					$regulkatworzaca.=")ENGINE=InnoDB DEFAULT CHARSET=utf8;";
					
					if($this->connection_handel->query("$regulkatworzaca"))
					{
					    $this->message($regulkatworzaca,$sql,$err);
						//echo("Tablica zalożona");
					}
					else
					    $this->message($regulkatworzaca,$sql,$err);
				}
				else
				{
				    $wynik=$this->connection_handel->query("describe $nazwatablicy;");
				    if($this->connection_handel->affected_rows>0)
					{
						//pobieram pola z tablicy
					    while(list($pole,$typ,$puste,$key,$default,$extra)=$wynik->fetch_row())
						{
							if($puste=='NO')$puste='not null';else $puste='';
							if($key=='PRI')$key='primary key';else $key='';
							if($default=='NULL')$default='';
							if($default!="" && $default!="CURRENT_TIMESTAMP" && !is_numeric($default))$default="'$default'";
							
							$tablefields[$pole][0]=$typ;
							$tablefields[$pole][1]=$puste;
							$tablefields[$pole][2]=$key;
							$tablefields[$pole][3]=$default;
							$tablefields[$pole][4]=$extra;
							$tablefields[$pole][5]=$pole;
							
							//if($tablefields[$pole][1]=="not null")$tablefields[$pole][1]="";
						}
						//--------------------
						foreach($pola as $i=>$val)
						{
							if($pola[$i][1]=="")$pola[$i][1]="not null";
							//--------------------
							if($i!="" && $tablefields[$i][5]=="")
							{
								if($pola[$i][3]!="")$pola[$i][3]="default ".$pola[$i][3];
								$regulkatworzaca=$pola[$i][5]." ".$pola[$i][0]." ".$pola[$i][1]." ".$pola[$i][2]." ".$pola[$i][3]." ".$pola[$i][4];
								if($this->connection_handel->query("alter table $nazwatablicy add $regulkatworzaca;"))
								{
								    $this->message("alter table $nazwatablicy add $regulkatworzaca;",$sql,$err);
									//echo("nowa kolumna została dodana<br />");
								}
								else
									$this->message("alter table $nazwatablicy add $regulkatworzaca;",$sql,$err);
							}
							else
							{
								$tablefields[$i][6]=true;
								if($tablefields[$i][3]=="'0000-00-00 00:00:00'")$tablefields[$i][3]="0";
								if($tablefields[$i][0]!=$pola[$i][0] || $tablefields[$i][1]!=$pola[$i][1] || $tablefields[$i][2]!=$pola[$i][2] || $tablefields[$i][3]!=$pola[$i][3] || $tablefields[$i][4]!=$pola[$i][4])
								{
									//echo("<p style='font-weight:bold;color:red;' class='message'>".$tablefields[$i][0]."!=".$pola[$i][0]." || ".$tablefields[$i][1]."!=".$pola[$i][1]." || ".$tablefields[$i][2]."!=".$pola[$i][2]." || ".$tablefields[$i][3]."!=".$pola[$i][3]." || ".$tablefields[$i][4]."!=".$pola[$i][4]."</p>");
									$this->message($tablefields[$i][0]."!=".$pola[$i][0]." || ".$tablefields[$i][1]."!=".$pola[$i][1]." || ".$tablefields[$i][2]."!=".$pola[$i][2]." || ".$tablefields[$i][3]."!=".$pola[$i][3]." || ".$tablefields[$i][4]."!=".$pola[$i][4],$sql,$err);
									if($pola[$i][3]!="")$pola[$i][3]="default ".$pola[$i][3];
									$regulkatworzaca=$pola[$i][5]." ".$pola[$i][0]." ".$pola[$i][1]." ".$pola[$i][2]." ".$pola[$i][3]." ".$pola[$i][4];
									if($this->connection_handel->query("alter table $nazwatablicy change ".$tablefields[$i][5]." $regulkatworzaca;"))
									{
										$this->message("alter table $nazwatablicy change ".$tablefields[$i][5]." $regulkatworzaca;",$sql,$err);
										//echo("Kolumna zostala poprawiona");
									}
									else
									    $this->message("alter table $nazwatablicy change ".$tablefields[$i][5]." $regulkatworzaca;",$sql,$err);
								}
							}
						}
						//--------------------
						foreach($tablefields as $key=>$val)
						{
							if($val[6]!=true)
							{
								//usuwam pole
								$regulkatworzaca="alter table $nazwatablicy drop $key;";
								if($this->connection_handel->query($regulkatworzaca))
								    $this->message("Kolumna została usunięta $key $nazwatablicy<br />",$sql,$err);
								else
								    $this->message($regulkatworzaca,$sql,$err);
							}
						}
					}
				}
			}
			/*else
				echo("<span style='color:red;font-size:10pt;'>Brak połączenia z bazą danych</span>");*/			
		}
		//----------------------------------------------------------------------------------------------------
		public function error() 
		{
		    return $this->connection_handel->errno;
		}
		//----------------------------------------------------------------------------------------------------
		public function result_count() 
		{
		    return $this->connection_handel->affected_rows;
		}
		//--------------------------------------------------------
		private function message($zapytanie,$sql,$err)
		{
			//komunikat uzywany do wyswietlania bledow przy operacjach z baza oraz logowania do pliku
			//przygotowuje dane
			$zapytanie=str_replace(",",", ",$zapytanie);
			//zabezpieczam zapytanie przed wyswietleniem pozbywam sie <>
			$zapytanie=$this->zabezpieczznaki($zapytanie);
			//--------------------
			$error=$this->connection_handel->error;
			if($error=="")
			    if($this->result_count()>0)
			        $error=$this->result_count()." wierszy";
				else
					$error="0 wierszy";
			$error=$this->zabezpieczznaki($error);
			//--------------------
			//zmienna sql i err nadpisuja standardowe ustawienia
			if(($this->show_query_without_error && $sql) || ($this->show_query_with_error && $err))
			{
			    $this->report_message.=("<p style='padding-left:5px;color:black;font-size:8pt;border:1px dashed #aaaaaa;text-align:left;background:white;'>");
			    $this->report_message.="<b><u>$this->event_number</u></b><br />";
				if($this->show_query_without_error && $sql)
				    $this->report_message.=("» $zapytanie «<br />");
						
						if($this->show_query_with_error && $err)
						    $this->report_message.=("» $error «<br />");
					
						    $this->report_message.=("</p>");
				//echo("<b style='color:red;'>»$nrbledu</b> ");
			}
		}
		//----------------------------------------------------------------------------------------------------
		private function zabezpieczznaki($input)
		{
			$input=str_replace("<","&lt;",$input);
			$input=str_replace(">","&gt;",$input);
			return $input;
		}
		//----------------------------------------------------------------------------------------------------
		// tu sa funkcje tworzące logi
		//----------------------------------------------------------------------------------------------------
		private function RootPath()
		{
			$path_full = dirname($_SERVER['PHP_SELF']);
			$path_tab = explode("/", $path_full);
			$path_count = count($path_tab);
			$path="";
			for($i=2;$i<count($path_tab);$i++)
				$path.="../";
			if($path=="")$path="./";
			return $path;
		}
		//----------------------------------------------------------------------------------------------------
		private function checkpoint($trace)
		{
			//$trace=debug_backtrace();
			$caller=isset($trace[0])?$trace[0]:array();
			$owner=isset($trace[1])?$trace[1]:array();
			$file=isset($caller['file'])?$caller['file']:null;
			$class=isset($owner['class'])?$owner['class']:null;
			$function=isset($owner['function'])?$owner['function']:null;
			$line=isset($caller['line'])?$caller['line']:null;
			$type=isset($owner['type'])?$owner['type']:null;
			list($timeSub,$time)=explode(' ',microtime());
			$timeSub=preg_replace("/^[^.]\./","",$timeSub);
			$timeSub=substr(str_pad($timeSub,6,'0'),0,6);
			$rettext='['.date( 'Y-m-d H:i:s', $time ).'.'.$timeSub.']: '.($file === null?'':$file.' ').($line===null?'':sprintf('{%05d} ',$line)).($class===null?'':$class.$type).($function===null?'':$function.'()');
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function save_log($zapytanie,$checkpoint,$znacznik=false)
		{
			//--------------------
			$checkpoint .= " [$this->event_number] operationtime:[".substr($_SESSION['database']['operationtime'],0,8)."ms]";
			$jestkatalognalogi=true;
			if(!file_exists($this->RootPath()."/logs"))
				if(!mkdir($this->RootPath()."/logs"))
					$jestkatalognalogi=false;
			//--------------------	
			$sciezkadoplikuzlogamigood=$this->RootPath()."/logs/good_".date("Y-m-d").".log";
			$sciezkadoplikuzlogamierror=$this->RootPath()."/logs/error_".date("Y-m-d").".log";
			$sciezkadoplikuzlogamilongtime=$this->RootPath()."/logs/longtime.log";
			//--------------------
			if($this->connection_handel)
			{
			    $error=mysqli_error($this->connection_handel);
				if($error=="")
				    if($this->result_count()>0)
				        $error=$this->result_count()." wierszy";
					else
						$error="0 wierszy";
			}
			//--------------------
			if($jestkatalognalogi)
			{
			    $this->clear_logs($this->RootPath()."/logs");
				//-------------------- loguje długe czasowo zapytania
				if($_SESSION['database']['operationtime']>1)//1s
				{
					$plik=fopen($sciezkadoplikuzlogamilongtime,"a+");
					fwrite($plik,$checkpoint."\n$zapytanie\n$error\n\n");
					fclose($plik);
				}
				//--------------------
				if($this->log_type=="all")
				{
				    if($this->connection_handel)
					{
					    if(mysqli_error($this->connection_handel)=="")
						{
							$plik=fopen($sciezkadoplikuzlogamigood,"a+");
							if($znacznik)
								fwrite($plik,"--------------------$checkpoint---------------------\n");
							else
								fwrite($plik,$checkpoint."\n$zapytanie\n$error\n\n");
							fclose($plik);
						}
						if(mysqli_error($this->connection_handel)!="")
						{
							$plik=fopen($sciezkadoplikuzlogamierror,"a+");
							if($znacznik)
								fwrite($plik,"--------------------$checkpoint---------------------\n");
							else
								fwrite($plik,$checkpoint."\n$zapytanie\n$error\n\n");
							fclose($plik);
						}
					}
				}
				elseif($this->log_type=="error")
				{
				    if(mysqli_error($this->connection_handel)!="")
					{
						$plik=fopen($sciezkadoplikuzlogamierror,"a+");
						if($znacznik)
							fwrite($plik,"--------------------$checkpoint---------------------\n");
						else
							fwrite($plik,$checkpoint."\n$zapytanie\n$error\n\n");
						fclose($plik);
					}
				}
				elseif($this->log_type=="good")
				{
				    if(mysqli_error($this->connection_handel)=="")
					{
						$plik=fopen($sciezkadoplikuzlogamigood,"a+");
						if($znacznik)
							fwrite($plik,"--------------------$checkpoint---------------------\n");
						else
							fwrite($plik,$checkpoint."\n$zapytanie\n$error\n\n");
						fclose($plik);
					}
				}
			}
		}
		//----------------------------------------------------------------------------------------------------
		private function clear_logs($katalog)
		{
			$d = dir($katalog);
			while(false!==($entry = $d->read()))
			{
				if($entry!="." && $entry!="..")
				{
					if((filemtime($katalog."/".$entry)+15552000)<time())//usuwam logi starsze niż 6mc (60*60*24*30*6)(zakladam ze mc ma 30 dni nie bawie sie w 31 czy 28)
	   				unlink($katalog."/".$entry);
				}
			}
		}
		//----------------------------------------------------------------------------------------------------
		private function generate_event_number()
		{
			if(!isset($_SESSION['event_number']) || $_SESSION['event_number']=="") $nr=1; else $nr=$_SESSION['event_number'];
			$nr=(int)$nr+1;
			$_SESSION['event_number']=$nr;
			$this->event_number=$nr;
		}
		//----------------------------------------------------------------------------------------------------
		public function backup($pathtofile)
		{
			//użycie
			//$this->strona->baza->backup("./logs/kopiabazy.php.".date("Y_m_d_H_i"));
			//pobieram nazwy tablic
		    $tablice=$this->get_data("show tables;");
			if($tablice)
			{
				$plik=fopen($pathtofile,"w");
				while(list($tablica)=$tablice->fetch_row())
				{
					//tu jest filter tablic; Pomijam statystyki
					if($tablica=="statystyki_a" || $tablica=="statystyki_d" || $tablica=="statystyki_j" || $tablica=="statystyki_l" || $tablica=="statystyki_m" || $tablica=="statystyki_p")continue;
					$sklad="";
					//pobieram nazwy kolumn w tablicach
					$kolumny=$this->get_data("describe $tablica;");
					if($kolumny)
					{
						$i=0;
						$typy="";
						while(list($kolumna,$typ)=$kolumny->fetch_row())
						{//tworze sklad kolumn
							$sklad.=$kolumna.",";
							$typy[$i++]=substr($typ,0,strpos($typ,"("));
						}
						$sklad=substr($sklad,0,strlen($sklad)-1);
						//pobieram dane z bazy zgodnie z skladem
						$wynik=$this->get_data("select $sklad from $tablica;");
						if($wynik)
						{
						    while($liniawyniku=$wynik->fetch_row())
							{
								$wartosci="";
								foreach($liniawyniku as $key=>$wartosc)
								{
									$wartosc=str_replace("'","&quot;",$wartosc);
									$wartosc=str_replace("\"","&quot;",$wartosc);
									$wartosc=str_replace("\r\n","<br />",$wartosc);
									switch($typy[$key])
									{
										case "int":
											$wartosci.=$wartosc.",";
											break;
										case "integer":
											$wartosci.=$wartosc.",";
											break;
										case "varchar":
											$wartosci.="'$wartosc',";
											break;
										case "text":
											$wartosci.="'$wartosc',";
											break;
										case "timestamp":
											$wartosci.="'$wartosc',";
											break;
										default:
											$wartosci.="'$wartosc',";
									}
								}
								$wartosci=substr($wartosci,0,strlen($wartosci)-1);
								//echo("insert into $tablica($sklad)values($wartosci);<br />");
								//echo("mysqli_query(\"insert into $tablica($sklad)values($wartosci);\",\$polaczenie);\n");
								fwrite($plik,"mysqli_query(\"insert into $tablica($sklad)values($wartosci);\",\$this->connection_handel);\n");
							}
						}
					}
					else
						echo("Tablica $tablica nie zawiera kolumn<br />");
				}
				fclose($plik);
			}
			else
				echo("Brak tablic<br />");
			return true;
		}
		//----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>
