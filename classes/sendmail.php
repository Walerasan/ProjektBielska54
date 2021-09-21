<?php
if(!class_exists('sendmail'))
{
	class sendmail
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
		public function sendsystemmessage($email_to,$title,$content)
		{
			$content2="$title \n";
			$content2.="\n$content\n";
			$headers="From: $email_to \n";
			$headers.="Reply-To: $email_to \n";
			$headers.="X-Mailer: PHP/".phpversion()." LabNode.org \n";
			$headers.="Content-Type: text/plain; charset=\"utf-8\"\n";
			$err_rep=error_reporting(0);
			$err_val=mail($email_to,$title,$content2,$headers);
			error_reporting($err_rep);
			return $err_val;
		}
		//----------------------------------------------------------------------------------------------------
		public function sendmessage($email_to,$title,$content)
		{
			$content2="$title \n";
			$content2.="\n$content\n";
			$headers="From: $email_to \n";
			$headers.="Reply-To: $email_to \n";
			$headers.="X-Mailer: PHP/".phpversion()." LabNode.org \n";
			$headers.="Content-Type: text/plain; charset=\"utf-8\"\n";
			$err_rep=error_reporting(0);
			$err_val=mail($email_to,$title,$content2,$headers);
			error_reporting($err_rep);
			return $err_val;
		}
		//----------------------------------------------------------------------------------------------------
		public function sendhtmlmessage($email_to,$title,$content)
		{
			$content2="$title \n";
			$content2.="\n$content\n";
			$headers="From: $email_to \n";
			$headers.="Reply-To: $email_to \n";
			$headers.="X-Mailer: PHP/".phpversion()." LabNode.org \n";
			$headers.="Content-Type: text/html; charset=\"utf-8\"\n";
			$err_rep=error_reporting(0);
			$err_val=mail($email_to,$title,$content2,$headers);
			error_reporting($err_rep);
			return $err_val;
		}
		//----------------------------------------------------------------------------------------------------
		public function sendhtmlmessage_from($email_from,$email_to,$title,$content)
		{
			//$content2="$title \n";
			$content2.="$content\n";
			$headers="From: $email_from \n";
			$headers.="Reply-To: $email_from \n";
			$headers.="X-Mailer: PHP/".phpversion()." LabNode.org \n";
			$headers.="Content-Type: text/html; charset=\"utf-8\"\n";
			$err_rep=error_reporting(0);
			$err_val=mail($email_to,$title,$content2,$headers);
			error_reporting($err_rep);
			return $err_val;
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
	die("Class exists: ".__FILE__);
?>