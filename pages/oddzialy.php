<?php
if(!class_exists('oddzialy'))
{
    class oddzialy
    {
        //----------------------------------------------------------------------------------------------------
        public function __construct($page_obj)
        {
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
                switch($page_obj->target)
                {
                    case "zapisz":
                        $idod=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idod'])?$_POST['idod']:0);
                        $name=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['name'])?$_POST['name']:"");
                        $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->add($idod,$name));                        
                        break;
                    case "formularz":                                                
                        $idod=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idod'])?$_POST['idod']:0);
                        $name=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['name'])?$_POST['name']:"");
                        $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->form($idod,$name));
                        break;
                    case "lista":
                    default:
                        $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->lista());
                        break;
                }
            }
            //--------------------
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function lista()
        {
            $rettext="";
            //--------------------
            $rettext.="<button title='dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",admin,formularz\"'>Dodaj nowy</button><br />";
            //--------------------
            //--------------------
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function form($idod,$name)
        {
            $rettext="";
            //--------------------
            $_SESSION['antyrefresh']=false;
            //--------------------
            /*if($idod!="" && is_numeric($idod) && $idod>0)
            {
                $wynik=$this->strona->baza->pobierzdane("select tytul,rodzic,opis from ".get_class($this)."_opis where usuniety='nie' and idp=$idp");
                if($wynik)
                    list($tytul,$rodzic,$opis)=mysql_fetch_row($wynik);
            }*/
            //--------------------
            //TODO: zabezpieczyć dane do edycji
            //$opis=$this->strona->string->doedycji($opis);
            //--------------------
            $rettext="
                    <style>
                        div.wiersz{float:left;clear:left;}
                        div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
						div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
					</style>";
            $rettext.="
					<form method='post' action='".get_class($this).",admin,zapisz'>
						<div style='overflow:hidden;'>							
							<div class='wiersz'><div class='formularzkom1'>Nazwa: </div><div class='formularzkom2'><input type='text' name='tytul' value='$name' style='width:800px;'/></div></div>							
							<div class='wiersz'>
                                <div class='formularzkom1'>&#160;</div>
                                <div class='formularzkom2'>
                                    <input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
                                    <button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",admin,lista\"'>Anuluj</button>
                                </div>
                            </div>
						</div>
						<input type='hidden' name='idod' value='$idod' />						
					</form>";
            //--------------------
            //--------------------
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function add($idod,$name)
        {
            tu skonczylem
            $rettext="";
            //--------------------
            //zabezpieczam dane
            //$name=$this->strona->string->domysql($name);            
            //--------------------
            //--------------------
            if($idod!="" && is_numeric($idod) && $idod>0)
            {
                $zapytanie="update ".get_class($this)." set tytul='$tytul',rodzic=$rodzic,opis='$opis',telefon='$telefon',email='$email',www='$www' where idk=$idk;";//poprawa wpisu
            }
            else
           {
                //pobrac następną dostępną kolejność w drzewie
                $zapytanie="insert into ".get_class($this)."(tytul,rodzic,opis,kolejnosc,telefon,email,www)values('$tytul',$rodzic,'$opis',$kolejnosc,'$telefon','$email','$www')";//nowy wpis
             }
             //--------------------
             if(!$_SESSION['antyrefresh'])
                {
                    if($this->strona->baza->operacja($zapytanie))
                    {
                        $_SESSION['antyrefresh']=true;
                        $rettext.="Zapisane<br />";
                        $rettext.=$this->lista();
                    }
                    else
                    {
                        $rettext.="Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
                        $rettext.=$this->formularz($idk,$tytul,$rodzic,$idk2,$telefon,$email,$www);
                    }
                }
                else
                    $rettext.=$this->lista();
                    return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function delete($idod,$confirm)
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        public function get_list()
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        public function get_name($idod)
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        private function definicjabazy($page_obj)
        {
            //funkcja utrzymuje taka sama strukture w bazie danych
            $nazwatablicy=get_class($this);
            
            //definicja tablicy
            $nazwa="idod";
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
            
            $nazwa="nazwa";
            $pola[$nazwa][0]="varchar(50)";
            $pola[$nazwa][1]="";//null
            $pola[$nazwa][2]="";//key
            $pola[$nazwa][3]="";//default
            $pola[$nazwa][4]="";//extra
            $pola[$nazwa][5]=$nazwa;
                        
            //----------------------------------------------------------------------------------------------------
            $page_obj->database_obj->install($nazwatablicy,$pola);
            unset($pola);
            //--------------------            
        }
        //----------------------------------------------------------------------------------------------------
    }
}//end if
else
    die("Class exists: ".__FILE__);
?>