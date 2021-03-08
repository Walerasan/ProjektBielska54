<?
include_once("./configs/server_cfg.php");
include_once("./configs/database_cfg.php");
include_once("./classes/text.php");
include_once("./classes/language.php");
include_once("./classes/keywords.php");
include_once("./classes/database.php");
include_once("./classes/users.php");
//----------------------------------------------------------------------------------------------------
if(!class_exists('page'))
{
    class page
    {
        var $server_cfg_obj;
        var $database_cfg_obj;
        var $text_obj;
        var $language_obj;
        var $keywords_obj;
        var $database_obj;
        var $users_obj;
        
        var $statystyki;
        
        var $class;
        var $template;
        var $target;
        //----------------------------------------------------------------------------------------------------
        public function __construct()
        {
            //--------------------
            $this->server_cfg_obj=new server_cfg;
            $this->database_cfg_obj=new database_cfg;
            $this->text_obj=new text;
            
            //--------------------
            // get control variables
            //-------------------- 
            $this->class=(isset($_GET['class']) && $_GET['class']!="")?$_GET['class']:((isset($_POST['class']) && $_POST['class']!="")?$_POST['class']:$this->server_cfg_obj->class_start);            
            $this->template=(isset($_GET['template']) && $_GET['template']!="")?$_GET['template']:((isset($_POST['template']) && $_POST['template']!="")?$_POST['template']:$this->server_cfg_obj->template_start);
            $this->target=(isset($_GET['target']) && $_GET['target']!="")?$_GET['target']:((isset($_POST['target']) && $_POST['target']!="")?$_POST['target']:$this->server_cfg_obj->target_start);
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // connect to database
            //--------------------            
            $this->database_obj=new database($this->database_cfg_obj,$this->server_cfg_obj->logtype,true,true,true);
            $this->database_obj->connect();
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // last URL variable is to change the language
            //--------------------
            $url="";
            $value="";
            foreach($_GET as $key=>$value)$url.="$value,";
            $url=substr($url,0,strlen($url)-1);
            $jezyk=($value=="pl"||$value=="en"||$value=="de"||$value=="ru")?$value:((isset($_SESSION['language']) && $_SESSION['language']!="")?$_SESSION['language']:$this->server_cfg_obj->defaultlanguage);
            $_SESSION['language']=$jezyk;
            if($_SESSION['language']=="" && $value!="select_language")
            {
                $this->target="select_language";
            }
            //~~~~~~~~~~~~~~~~~~~~
                
            //--------------------
            // create language object
            //--------------------            
            $this->language_obj=new language();
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // create keyword obiect
            //--------------------            
            $this->keywords_obj=new keywords();
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // create users obiect
            //--------------------
            $this->users_obj=new users($this);
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // create pages object
            //--------------------            
            $fuse=100;
            $dirs_to_load[0]="./pages";
            $dirs_to_load[1]="./templates";
            
            foreach($dirs_to_load as $key=>$val)
            {
                $files=dir($val);            
                if($files)
                {
                    while(false!==($entry=$files->read()) && $fuse--)
                    {
                        if($entry!="." && $entry!=".." && $entry!="" && !is_dir("$val/$entry"))
                        {
                            //wczytuje plik php
                            $file_handle=fopen("$val/$entry",'r');
                            if(filesize("$val/$entry")>0)
                            {
                                $file_content=fread($file_handle,filesize("$val/$entry"));
                                //przeszukuje w celu wpisania jezykow
                                if(is_object($this->language_obj))
                                {
                                    $this->language_obj->zbierzselektory($file_content);
                                }
                            }
                            fclose($file_handle);
                            //inkluduje tylko poprawne pliki klas
                            include("$val/$entry");
                            $classname=substr($entry,0,strpos($entry,'.'));
                            $this->$classname=new $classname($this);
                        }
                    }
                    $files->close();
                }
            }
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // execute login
            //--------------------
            switch($this->target)
            {
                case "login":
                    if(isset($_POST['r_login']) && isset($_POST['r_password']))
                    {
                        $this->users_obj->login($this,$_POST['r_login'],$_POST['r_password']);
                    }
                    break;
                case "logout":
                    $this->users_obj->logout();
                    break;
            }
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // save into the statistics
            //--------------------
            is_object($this->statystyki)?$this->statystyki->dopisz():null;
            //~~~~~~~~~~~~~~~~~~~~
        }
        //----------------------------------------------------------------------------------------------------
        public function __destruct()
        {
            $this->database_obj->disconnect();
            //$this->syslog(debug_backtrace(),"czas wykonania:".(microtime(true)-$this->timestart)."\n");
        }        
        //----------------------------------------------------------------------------------------------------
        public function show()
        {
            //--------------------
            // get content from template
            //--------------------;
            $classname=$this->class;            
            $page_content=$this->$classname->get_content($this);
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // check xml
            //--------------------
            $xml_parser = xml_parser_create();
            if(!xml_parse($xml_parser,$page_content))
                $this->isxhtml=false;
            else
                $this->isxhtml=true;
            $xmlerrorcode=xml_error_string(xml_get_error_code($xml_parser));
            $xmlerrorline=xml_get_current_line_number($xml_parser);
            $xmlerrorbyte=xml_get_current_byte_index($xml_parser);
            xml_parser_free($xml_parser);
            //~~~~~~~~~~~~~~~~~~~~                 
                
            //--------------------
            // send header
            // uruchamiam strone jako xhtml albo nie - sterowanie w ustawienia.php oraz w zaleznosci od tego czy zawartosc jest zgodna z xml
            //--------------------
            $xhtml = preg_match('/application\/xhtml\+xml(?![+a-z])(;q=(0\.\d{1,3}|[01]))?/i',$_SERVER['HTTP_ACCEPT'], $xhtml) && (isset($xhtml[2])?$xhtml[2]:1) > 0 || strpos($_SERVER["HTTP_USER_AGENT"], "W3C_Validator")!==false || strpos($_SERVER["HTTP_USER_AGENT"], "WebKit")!==false;
            header('Content-Type: '.(($xhtml && $this->server_cfg_obj->xhtmlon && $this->isxhtml)?'application/xhtml+xml':'text/html').'; charset=utf-8');
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // prepare contenttype
            //--------------------
            if($this->isxhtml)
            {
                $rettext="<!DOCTYPE HTML PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
                        <html xmlns='http://www.w3.org/1999/xhtml' xml:lang='pl'>";
                $contenttype="<meta http-equiv='Content-Type' content='application/xhtml+xml; charset=utf-8' />";
            }
            else
           {
                $rettext="<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
                        <html>";
                $contenttype="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
            }
            //~~~~~~~~~~~~~~~~~~~~
            
            //--------------------
            // prepare content to display
            //--------------------
                //--------------------
                // head
                //--------------------
                $rettext.="                
			    <head>
				    $contenttype
					<meta http-equiv='Content-Language' content='pl' />
					<meta name='Description' content='".((is_object($this->{$this->class}) && method_exists($this->{$this->class},"description"))?$this->{$this->class}->description($this->zmk):((is_object($this->language_obj) && method_exists($this->language_obj,"pobierz"))?$this->language_obj->pobierz("description",false,false):""))."' />
					<meta name='Keywords' content='".$this->keywords_obj->get_keywords($this->target,$this->language_obj)."' />
					<meta name='rating' content='".$this->keywords_obj->get_rating($this->target,$this->language_obj)."' />
					<meta name='Author' content='".$this->server_cfg_obj->autor."' />
					<meta name='Revisit-after' content='".$this->server_cfg_obj->revisitafter."' />
					<meta name='robots' content='index,follow' />
					<meta http-equiv='Reply-To' content='".$this->server_cfg_obj->autoremail."' />
					<meta http-equiv='pragma' content='no-cache' />
					<meta http-equiv='cache-control' content='no-cache' />
					<meta http-equiv='Creation-Date' content='".$this->server_cfg_obj->createdate."' />
					<meta name='viewport' content='width=device-width, initial-scale=1.0' />
					<title>".((is_object($this->{$this->class}) && method_exists($this->{$this->class},"title"))?$this->{$this->class}->title($this->zmk):((is_object($this->language_obj) && method_exists($this->language_obj,"pobierz"))?$this->language_obj->pobierz("title",false,false):""))."</title>
					    
					<link rel='Stylesheet' type='text/css' href='./css/strona.css' />
					<link rel='Stylesheet' type='text/css' href='./css/lightbox.css' />";
				    
			    //--------------------
			    // favcicon
			    //--------------------
				    if(file_exists($this->server_cfg_obj->pathtofavcicon))
				{
				    $rettext.="
					<link rel='icon' href='".$this->server_cfg_obj->pathtofavcicon."' type='image/x-icon' />
					<link rel='shortcut icon' href='".$this->server_cfg_obj->pathtofavcicon."' type='image/x-icon' />";
				}
				//~~~~~~~~~~~~~~~~~~~~
					
				//--------------------
				//load js files
				//--------------------					
				$rettext.="<script type='text/javascript' src='./js/tinymce/tinymce.min.js'></script>";
				$rettext.="<script type='text/javascript' src='./js/lightbox/jquery-1.7.2.min.js'></script>";
				$rettext.="<script type='text/javascript' src='./js/lightbox/lightbox.js'></script>";
				$rettext.="\n".$this->ladujjs("./js");
				//~~~~~~~~~~~~~~~~~~~~
					
				$rettext.="</head>";
				//~~~~~~~~~~~~~~~~~~~~
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// page_content
			//--------------------				
			$rettext.="\r\n<body>\r\n";
			$rettext.=$page_content;
			
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// raports
			//--------------------
			/*$rettext.="<div class='raporty'>";
			if($this->server_cfg_obj->showerror)
			    $rettext.="<p class='phperrorblock'>{$this->parent->bledy}</p>";
			    //--------------------
			    $rettext.=$this->baza->showraport();
			    if(!$this->isxhtml)
			    {
			        if($this->server_cfg_obj->autoremail!="")
			        {
			            if($this->server_cfg_obj->sendmailwitherror)
			            {
			                include_once("./classes/sendmail.php");
			                $objsendmail=new sendmail;
			                if(!$objsendmail->sendsystemmessage($this->server_cfg_obj->autoremail,"Błąd na stronie: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],$this->parent->bledy."<br />\nBłąd składni xhtml: $xmlerrorcode line: $xmlerrorline offset: $xmlerrorbyte <b style='color:red;'>&lArr;&otimes;</b>\n\n".$this->showcode($zawartosc,$xmlerrorbyte)));
			            }
			        }
			        if($this->server_cfg_obj->showerror)
			        {
			            $rettext.="Błąd składni xhtml: $xmlerrorcode line: $xmlerrorline offset: $xmlerrorbyte <b style='color:red;'>&lArr;&otimes;</b>";
			            $rettext.=$this->showcode($zawartosc,$xmlerrorbyte);
			        }
			    }
			    $rettext.="</div>";//koniec div dla raporty*/
			    //--------------------
			$rettext.="\r\n</body>\r\n</html>";
		    //~~~~~~~~~~~~~~~~~~~~
					    
    	    //--------------------
    	    // return text to display
    	    //--------------------
    	    return $rettext;
    	    //~~~~~~~~~~~~~~~~~~~~
        }
        
        //----------------------------------------------------------------------------------------------------
        private function ladujjs($kat)
        {
            $rettext="";
            $d=dir($kat);
            if($d)
            {
                $bezpiecznik=100;
                while(false!==($entry=$d->read()) && $bezpiecznik--)
                {
                    if($entry!="." && $entry!="..")
                    {
                        if(is_dir("$kat/$entry"))
                        {
                            //podkatalogi muszę załadować ręcznie po przez plik js na przykład lightbox.js
                            //bo nie które pliki się powielają lub mają być ładowane tylko w wyjątkowych sytuacjach.
                        }
                        else
                            $rettext.="<script type='text/javascript' src='$kat/$entry'></script>\n";
                    }
                }
            }
            return $rettext;
        }
        //----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>