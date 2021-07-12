<?php
if(!class_exists('picture_gallery'))
{
    class picture_gallery
	{
		var $rozdzielacz="#@#";
		var $rozdzielaczzdjec="#$#";
		var $katgal;
		var $katgalmin;
		//----------------------------------------------------------------------------------------------------
		public function __construct($page_obj)
		{
			$this->katgal="./media/galerie";
			$this->katgal=$page_obj->create_directory($this->katgal,debug_backtrace());
			if($this->katgal)
			    $this->katgalmin=$page_obj->create_directory($this->katgal."/mini",debug_backtrace());
			$this->definicjabazy($page_obj);
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_content($page_obj)
		{
		    $rettext="";
		    $template_class_name=$page_obj->template."_template";
		    //--------------------
		    if($page_obj->template=="admin")
		    {
		        if($page_obj->users->is_login())
                {
                    switch($page_obj->target)
                    {
					   default:
					       break; 
					}
				}
		    }
		    else if($page_obj->template=="index")
		    {
		        switch($page_obj->target)
		        {
				    default:
				        $rettext.=$page_obj->$template_class_name->get_content($page_obj,"Empty");
						break;
				}
		    }
		    else if($page_obj->template=="raw")
		    {
		        switch($page_obj->target)
		        {
				    case "zapisz":
				        $rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->zapisz($page_obj,$_FILES['zdjecie'],$_POST['idg'],$_POST['jsobiect'],$_POST['nalozlogo']));
                        break;
					case "dodaj":
					    $rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->formularzgalerii($_GET['par1'],$_GET['par1']));
						break;
					case "wstawgalerie":
					    $rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->wstawgalerie($_GET['par1'],$_GET['par2']));
						break;
					case "zdjeciezgaleri":
					    $rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->zdjeciezgaleri($_GET['par1'],$_GET['par2']));
						break;
					default:
					    $rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->listagaleriadmin($page_obj,$_GET['par1']));
						break;
				}
		    }
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function listagaleriadmin($page_obj,$jsobiect)
		{
			$rettext .= "<button title='dodaj nową' type='button' onclick='$jsobiect.pobierzgalerie(\"dodaj\")'>dodaj nową</button><br />";
			$wynik=$page_obj->database_obj->get_data("select idg,tytul from ".get_class($this)." where usuniety='nie'");
			if($wynik)
			{
			    while(list($idg,$tytul)=$wynik->fetch_row())
				{
					$rettext.=$tytul.$this->rozdzielaczzdjec.$idg.$this->rozdzielaczzdjec;
					$wynik2=$page_obj->database_obj->get_data("select z.idgz,z.plik from ".get_class($this)."_z z, ".get_class($this)."_lgz lgz where z.usuniety='nie' and lgz.usuniety='nie' and z.idgz=lgz.idgz and lgz.idg=$idg limit 5");
					if($wynik2)
					{
						while(list($idgz,$plik)=$wynik2->fetch_row())
						{
							$rettext.=$this->katgal."/mini/".$plik.$this->rozdzielaczzdjec;
						}
					}
					//usuwam znacznik z końca zdjęci
					$rettext=substr($rettext,0,strlen($rettext)-3);
					$rettext.=$this->rozdzielacz;
				}
				//usuwam znacznik z konca wszystkich galerii
				$rettext=substr($rettext,0,strlen($rettext)-3);
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function formularzgalerii($jsobiect,$idg)
		{
			$rettext .= "
			<div style='background:white;overflow:hidden;width:300px;margin:auto;margin-top:50px;color:black;'>
				<form method='post' onsubmit='$jsobiect.pobierzgalerie(\"zapisz\",$jsobiect.formtorawpost(this));return false;' name='formularzname' enctype='multipart/form-data'>
					<input type='hidden' name='MAX_FILE_SIZE' value='5242880' />
					<b style='color:red;'>Sumarycznie wielkość plików maksymalnie do ".ini_get('post_max_size')."B ! </b><br />
					<div id='blokzdjec'>
						Zdjęcie: <input type='file' name='zdjecie[]' multiple='multiple'/><br />
					</div>
					<button title='dodaj kolejne' type='button' onclick='$jsobiect.dodajpolefile($id)'>dodaj kolejne</button><br />
					Nałóż logo: <input type='checkbox' name='nalozlogo' checked='checked' />
					<input type='submit' name='zapisz' value='zapisz' />
					<input type='hidden' name='idg' value='$idg'/> 
					<input type='hidden' name='jsobiect' value='$jsobiect'/> 
				</form>
			</div>";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function zapisz($page_obj,$pliki,$idg,$jsobiect,$nalozlogo)
		{
			$tytul="galeria";
			if($idg!="" && is_numeric($idg) && $idg>0)
			{
				$uaktualnienie=true;
				//uaktualniam galerię
			}
			else
			{
				$uaktualnienie=false;
				//zapisuje nową galerię
				if($page_obj->database_obj->execute_query("insert into ".get_class($this)."(tytul,dataw)values('$tytul',now())"))
				    $idg=$page_obj->database_obj->last_id();
				else
				{
					$idg=0;
					$rettext.=$page_obj->formaterror($page_obj->language_obj->pobierz('bladzapisudobazynowyrekord',true));
				}
			}
			//zapisuje zdjęcia do galerii
			if($idg!="" && is_numeric($idg) && $idg>0)
			{
				foreach($pliki[tmp_name] as $key=>$val)
				{
					//dodaje plik do bazy
				    if($page_obj->database_obj->execute_query("insert into ".get_class($this)."_z(dataw)values(now())"))
					{
					    $idgz=$page_obj->database_obj->last_id();
					    $wynik=$page_obj->graphic_obj->savepictureinarray($pliki,$key,$this->katgal,"$idg_$idgz");
						if($wynik[0]==1)
						{
						    $$page_obj->database_obj->execute_query("update ".get_class($this)."_z set plik='{$wynik[2]}' where idgz=$idgz");
						    $rettext.=$page_obj->formatok("{$wynik[1]}");
							//zmieniam rozmiar
						    $wynik2=$page_obj->graphic_obj->resizepicture($this->katgal."/".$wynik[2],$this->katgal."/".$wynik[2],800,600,false);
							//tworzę miniaturkę
						    $wynik3=$page_obj->graphic_obj->croppicture($this->katgal."/".$wynik[2],$this->katgalmin."/".$wynik[2],75,56);
							//nakładam logo
							if($nalozlogo=="on")
							    $wynik4=$page_obj->graphic_obj->nalozlogo($this->katgal."/".$wynik3[2],$this->katgal."/".$wynik3[2],$page_obj->server_cfg_obj->logonazdjecia);
							
							if(!$uaktualnienie)
							    $page_obj->database_obj->execute_query("insert into ".get_class($this)."_lgz(idg,idgz)values($idg,$idgz)");
						}
						else
						{
						    $rettext.=$page_obj->formaterror("{$wynik[1]}");
							$page_obj->database_obj->execute_query("delete from ".get_class($this)."_z where idgz=$idgz");
						}
					}
					else
					    $rettext.=$page_obj->formaterror("Błąd zapisu zdjęcia w bazie.");
				}
			}
			$rettext.=$this->listagaleriadmin($page_obj,$jsobiect);
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function wstawgalerie($page_obj,$jsobiect,$idg)
		{
		    $wynik2=$page_obj->database_obj->get_data("select z.idgz,z.plik from ".get_class($this)."_z z, ".get_class($this)."_lgz lgz where z.usuniety='nie' and lgz.usuniety='nie' and z.idgz=lgz.idgz and lgz.idg=$idg");
			if($wynik2)
			{
			    $ilosczdjec=$page_obj->database_obj->result_count();
				$firstpic=true;
				$licznikminiatur=0;
				while(list($idgz,$plik)=$wynik2->database_obj->fetch_row($wynik2))
				{
					//--------------------
					if($licznikminiatur<5)
						if($firstpic)
						    $zdjecia.="<img id='miniaturka_".$idg."_".$licznikminiatur."' src='".$page_obj->server_cfg_obj->adresstrony.$this->katgal."/mini/".$plik."' style='margin-left:3px;margin-right:3px;border:1px solid red;cursor:hand;cursor:pointer;' />";
						else
						    $zdjecia.="<img id='miniaturka_".$idg."_".$licznikminiatur."' src='".$page_obj->server_cfg_obj->adresstrony.$this->katgal."/mini/".$plik."' style='margin-left:3px;margin-right:3px;border:1px solid white;cursor:hand;cursor:pointer;' />";
					
					if($firstpic)
					{
					    $lightboxgal.="<a id='zdjecie$licznikminiatur' href='".$page_obj->server_cfg_obj->adresstrony.$this->katgal."/".$plik."'></a>\n";
					    $zdjecie="<div id='ramkanazdjecie$idg' style='display:table-cell;vertical-align:middle;text-align:center;width:445px;height:356px;overflow:hidden;'><a href='".$page_obj->server_cfg_obj->adresstrony.$this->katgal."/".$plik."' rel='lightbox[galeria$idg]'><img src='".$page_obj->server_cfg_obj->adresstrony.$this->katgal."/".$plik."' style='margin-bottom:3px;width:445px;'/></a></div>";
						$firstpic=false;
					}
					else
					    $lightboxgal.="<a id='zdjecie$licznikminiatur' href='".$page_obj->server_cfg_obj->adresstrony.$this->katgal."/".$plik."' rel='lightbox[galeria$idg]'></a>\n";
					
					    $miniatury.="<a id='miniaturka_link_".$idg."_$licznikminiatur' href='".$page_obj->server_cfg_obj->adresstrony.$this->katgal."/mini/".$plik."'></a>\n";
					$licznikminiatur++;
					
				}
			}
			//--------------------
			$rettext="<div style='width:445px;text-align:center;'>
					<script  type='text/javascript'>var obiektgaleri$idg=new LabNode.galeria(\"ramkanazdjecie$idg\",$ilosczdjec,$idg);</script>
					$zdjecie 
					$zdjecia
					$miniatury
					<table cellspacing='0' cellpadding='0' style='width:445px;height:43px;'>
						<tr>
							<td style='width:1px;'><img src='{$page_obj->server_cfg_obj->adresstrony}/media/ikony/galeria/galkropkipion.gif' alt='' style='display:block;'/></td>
							<td style='background:url({$page_obj->server_cfg_obj->adresstrony}/media/ikony/galeria/gaktloprzyciskow.gif);text-align:center;'>
								<a href='javascript:obiektgaleri$idg.napoczatek();'><img src='{$page_obj->server_cfg_obj->adresstrony}/media/ikony/galeria/galrewind.gif' alt='' /></a>
								<a href='javascript:obiektgaleri$idg.poprzednie();'><img src='{$page_obj->server_cfg_obj->adresstrony}/media/ikony/galeria/galbefore.gif' alt='' /></a>
								<a href='javascript:obiektgaleri$idg.nastepne();'><img src='{$page_obj->server_cfg_obj->adresstrony}/media/ikony/galeria/galnext.gif' alt='' /></a>
								<a href='javascript:obiektgaleri$idg.nakoniec();'><img src='{$page_obj->server_cfg_obj->adresstrony}/media/ikony/galeria/galforward.gif' alt='' /></a>
							</td>
							<td style='width:1px;'><img src='{$page_obj->server_cfg_obj->adresstrony}/media/ikony/galeria/galkropkipion.gif' alt='' style='display:block;'/></td>
						</tr>
					</table>
					$lightboxgal
					</div>";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function zdjeciezgaleri($page_obj,$idg,$nrzdjecia)
		{
		    $wynik2=$page_obj->database_obj->get_data("select z.idgz,z.plik from ".get_class($this)."_z z, ".get_class($this)."_lgz lgz where z.usuniety='nie' and lgz.usuniety='nie' and z.idgz=lgz.idgz and lgz.idg=$idg limit $nrzdjecia,1");
			if($wynik2)
			{
			    list($idgz,$plik)=$wynik2->fetch_row();
			    $rettext.=$page_obj->server_cfg_obj->adresstrony.$this->katgal."/".$plik;
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		//----------------------------------------------------------------------------------------------------
		private function definicjabazy($page_obj)
		{
			//funkcja utrzymuje takasama strukture w bazie danych
			$nazwatablicy=get_class($this);
			//definicja tablicy
			$nazwa="idg";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="tytul";
			$pola[$nazwa][0]="varchar(100)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="dataw";
			$pola[$nazwa][0]="datetime";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//----------------------------------------------------------------------------------------------------
			//funkcja utrzymuje takasama strukture w bazie danych
			$nazwatablicy=get_class($this)."_z";
			//definicja tablicy
			$nazwa="idgz";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="plik";
			$pola[$nazwa][0]="varchar(30)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="dataw";
			$pola[$nazwa][0]="datetime";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//----------------------------------------------------------------------------------------------------
			//funkcja utrzymuje takasama strukture w bazie danych
			$nazwatablicy=get_class($this)."_lgz";
			//definicja tablicy
			$nazwa="idglgz";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="idg";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idgz";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			//----------------------------------------------------------------------------------------------------
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
		}
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>