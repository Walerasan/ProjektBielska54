<?php
if(!class_exists('media_browser'))
{
    class media_browser
	{ 
	    var $rozdzielacz;
	    var $rozdzielacz2;
	    var $katalog;
	    var $katalogmini;
	    var $katalogkosz;
	    var $katalogkoszmini;
	    //----------------------------------------------------------------------------------------------------
	    public function __construct($page_obj)
		{
		    $this->definicjabazy($page_obj);
			$this->rozdzielacz="#$#";
			$this->rozdzielacz2="#@#";
			$this->katalog=$page_obj->create_directory("./media/mediabrowser",debug_backtrace());
			$this->katalogmini=$page_obj->create_directory("./media/mediabrowser/mini",debug_backtrace());
			$this->katalogkosz=$page_obj->create_directory("./media/mediabrowser/kosz",debug_backtrace());
			$this->katalogkoszmini=$page_obj->create_directory("./media/mediabrowser/kosz/mini",debug_backtrace());
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
		                    $rettext="";
		                    break;
		            }
		        }
		    }
		    else if($page_obj->template=="index")
		    {
		        switch($page_obj->target)
		        {
		            default:
		                $rettext.=$page_obj->$template_class_name->get_content($page_obj,"");
		                break;
		        }
		    }
		    else if($page_obj->template=="raw")
		    {
		        switch($page_obj->target)
				{
					case "deletepicture":
						$par1=isset($_GET['par1'])?$_GET['par1']:'';
						$par2=isset($_GET['par2'])?$_GET['par2']:'';
						$par3=isset($_GET['par3'])?$_GET['par3']:'';
						$par4=isset($_GET['par4'])?$_GET['par4']:'';
						$rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->deletepicture($page_obj,$par1,$par2,$par3,$par4));
						break;
					case "pictureslist":
						$typ=isset($_POST['typ'])?$_POST['typ']:'';
						$ref=isset($_POST['ref'])?$_POST['ref']:'';
						$id=isset($_POST['id'])?$_POST['id']:'';
						$trescdowyszukania=isset($_POST['par1'])?$_POST['par1']:'';
						$$aktualnailosc=isset($_POST['par2'])?$_POST['par2']:'';
						$rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->pictureslist($page_obj,$typ,$ref,$id,$trescdowyszukania,$$aktualnailosc));
						break;
					case "savepicture":
					    $rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->savepicture($page_obj));
						break;
					case "findpictures":
					    $rettext.=$page_obj->$template_class_name->get_content($page_obj,$this->findpictures());
						break;
					default:
					    $rettext=$page_obj->$template_class_name->get_content($page_obj,"");
						break;
				}
			}
			
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function pictureslist($page_obj,$type,$ref,$id,$trescdowyszukania,$aktualnailosc)
		{
			$filtr="";
			//--------------------
			if($type!="")
				$filtr.=" and kategoria='$type' ";
				/*if($type=='image')
					$filtr.=" and (typpliku='jpg' or typpliku='gif' or typpliku='jpeg' || typpliku='png') ";
				else
					$filtr.=" and typpliku<>'jpg' and typpliku<>'gif' and typpliku<>'jpeg' and typpliku<>'png' ";*/
			if(isset($trescdowyszukania) && $trescdowyszukania!="" && $trescdowyszukania!="undefined")
			{
				$trescdowyszukania=$this->strona->string->domysql($trescdowyszukania);
				$filtr.=" and podpis like '%$trescdowyszukania%' ";
			}
			//--------------------
			if(!isset($aktualnailosc) || $aktualnailosc=="" || !is_numeric($aktualnailosc) || $aktualnailosc<0)$aktualnailosc=0;
			$iloscnastronie=30;
			$page_obj->database_obj->get_data("select idtbz,typpliku,podpis,nazwa from ".get_class($this)." where usuniety='nie' $filtr order by idtbz desc");
			$iloscwszystkich=$page_obj->database_obj->results_count();
			//--------------------
			$rettext.="	<hr style='width:650px;border-color:gray;' />
									<form target='uploadPhotoIframe' onsubmit='return true;' method='post' enctype='multipart/form-data' action='mediabrowser,raw,savepicture' style='margin-top:5px;'>
										Plik: <input type='file' name='zdjecie[]' style='color:white;' multiple='multiple'/>
										<!--<input type='checkbox' name='nalozlogo' style='color:white;' checked='checked'/> nałóż logo, -->
										podpis: <input type='text' name='podpiszdjecia' style='background:#cccccc;border:0px;'/>
										<input type='submit' value='wyślij na serwer' style='background:#cccccc;border:0px;'/>
										<input type='hidden' name='id' value='$id' />
										<input type='hidden' name='type' value='$type' />
										<input type='hidden' name='ref' value='$ref' />
									</form>
									<hr style='width:650px;border-color:gray;'/>
			
									<form target='uploadPhotoIframe' onsubmit='return true;' method='post' enctype='multipart/form-data' action='mediabrowser,raw,findpictures' style='margin-top:5px;'>
										Wyszukaj: <input type='text' name='szukajzdjecia' value='$trescdowyszukania' style='background:#cccccc;border:0px;'/> <input type='submit' value='szukaj' style='background:#cccccc;border:0px;'/>
										<input type='hidden' name='id' value='$id' />
										<input type='hidden' name='type' value='$type' />
										<input type='hidden' name='ref' value='$ref' />
									</form>
									<hr style='width:650px;border-color:gray;' />
									<iframe style='border:0px solid orange;width:1px;height:1px;display:none;' src='' name='uploadPhotoIframe' id='uploadPhotoIframe'></iframe>
									<br />";
			//--------------------
			$wynik=$page_obj->database_obj->get_data("select idtbz,typpliku,podpis,nazwa from ".get_class($this)." where usuniety='nie' $filtr order by idtbz desc limit $aktualnailosc,$iloscnastronie");
			if($wynik)
			    while(list($idtbz,$typpliku,$podpis,$nazwa)=$wynik->fetch_row())
				{
					if($typpliku=="jpg" || $typpliku=="jpeg" || $typpliku=="gif" || $typpliku=="png")
					{
						if(file_exists($this->katalogmini."/".$idtbz.".".$typpliku))
							$ikona=$idtbz.".".$typpliku;
						else
							$ikona="zaslona.png";
					}
					else
					{
						$ikona=$this->ikonapliku($typpliku);
					}
					
					$rettext.="<div style='vertical-align:top;display:inline-block;position:relative;overflow:hidden;margin:10px;width:140px;'>
											<img src='./media/mediabrowser/tlozdjecia01.gif' alt='' style='display:block;'/>
											<div style='background:url(./media/mediabrowser/tlozdjecia02.gif);text-align:center;'>
												<div style='margin:auto;width:124px;overflow:hidden;'>
													<a href='javascript:$ref.wstawzdjecie(\"$ikona\",\"$podpis\",\"$nazwa\")' title='$podpis' style='color:#cccccc;'>
														<img src='./media/mediabrowser/mini/$ikona' style='width:120px;border:1px solid #333333;display:inline-block;'/><br />
														$podpis<br />
														$nazwa
													</a>
												</div>
											</div>
											<img src='./media/mediabrowser/tlozdjecia03.gif' alt='' style='display:block;'/>
											<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"mediabrowser,raw,deletepicture,$ref,$id,$idtbz,$ikona\",uploadPhotoIframe)' >
												<img src='./media/mediabrowser/zamknijoff.png' alt='' style='position:absolute;right:10px;top:10px;' onmouseover='this.src=\"./media/mediabrowser/zamknij.png\"' onmouseout='this.src=\"./media/mediabrowser/zamknijoff.png\"' />
											</a>
										</div>";
					/*if($typpliku=="jpg" || $typpliku=="jpeg" || $typpliku=="gif" || $typpliku=="png")
					{
						if(file_exists($this->katalogmini."/".$idtbz.".".$typpliku))
							$rettext.=$idtbz.".".$typpliku.$this->rozdzielacz.$podpis.$this->rozdzielacz.$nazwa.$this->rozdzielacz.$idtbz.$this->rozdzielacz2;
						else
							$rettext.="zaslona.png".$this->rozdzielacz.$podpis.$this->rozdzielacz.$nazwa.$this->rozdzielacz.$idtbz.$this->rozdzielacz2;
					}
					else
					{
						$rettext.=$this->ikonapliku($typpliku).$this->rozdzielacz.$podpis.$this->rozdzielacz.$nazwa.$this->rozdzielacz.$idtbz.$this->rozdzielacz2;
					}*/
				}
			$rettext.="<br /><br />";
			if($aktualnailosc>=$iloscnastronie)
				$rettext.="<a href='javascript:parent.$ref.wykonajakcje(\"pictureslist\",\"$trescdowyszukania\",\"".($aktualnailosc-$iloscnastronie)."\")'><img src='./media/mediabrowser/left.png' alt='' /></a> ";
			$rettext.="<a href='javascript:$ref.anulujzaciemnienie()' style='color:white;font-size:10pt;font-weight:bold;'><img src='./media/mediabrowser/center.png' alt='' /></a>";
			if($aktualnailosc<=$iloscwszystkich-$iloscnastronie)
				$rettext.="<a href='javascript:parent.$ref.wykonajakcje(\"pictureslist\",\"$trescdowyszukania\",\"".($aktualnailosc+$iloscnastronie)."\")'><img src='./media/mediabrowser/right.png' alt='' /></a>";
			//--------------------
			
			return ($rettext);
		}
		//----------------------------------------------------------------------------------------------------
		private function ikonapliku($typpliku)
		{
			$dane="";
			//--------------------
			switch($typpliku)
			{
				case "xls":
				case "xlsx":
					$dane="xls_ico.png";
					break;
				case "doc":
				case "docx":
					$dane="doc_ico.png";
					break;
				case "pdf":
					$dane="pdf_ico.png";
					break;
				default:
					$dane="all_ico.png";
					break;
			}
			//--------------------
			$rettext="$dane";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function LoadFiles($dir)
		{
			$Files = array();
			$It =  opendir($dir);
			if (! $It)
				die('Cannot list files for ' . $dir);
			while ($Filename = readdir($It))
			{
				if ($Filename == '.' || $Filename == '..')
					continue;
				$LastModified = filemtime($dir . $Filename);
				$Files[] = array($Filename, $LastModified);
			}
			return $Files;
		}
		//----------------------------------------------------------------------------------------------------
		private function DateCmp($a, $b)
		{
			return ($a[1] > $b[1]) ? -1 : 1;
		}
		//----------------------------------------------------------------------------------------------------
		private function SortByDate(&$Files)
		{
			usort($Files, array($this, 'DateCmp'));
		}
		//----------------------------------------------------------------------------------------------------
		private function savepicture($page_obj)
		{
			$szerokoscminiatury=900;
			$wysokoscminiatury=675;
			//$szerokoscminiatury=180;
			//$wysokoscminiatury=135;
			$szerokosc=1024;
			$wysokosc=786;
			//--------------------
			$zdjecie=$_FILES['zdjecie'];
			$nalozlogo=$_POST['nalozlogo'];
			$podpiszdjecia=$_POST['podpiszdjecia'];
			$kategoria=$_POST['type'];
			$id=$_POST['id'];
			$ref=$_POST['ref'];
			//--------------------
			//$pic['tmp_name'][$key]
			
			foreach($zdjecie['tmp_name'] as $key=>$val)
			{
				if(is_uploaded_file($zdjecie['tmp_name'][$key]))
				{
					$typpliku=strtolower(strrpos($zdjecie['name'][$key],'.')>0?substr($zdjecie['name'][$key],strrpos($zdjecie['name'][$key],'.')+1):"");
					//dodaje załącznik do bazy
					if($page_obj->database_obj->execute_query("insert into ".get_class($this)."(nazwa,podpis,datadodania,typpliku,kategoria)values('".($zdjecie['name'][$key])."','$podpiszdjecia',now(),'$typpliku','$kategoria')"))
					{
					    $id=$page_obj->database_obj->last_id();
						//zapisuje zdjęcia
						if($kategoria=="image")
						{
						    $wynik=$page_obj->graphic_obj->savepictureinarray($zdjecie,$key,$this->katalog,$id);
							if($wynik[0]==1)
							{
							    $wynik4=$page_obj->graphic_obj->resizepicture($wynik[1],$wynik[1],$szerokosc,$wysokosc,false);
							    $wynik2=$page_obj->graphic_obj->resizepicture($wynik[1],$this->katalogmini."/".$wynik[2],$szerokoscminiatury,$wysokoscminiatury,false);
							}
							if($nalozlogo=="on")
							    $wynik3=$page_obj->graphic_obj->nalozlogo($wynik[1],$wynik[1],$this->strona->ustawienia->logonazdjecia);
						}
						else
						{//zapisuje zwykłe pliki
							copy($zdjecie['tmp_name'][$key],$this->katalog."/$id.$typpliku");
						}
					}
				}
			}
			
			$rettext.="<script type='text/javascript'>parent.$ref.wykonajakcje('pictureslist');</script>";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function findpictures()
		{
			$rettext="";
			//--------------------
			$kategoria=$_POST['type'];
			$id=$_POST['id'];
			$ref=$_POST['ref'];
			//--------------------
			$szukajzdjecia=$_POST['szukajzdjecia'];
			//$rettext.="<script type='text/javascript'>parent.mediabrowser.odswierz('".$szukajzdjecia."');</script>";
			$rettext="<script type='text/javascript'>parent.$ref.wykonajakcje('pictureslist','".$szukajzdjecia."');</script>";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function deletepicture($page_obj,$ref,$id,$idtbz,$zdjecie)
		{
			if($zdjecie!="zaslona.png")//nie uduwam zaslony
			{
				rename($this->katalog."/".$zdjecie,$this->katalogkosz."/".$zdjecie);
				rename($this->katalogmini."/".$zdjecie,$this->katalogkoszmini."/".$zdjecie);
			}
			$page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idtbz=$idtbz limit 1");
			//--------------------
			$rettext="<script type='text/javascript'>parent.$ref.wykonajakcje('pictureslist');</script>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function definicjabazy($page_obj)
		{
			//funkcja utrzymuje takasama strukture w bazie danych
			$nazwatablicy=get_class($this);
			//definicja tablicy
			$nazwa="idtbz";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="nazwa";
			$pola[$nazwa][0]="varchar(100)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="podpis";
			$pola[$nazwa][0]="varchar(250)";//przechowuje nazwe klasy
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
								
			$nazwa="datadodania"; //data uruchomienia
			$pola[$nazwa][0]="datetime";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie','moderator','zablokowany')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="typpliku";
			$pola[$nazwa][0]="varchar(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="kategoria";
			$pola[$nazwa][0]="varchar(20)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			//--------------------
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
		}
		//----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>