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
					case "przywroc":
						$idop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->restore($idop,$confirm);
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
						$content_text.=$this->zapisz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie);
					break;
					case "formularz_uczen":
						$idop = isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$nazwa = isset($_GET['par2'])?$_GET['par2']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
						$kwota = isset($_GET['par3'])?$_GET['par3']:(isset($_POST['kwota'])?$_POST['kwota']:"");
						$idto = isset($_GET['par4'])?$_GET['par4']:(isset($_POST['idto'])?$_POST['idto']:0);
						$selected_uczniowie = isset($_GET['par5'])?$_GET['par5']:(isset($_POST['selected_uczniowie'])?$_POST['selected_uczniowie']:0);
						$content_text.=$this->formularz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie);
						break;
					break;
					case "lista":
					default:
						$content_text.=$this->lista();
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
			$rettext .= "<button class='test' title='Dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz_uczen\"'>Dodaj nowy</button>&#160;";
			$rettext .= "<br />";
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idop,idto,nazwa,kwota,usuniety from ".get_class($this).";");
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
							<td style='text-align:right;padding-right:10px;color:#555555;'>$lp.</td>
							<td>$nazwa</td>
							<td>$kwota</td>
							<td>{$this->page_obj->typy_oplat->get_name($idto)}</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz_uczen,$idop'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a></td>
							<td style='text-align:center;'>$operacja</td>
						</tr>";
				}
				$rettext .= "</table>";
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
							<div class='wiersz'><div class='formularzkom1'>kwota: </div><div class='formularzkom2'><input type='text' name='kwota' value='$kwota' style='width:800px;'/></div></div>
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
					$rettext.=$this->lista();
				}
				else
				{
					$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext.=$this->form($idop,$idto,$nazwa,$kwota);
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
		public function delete($idop,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idop=$idop;"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
					$rettext.=$this->lista();
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
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idop=$idop;"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd przywracania</span><br />";
					$rettext.=$this->lista();
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
		//----------------------------------------------------------------------------------------------------
		//----------------------------------------------------------------------------------------------------
		#region formularz_uczen
		private function formularz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if( isset($idop) && ($idop != "") && is_numeric($idop) && ($idop > 0) )
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
					<form method='post' action='".get_class($this).",{$this->page_obj->template},zapisz_uczen'>
						<div style='overflow:hidden;'>
							<div class='wiersz'><div class='formularzkom1'>Nazwa: </div><div class='formularzkom2'><input type='text' name='nazwa' value='$nazwa' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>typ: </div><div class='formularzkom2'>{$this->create_select_field_from_typy_oplat($idto)}</div></div>
							<div class='wiersz'><div class='formularzkom1'>kwota: </div><div class='formularzkom2'><input type='text' name='kwota' value='$kwota' style='width:800px;'/></div></div>
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
		private function zapisz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie)
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
					$rettext .= "Zapisane<br />";
					#region save users
					if( ($idop == "") || !is_numeric($idop) || ($idop <= 0) )
					{
						$idop = $this->page_obj->database_obj->last_id();
					}
					$this->page_obj->uczniowie_oplaty->mark_delete($idop);
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
					$rettext.=$this->lista();
				}
				else
				{
					$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext.=$this->formularz_uczen($idop,$nazwa,$kwota,$idto,$selected_uczniowie);
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
			$pola[$nazwa][0]="decimal(5,2)";
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