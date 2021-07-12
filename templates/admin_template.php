<?php
//--------------------
if(!class_exists('admin_template'))
{
	class admin_template
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
			$rettext="";
			if(!$this->page_obj->users->is_login())
			{
				$trescstrony="";
				$menupozome="";
				$komunikat="Prosze się zalogować";
				//--------------------
				$rettext .= "<body>
								<link rel='Stylesheet' type='text/css' href='./css/admin.css' />
								<div class='loginodstepgorny'></div>
								<div class='center'>
									<img src='./media/admin/tytul.gif' alt='Panel administracyjny' /> <br />
									<img src='./media/admin/liniapozioma.gif' alt='Linia pozioma' /> <br />
									<form method='post' action='users,admin,login'>
										<table style='width:700px;margin:auto;margin-top:40px;margin-bottom:40px;'>
											<tr><td style='text-align:right;'><img src='./media/admin/login.gif' alt='login' /></td><td style='text-align:center;'><input type='text' name='r_login' value='login' style='width:412px;height:53px;background:url(./media/admin/tlopola.gif);border:0px;text-align:center;font-size:28px;padding:0px;' onclick='this.value==\"login\"?this.value=\"\":null'/></td></tr>
											<tr style='line-height:5px;'><td style='text-align:right;'>&nbsp;</td><td style='text-align:center;'>&nbsp;</td></tr>
											<tr><td style='text-align:right;'><img src='./media/admin/haslo.gif' alt='hasło' /></td><td style='text-align:center;'><input type='password' name='r_password' value='hasło' style='width:412px;height:53px;background:url(./media/admin/tlopola.gif);border:0px;text-align:center;font-size:28px;padding:0px;' onclick='this.value==\"hasło\"?this.value=\"\":null'/></td></tr>
										</table>
										<img src='./media/admin/liniapozioma.gif' alt='Linia pozioma' /> <br />
										<br />
										<table style='width:800px;margin:auto;'>
											<tr>
												<td><img src='./media/admin/przypomnijhaslo.gif' alt='Przypomnij hasło' /></td>
												<td><img src='./media/admin/liniapionowa.gif' alt='' /></td>
												<td><input type='submit' name='' value='' style='width:248px;height:72px;background:url(./media/admin/zaloguj.gif);border:0px;cursor:hand;cursor:pointer;'/></td>
											</tr>
										</table>
									</form>
								</div>
							</body>";
			}
			else
			{
				$menupozome="<a href='users,admin,lista'>Użytkownicy</a><br />";
				$menupozome.="<a href='oddzialy,admin,lista'>Oddziały</a><br />";
				$menupozome.="<hr />";
				$menupozome.="<a href='users,admin,logout'>Logout</a><br />";
				$komunikat="";
				//--------------------
				$rettext .= "
								<link rel='Stylesheet' type='text/css' href='./css/admin.css' />
								<!-- ################################################## -->
								<div class='header'>
									<div class='logo'><img src='./media/admin/LabnodeLogo.gif' alt='' class='labnodelogo'/></div>
									<div class='komunikat'>$komunikat</div>
								</div>
								<table>
									<tr>
										<td style='vertical-align:top;width:200px;padding:10px;border-right:2px solid gray;margin:10px;'>$menupozome</td>
										<td style='vertical-align:top;padding:10px;'>$trescstrony</td>
									</tr>
								</table>
								<!-- ################################################## -->
							";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------		
	}
}
?>