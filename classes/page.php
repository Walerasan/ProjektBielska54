<?php
include_once("./configs/server_cfg.php");
include_once("./configs/database_cfg.php");
include_once("./classes/text.php");
include_once("./classes/language.php");
include_once("./classes/keywords.php");
include_once("./classes/database.php");
include_once("./classes/users.php");
include_once("./classes/statistics.php");
include_once("./classes/graphic.php");
include_once("./classes/imagemagic.php");
include_once("./classes/admin.php");
include_once("./classes/subpages.php");
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
		var $users;
		var $statistics_obj;
		var $graphic_obj;
		var $imagemagic_obj;
		var $admin;
		var $subpage;
		
		var $page;
		var $template;
		var $target;
		
		var $token;
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
			$this->page=(isset($_GET['page']) && $_GET['page']!="")?$_GET['page']:((isset($_POST['page']) && $_POST['page']!="")?$_POST['page']:$this->server_cfg_obj->page_start);				
			$this->template=(isset($_GET['template']) && $_GET['template']!="")?$_GET['template']:((isset($_POST['template']) && $_POST['template']!="")?$_POST['template']:$this->server_cfg_obj->template_start);
			$this->target=(isset($_GET['target']) && $_GET['target']!="")?$_GET['target']:((isset($_POST['target']) && $_POST['target']!="")?$_POST['target']:$this->server_cfg_obj->target_start);
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// next token
			//--------------------
			$this->token=$_SESSION['token']=(!isset($_SESSION['token']) || $_SESSION['token']=="")?1:++$_SESSION['token'];
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// connect to database
			//--------------------				
			$this->database_obj=new database($this->database_cfg_obj,$this->server_cfg_obj->log_type,true,true,true);
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
			$this->users=new users($this);
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// create admin obiect
			//--------------------
			$this->admin=new admin($this);
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// create graphic obiect
			//--------------------
			$this->graphic_obj=new graphic();
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// create imagemagic obiect
			//--------------------
			$this->imagemagic_obj=new imagemagic();
			//~~~~~~~~~~~~~~~~~~~~

			//--------------------
			// create subpages obiect
			//--------------------
			$this->subpages=new subpages();
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
							$this->users->login($this,$_POST['r_login'],$_POST['r_password']);
					}
					break;
				case "logout":
					$this->users->logout();
					break;
			}
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// save into the statistics
			//--------------------
			$this->statistics_obj=new statistics($this);
			$this->statistics_obj->insert($this);
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
			//--------------------
			if( isset($this->{$this->page}) && (is_object($this->{$this->page})) && (method_exists($this->{$this->page},"get_content")) )
			{
				$page_content=$this->{$this->page}->get_content($this);
			}
			else
			{
				$page_content="Ta strona nie istnieje";
			}
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
					<meta name='Description' content='".(isset($this->{$this->page}) && (is_object($this->{$this->page}) && method_exists($this->{$this->page},"description"))?$this->{$this->page}->description($this->zmk):((is_object($this->language_obj) && method_exists($this->language_obj,"pobierz"))?$this->language_obj->pobierz("description",false,false):""))."' />
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
					<title>".(isset($this->{$this->page}) && (is_object($this->{$this->page}) && method_exists($this->{$this->page},"title"))?$this->{$this->page}->title($this->zmk):((is_object($this->language_obj) && method_exists($this->language_obj,"pobierz"))?$this->language_obj->pobierz("title",false,false):""))."</title>
						
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
				$rettext.=$this->baza->show_report_message();
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
		public function formaterror($opis)
		{
			$rettext="<p class='komunikaterror'>$this->token &#8855;&#8658; $opis</p><br />";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function formatok($opis)
		{
			$rettext="<p class='komunikatok'>$this->token &#174;&#8658; $opis</p><br />";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function formatwarning($opis)
		{
			$rettext="<p class='komunikatwarning'>$this->token &#174;&#8658; $opis</p><br />";
			return $rettext;
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
		public function create_directory($kat,$debug)
		{
			//sprawdzić czy istnieje taki katalog
			$error=error_reporting(0);
			$jestkatalog=$kat;
			if(!file_exists($kat))
				if(!mkdir($kat))
				{
					$jestkatalog=null;
				}
			error_reporting($error);
			return $jestkatalog;
		}
		//----------------------------------------------------------------------------------------------------
		public function syslog($trace, $dane)
		{
			//$this->strona->syslog(debug_backtrace(), $dane);
			$chceckpoint=$this->checkpoint($trace);
			$jestkatalognalogi=true;
			if(!file_exists($this->RootPath()."/logs"))
			if(!mkdir($this->RootPath()."/logs"))
				$jestkatalognalogi=false;
			if($jestkatalognalogi)
			{
				$plik=fopen($this->RootPath()."/logs/syslog",'a');
				fwrite($plik, $chceckpoint."\n".$dane."\n\n");
				fclose($plik);
			}
		}
		//----------------------------------------------------------------------------------------------------
		public function checkpoint($trace)
		{
			//$trace=debug_backtrace();
			$rettext="";
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
			$rettext.='['.date( 'Y-m-d H:i:s', $time ).'.'.$timeSub.']: '.($file === null?'':$file.' ').($line===null?'':sprintf('{%05d} ',$line)).($class===null?'':$class.$type).($function===null?'':$function.'()');
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function RootPath()
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
		public function instaldir($kat,$debug)
		{
			//sprawdzić czy istnieje taki katalog
			/*$error=error_reporting(0);
			$jestkatalog=$kat;
			if(!file_exists($kat))
				if(!mkdir($kat))
				{
					$this->strona->logtofile($debug,"Brak katalogu '".$folder."' i nie można go utworzyć");
					$jestkatalog=null;
				}
			error_reporting($error);
			return $jestkatalog;*/
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
	die("Class exists: ".__FILE__);
?>