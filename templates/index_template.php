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
			$formularzlogowania="";//(is_object($this->silnik->uzytkownicy) && method_exists($this->silnik->uzytkownicy,"formularz")) ?$this->silnik->uzytkownicy->formularz():"";
			$stopkastrony=$this->stopka();		
			//--------------------
			$rettext="
				<link rel='Stylesheet' type='text/css' href='./css/index.css' />
				<!-- ################################################## -->
				 ".$this->infoOCookie($page_obj)."
					<!-- ################## środek strony ##################### -->
					<div class='tlostrony'>
					  <div class='centruj'>
					  
						  <!-- ########## -->
					  	<div class='belkalogailoginu'>
					  		<div class='logo'><img src='./media/desktop/logo.gif' alt='LabNode - Laboratorium informatyki, matematyki, chemii, mechaniki, elektroniki' /></div>
					  		<div class='logindata'>
					  			$formularzlogowania
					  			<br />
									Twoje ip: ".((isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'])."
									<div id='godzina' class='godzina' onclick='var e=arguments[0] || event;kalendarzKG.pokaz(e,\"\",\"\",\"\",\"\",\"\",\"\",this,true);'></div>
									<script type='text/javascript'>var kalendarzKG=new LabNode.kalendarz('KG');</script>\n 
									<script type='text/javascript'>new LabNode.zegarek('godzina');</script>\n
					  		</div>
					  	</div>
											
							<!-- ########## -->				
					  	<div class='belkamenu'>
					  		<div class='belkamenuodstepy'>
					  			$menupozome
					  		</div>
					  	</div>
					  				
					  	<!-- ########## -->
					  	<div class='srodek'>
								$trescstrony
							</div>
							
						 	<!-- ###### koniec środek strony ###### -->
						 	$stopkastrony
						</div>
					</div>
			";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function stopka()
		{
			$rettext="<div class='stopka'>
							..:: &#169;opyright: <a href='javascript:window.open(\"http://labnode.org\",\"chaild\");void(null);' class='stopkadolna'>labnode.org</a> 2010 &#183;&#183;::.. Project &#38; fabricating: <a href='javascript:window.open(\"http://labnode.org\",\"chaild\");void(null);' class='stopkadolna'>LabNode</a> ..::&#183;&#183; Programmer: <a href='javascript:window.open(\"mailto:rafal@labnode.org\",\"chaild\");void(null);' class='stopkadolna'>Rafał Oleśkowicz</a> ::..<br />
							..:: Laboratorium informatyki, matematyki, chemii, mechaniki, elektroniki, psychologii ::..<br />
							..:: mgr inż. Rafał Oleśkowicz ::..
						</div>";
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