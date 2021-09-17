<?php
//--------------------
if(!class_exists('index_template'))
{
	class index_template
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		public function __construct($page_obj)
		{
			$this->page_obj=$page_obj;
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_content($trescstrony)
		{
			if($this->page_obj->opiekunowie->is_login())
			{
				$rettext=$this->index_template_user_is_login($trescstrony);
			}
			else
			{
				$rettext=$this->index_template_user_is_logout($trescstrony);
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function index_template_user_is_logout($trescstrony)
		{
			$rettext="
				<link rel='Stylesheet' type='text/css' href='./css/index.css' />
				<!-- ################################################## -->
				<div class='pasekgorny'><div class='center'><img src='./media/desktop/pasekgorny.gif' alt='' class='pasekgorny'/></div></div>
				<div class='tlo'>
					<div class='center'>
						<img src='./media/desktop/nowe_logo.png' alt='' class='nowe_logo'/>
						<!--<img src='./media/desktop/tytul2.png' alt='' class='tytul2'/>-->
						{$this->login_form()}
						<div class='info'>
							<img src='./media/desktop/ikonadomku.gif' alt='' style='vertical-align:middle;' /> Bielska 54, 43-200 Pszczyna <img src='./media/desktop/ikonatelefonu.gif' alt='' style='vertical-align:middle;'/>  502 243 181 <img src='./media/desktop/ikonakoperty.gif' alt='' style='vertical-align:middle;' /> <a href='mailto:biuro@nzpe.pl' style='color:inherit;text-decoration:none;'>biuro@nzpe.pl</a> <img src='./media/desktop/ikonamapy.gif' alt='' style='vertical-align:middle;' /> <a href='https://goo.gl/maps/bmHnVLcNAhYVxGy8A' onclick='window.open(\"https://goo.gl/maps/bmHnVLcNAhYVxGy8A\",\"chaild\");return false;' style='color:inherit;text-decoration:none;'> mapa</a> 
						</div>
					</div>
				</div>";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function index_template_user_is_login($trescstrony)
		{
			$rettext = "<link rel='Stylesheet' type='text/css' href='./css/index_login.css' />";
			$rettext .= "<div style='width:100%;'>";
			$rettext .= "<div style='width:250px;float:left;overflow:hidden;padding:10px;'>";
			$rettext .= "<div class='button_spacing'></div>";
			$rettext .= "<div class='login_imie_nazwisko'>".$this->page_obj->opiekunowie->get_login_imie_nazwisko()."</div>";
			$rettext .= "<div class='button_spacing_x4'></div>";
			$rettext .= $this->button("Uczniowie","uczniowie,index,lista");
			$rettext .= "<div class='button_spacing'></div>";
			$rettext .= $this->button("Opłaty","oplaty,index,lista");
			$rettext .= "<div class='button_spacing'></div>";
			$rettext .= $this->button("Wyciągi","wyciagi,index,lista");
			$rettext .= "<div class='button_spacing_x4'></div>";
			$rettext .= "<div class='button_spacing_x4'></div>";
			$rettext .= $this->button("Zmień hasło","opiekunowie,index,change_password_form");
			$rettext .= "<div class='button_spacing'></div>";
			$rettext .= $this->button("Wyloguj","staticpages,index,logout");
			$rettext .= "</div>";
			$rettext .= "<div style='overflow:hidden;padding:20px;'>";
			$rettext .= $trescstrony;
			$rettext .= "</div>";
			$rettext .= "</div>";
			$rettext .= "<div style='width:100%;height:30px;clear:both;text-align:right;position:absolute;bottom:10px;'><p style='padding:10px;'>v 1.1</p></div>";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function button($title,$link)
		{
			$rettext = "";
			//--------------------
			$rettext = "<div class='button' onclick='window.location=\"{$link}\"'>{$title}</div>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function login_form()
		{
			$rettext="<form method='post' action='staticpages,index,login' class='login_form'>";
			$rettext.="<input type='text' class='login_form_input' name='r_login' placeholder='e-mail' /> <br />";
			$rettext.="<input type='password' class='login_form_input' name='r_password' placeholder='hasło' /> <br />";
			$rettext.="<input type='submit' class='login_form_submit' value='zaloguj' />";
			$rettext.="</form>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function infoOCookie()
		{
			if(isset($_COOKIE['cookieinfo']))$cookieinfo=$_COOKIE['cookieinfo'];else $cookieinfo="";
			$rettext="
				<div id='cookieinfo' class='cookieinfo'>
					<div class='cookieinfowarstwanosna' style='".($cookieinfo=="potwierdzone"?"display:none;":"")."'>
						<div class='cookieinfowarstwatekstu'>
							".$this->page_obj->language_obj->pobierz("cookieinfo")."
						</div>
						<img src='./media/desktop/zamknij.png' alt='' class='cookieinfozamknij' onclick='document.getElementById(\"cookieinfo\").style.display=\"none\";setCookie(\"cookieinfo\",\"potwierdzone\",365);'/>
					</div>
				</div>";
			return $rettext;
		}
	}
}
?>