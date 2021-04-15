<?php
//--------------------
if(!class_exists('index_template'))
{
    class index_template
	{
		//----------------------------------------------------------------------------------------------------
	    public function __construct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_content($page_obj,$trescstrony,$menupozome)
		{
			if($page_obj->users->is_login())
		    {
				$rettext=$this->index_template_user_is_login();
			}
			else
			{
				$rettext=$this->index_template_user_is_logout();
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function index_template_user_is_logout()
		{
			$rettext="
				<link rel='Stylesheet' type='text/css' href='./css/index.css' />
				<!-- ################################################## -->
				<div class='pasekgorny'><div class='center'><img src='./media/desktop/pasekgorny.gif' alt='' class='pasekgorny'/></div></div>
				<div class='tlo'>			  
					<div class='center'>
						<img src='./media/desktop/tytul.png' alt='' class='tytul'/>
						<img src='./media/desktop/tytul2.png' alt='' class='tytul2'/>
						{$this->login_form()}
						<div class='info'>				  		
							<img src='./media/desktop/ikonadomku.gif' alt='' style='vertical-align:middle;' /> Bielska 54, 43-200 Pszczyna <img src='./media/desktop/ikonatelefonu.gif' alt='' style='vertical-align:middle;'/>  502 243 181 <img src='./media/desktop/ikonakoperty.gif' alt='' style='vertical-align:middle;' /> <a href='mailto:biuro@nzpe.pl' style='color:inherit;text-decoration:none;'>biuro@nzpe.pl</a> <img src='./media/desktop/ikonamapy.gif' alt='' style='vertical-align:middle;' /> <a href='https://goo.gl/maps/bmHnVLcNAhYVxGy8A' onclick='window.open(\"https://goo.gl/maps/bmHnVLcNAhYVxGy8A\",\"chaild\");return false;' style='color:inherit;text-decoration:none;'> mapa</a> 
						</div>
					</div>
				</div>";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function index_template_user_is_login()
		{
			$rettext="jesteś zalogowany <br />";
			$rettext.="<a href='staticpages,index,logout'>Logout</a><br />";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function login_form()
		{
			$rettext="<form method='post' action='staticpages,index,login' class='login_form'>";
			$rettext.="<input type='text' class='login_form_input' name='r_login' value='e-mail' onclick='this.value==\"e-mail\"?this.value=\"\":null'/> <br />";
			$rettext.="<input type='text' class='login_form_input' name='r_password' value='hasło' onclick='this.value==\"hasło\"?this.value=\"\":null;this.type=\"password\";'/> <br />";
			$rettext.="<input type='submit' class='login_form_submit' value='zaloguj' />";
			$rettext.="</form>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function infoOCookie($page_obj)
		{
		    if(isset($_COOKIE['cookieinfo']))$cookieinfo=$_COOKIE['cookieinfo'];else $cookieinfo="";
		    $rettext="
			<div id='cookieinfo' class='cookieinfo'>
				<div class='cookieinfowarstwanosna' style='".($cookieinfo=="potwierdzone"?"display:none;":"")."'>
					<div class='cookieinfowarstwatekstu'>
						".$page_obj->language_obj->pobierz("cookieinfo")."
					</div>
					<img src='./media/desktop/zamknij.png' alt='' class='cookieinfozamknij' onclick='document.getElementById(\"cookieinfo\").style.display=\"none\";setCookie(\"cookieinfo\",\"potwierdzone\",365);'/>
				</div>
			</div>";
		    return $rettext;
		}
	}
}
?>