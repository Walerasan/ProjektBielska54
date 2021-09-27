<?php
//--------------------
if(!class_exists('change_password_template'))
{
	class change_password_template
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		public function __construct($page_obj)
		{
			$this->page_obj = $page_obj;
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_content($trescstrony)
		{
			$rettext = $this->change_password_template($trescstrony);
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function change_password_template($trescstrony)
		{
			$rettext="
				<link rel='Stylesheet' type='text/css' href='./css/index.css' />
				<!-- ################################################## -->
				<div class='pasekgorny'><div class='center'><img src='./media/desktop/pasekgorny.gif' alt='' class='pasekgorny'/></div></div>
				<div class='tlo'>
					<div class='center'>
						<img src='./media/desktop/nowe_logo.png' alt='' class='nowe_logo'/>
						<div style='position:absolute;left:460px;top:300px;'>
							$trescstrony
						</div>
						<div class='info'>
							<img src='./media/desktop/ikonadomku.gif' alt='' style='vertical-align:middle;' /> Bielska 54, 43-200 Pszczyna <img src='./media/desktop/ikonatelefonu.gif' alt='' style='vertical-align:middle;'/>  502 243 181 <img src='./media/desktop/ikonakoperty.gif' alt='' style='vertical-align:middle;' /> <a href='mailto:biuro@nzpe.pl' style='color:inherit;text-decoration:none;'>biuro@nzpe.pl</a> <img src='./media/desktop/ikonamapy.gif' alt='' style='vertical-align:middle;' /> <a href='https://goo.gl/maps/bmHnVLcNAhYVxGy8A' onclick='window.open(\"https://goo.gl/maps/bmHnVLcNAhYVxGy8A\",\"chaild\");return false;' style='color:inherit;text-decoration:none;'> mapa</a> 
						</div>
					</div>
				</div>";
			return $rettext;
		}
	}
}
?>