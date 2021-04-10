<?php
if(!class_exists('typy_oplat'))
{
    class typy_oplat
    {
        var $page_obj;
        //----------------------------------------------------------------------------------------------------
        public function __construct($page_obj)
        {
            $this->page_obj=$page_obj;
            $this->definicjabazy();
        }
        //----------------------------------------------------------------------------------------------------
        public function __destruct()
        {
        }
        //----------------------------------------------------------------------------------------------------
        public function get_content()
        {
            $rettext="";
            $template_class_name=$this->page_obj->template."_template";
            //--------------------
            if($this->page_obj->template=="admin")
            {
                switch($this->page_obj->target)
                {
                    case "przywroc":
                        $idto=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idto'])?$_POST['idto']:0);
                        $confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
                        $rettext=$this->page_obj->$template_class_name->get_content($this->page_obj,$this->restore($idto,$confirm));
                        break;
                    case "usun":
                        $idto=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idto'])?$_POST['idto']:0);
                        $confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
                        $rettext=$this->page_obj->$template_class_name->get_content($this->page_obj,$this->delete($idto,$confirm));
                        break;
                    case "zapisz":
                        $idto=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idto'])?$_POST['idto']:0);                        
                        $nazwa=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
                        $rettext=$this->page_obj->$template_class_name->get_content($this->page_obj,$this->add($idto,$nazwa));                        
                        break;
                    case "formularz":                                                
                        $idto=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idto'])?$_POST['idto']:0);
                        $nazwa=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
                        $rettext=$this->page_obj->$template_class_name->get_content($this->page_obj,$this->form($idto,$nazwa));
                        break;
                    case "lista":
                    default:
                        $rettext=$this->page_obj->$template_class_name->get_content($this->page_obj,$this->lista());
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
            $wynik=$this->page_obj->database_obj->get_data("select idto,nazwa,usuniety from ".get_class($this).";");
            if($wynik)
            {
                $rettext.="<script type='text/javascript' src='./js/opticaldiv.js'></script>";
                $rettext.="<script type='text/javascript' src='./js/potwierdzenie.js'></script>";                
                $rettext.="<table style='width:100%;font-size:10pt;' cellspacing='0'>";
                $rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>						
						<td>nazwa</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>						
					</tr>";
                $lp=0;
                while(list($idto,$nazwa,$usuniety)=$wynik->fetch_row())
                {
                    $lp++;
                    //--------------------
                    if($usuniety=='nie')
                    {
                        $operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",admin,usun,$idto,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:15px;'/></a>";
                    }
                    else
                  {
                        $operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",admin,przywroc,$idto,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:15px;'/></a>";
                    }
                    //--------------------
                    $rettext.="
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idto' onmouseover=\"setopticalwhite50('wiersz$idto')\" onmouseout=\"setoptical0('wiersz$idto')\">
							<td>$lp</td>							
							<td>$nazwa</td>
							<td style='text-align:center;'><a href='".get_class($this).",admin,formularz,$idto'><img src='./media/ikony/edit.png' alt='' style='height:15px;'/></a></td>
							<td style='text-align:center;'>$operacja</td>							
						</tr>
					";
                }
                $rettext.="</table>";                
            }
            else
            {
                $rettext.="<br />Brak wpisów<br />";
            }
            //--------------------
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function form($idto,$nazwa)
        {
            $rettext="";
            //--------------------
            $_SESSION['antyrefresh']=false;
            //--------------------
            if($idto!="" && is_numeric($idto) && $idto>0)
            {
                $wynik=$this->page_obj->database_obj->get_data("select nazwa from ".get_class($this)." where usuniety='nie' and idto=$idto");
                if($wynik)
                {
                    list($nazwa)=$wynik->fetch_row();
                }
            }
            //--------------------
            $nazwa=$this->page_obj->text_obj->doedycji($nazwa);
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
							<div class='wiersz'><div class='formularzkom1'>Nazwa: </div><div class='formularzkom2'><input type='text' name='nazwa' value='$nazwa' style='width:800px;'/></div></div>                            
							<div class='wiersz'>
                                <div class='formularzkom1'>&#160;</div>
                                <div class='formularzkom2'>
                                    <input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
                                    <button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",admin,lista\"'>Anuluj</button>
                                </div>
                            </div>
						</div>
						<input type='hidden' name='idto' value='$idto' />						
					</form>";
            //--------------------
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function add($idto,$nazwa)
        {
            $rettext = "";
            //--------------------
            // zabezpieczam dane
            //--------------------
            $nazwa = $this->page_obj->text_obj->domysql($nazwa);            
            //--------------------
            if( ($idto != "") && is_numeric($idto) && ($idto > 0) )
            {
                $zapytanie="update ".get_class($this)." set nazwa='$nazwa' where idto=$idto;";//poprawa wpisu
            }
            else
           {
                $zapytanie="insert into ".get_class($this)."(nazwa)values('$nazwa')";//nowy wpis
            }
            //--------------------
            if(!$_SESSION['antyrefresh'])
            {
                if($this->page_obj->database_obj->execute_query($zapytanie))
                {
                    $_SESSION['antyrefresh']=true;
                    $rettext.="Zapisane<br />";
                    $rettext.=$this->lista();
                }
                else
              {
                    $rettext.="Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
                    $rettext.=$this->form($idto,$nazwa);
                }
            }
            else
           {
               $rettext.=$this->lista();
            }
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function delete($idto,$confirm)
        {
            $rettext="";
            //--------------------
            if($confirm=="yes")
            {
                if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idto=$idto;"))
                {
                    //$rettext.="<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
                    $rettext.=$this->lista();
                }
                else
                {
                    $rettext.="<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
                    $rettext.=$this->lista();
                }
            }
            else
           {
                $rettext.="This operation need confirm.";
            }
            //--------------------
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function restore($idto,$confirm)
        {
            $rettext="";
            //--------------------
            if($confirm=="yes")
            {
                if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idto=$idto;"))
                {
                    //$rettext.="<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
                    $rettext.=$this->lista();
                }
                else
                {
                    $rettext.="<span style='font-weight:bold;color:red;'>Błąd przywracania</span><br />";
                    $rettext.=$this->lista();
                }
            }
            else
            {
                $rettext.="This operation need confirm.";
            }
            //--------------------
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
        public function get_list()
        {
            $rettext=array();
            //--------------------
            $wynik=$this->page_obj->database_obj->get_data("select idto,nazwa from ".get_class($this)." where usuniety='nie';");
            if($wynik)
            {
                while(list($idto,$nazwa)=$wynik->fetch_row())
                {
                    $rettext[] = array((int)$idto,$nazwa);
                }
            }
            //--------------------
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
       public function get_name($idto)
        {
            $nazwa='';
            if($idto!="" && is_numeric($idto) && $idto>0)
            {
                $wynik=$this->page_obj->database_obj->get_data("select nazwa from ".get_class($this)." where usuniety='nie' and idto=$idto");
                if($wynik)
                {
                    list($nazwa)=$wynik->fetch_row();
                }
            }
            return $nazwa;
        }
        //----------------------------------------------------------------------------------------------------
        private function definicjabazy()
        {
            //funkcja utrzymuje taka sama strukture w bazie danych
            $nazwatablicy=get_class($this);
            $pola=array();
            
            //definicja tablicy
            $nazwa="idto";
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
            $this->page_obj->database_obj->install($nazwatablicy,$pola);
            unset($pola);
            //--------------------            
        }
        //----------------------------------------------------------------------------------------------------
    }
}//end if
else
    die("Class exists: ".__FILE__);
?>