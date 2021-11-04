<?php
if(!class_exists('blue_media'))
{
	require_once './vendor/autoload.php';
	
	class blue_media
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj = $page_obj;
			$this->definicjabazy();
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region destructor
		public function __destruct()
		{
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_content
		public function get_content()
		{
			$content_text = "";
			$template_class_name = $this->page_obj->template."_template";
			//--------------------
			if( $this->page_obj->template == "index" )
			{
				switch($this->page_obj->target)
				{
					case "platnosc":
						$ServiceID = isset($_GET['ServiceID']) ? $_GET['ServiceID'] : "";
						$OrderID = isset($_GET['OrderID']) ? $_GET['OrderID'] : "";
						$Hash = isset($_GET['Hash']) ? $_GET['Hash'] : "";
						$content_text .= $this->link_back($ServiceID, $OrderID, $Hash);
						break;
					case "get_link":
						$idop = isset($_GET['par1']) ? $_GET['par1'] : 0;
						$content_text .= $this->get_link($idop);
						break;
					default:
						$content_text .= "";
						break;
				}
			}
			if( $this->page_obj->template == "raw" )
			{
				switch($this->page_obj->target)
				{
					case "status":
						$status = isset($_POST['transactions']) ? $_POST['transactions'] : "";
						$content_text .= $this->platnosc($status);
						break;
					default:
						$content_text .= "";
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		private function link_back($ServiceID, $OrderID, $Hash)
		{
			$rettext = "";
			//--------------------
			$data = [ 'ServiceID' => $ServiceID, 'OrderID' => $OrderID, 'Hash' => $Hash ];

			$client = new BlueMedia\Client('903764', '8424569ac0c061925ab883b6f34ca80ff3ebc165', 'sha256', '|');
			$result = $client->doConfirmationCheck($data); // true | false

			if($result)
			{
				$idbm = substr($OrderID,7);
				$wynik = $this->page_obj->database_obj->get_data("select status from " . get_class($this) . " where idbm = $idbm;");
				if($wynik)
				{
					list($status) = $wynik->fetch_row();
					if( $status == "oplacone")
					{
						$rettext .= "Dziękujemy twoja płatność została przyjęta pomyślnie.";
					}
					else if( $status == "blad")
					{
						$rettext .= "Błąd transakcja. Sprawdzam status ponownie...";
						$rettext .= "<script>setTimeout(function(){window.location.reload(1);}, 5000);</script>";
					}
					else if( $status == "expired")
					{
						$rettext .= "Płatność wygasła.";
					}
					else
					{
						$rettext .= "Przetwarzanie, proszę czekać...";
						$rettext .= "<script>setTimeout(function(){window.location.reload(1);}, 5000);</script>";
					}
				}
				else
				{
					$rettext .= "Przetwarzanie, proszę czekać...";
					$rettext .= "<script>setTimeout(function(){window.location.reload(1);}, 5000);</script>";
				}
			}
			else
			{
				$rettext .= "Wystąpił problem z przetwarzaniem płatności.";
			}
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function platnosc($status)
		{
			if( $status != "" )
			{
				$client = new BlueMedia\Client('903764', '8424569ac0c061925ab883b6f34ca80ff3ebc165', 'sha256', '|');

				$result = $client->doItnIn($status);
				$itnIn = $result->getData();
				$transactionConfirmed = $client->checkHash($itnIn);
				
				// Jeżeli status płatności z ITN jest potwierdzony i hash jest poprawny - zakończ płatność w systemie
				$transactionConfirmed = true;

				$idbm = substr($itnIn->getOrderId(),7);
				$this->add_itn_history($idbm, $itnIn->getPaymentStatus(), $itnIn->getPaymentStatusDetails(), $itnIn->getPaymentDate(), $itnIn->getAmount());
				if ($itnIn->getPaymentStatus() === 'SUCCESS' && $transactionConfirmed)
				{
					//update blue_media set status = 'oplacone', messages = CONCAT(messages , ' SUCCESS ' , DATE_FORMAT(NOW(), '%Y-%m-%d %T.%f'), '\r\n') where idbm = 34
					$zapytanie = "update " . get_class($this) . " set status = 'oplacone', messages = CONCAT(messages , ' SUCCESS ' , DATE_FORMAT(NOW(), '%Y-%m-%d %T.%f'), '\r\n') where idbm = $idbm;";
					if( $this->page_obj->database_obj->execute_query($zapytanie) )
					{
						$transactionConfirmed = true;
					}
					else
					{
						$transactionConfirmed = false;
					}
				}
				else
				{
					$zapytanie = "update " . get_class($this) . " set status = 'blad', messages = CONCAT(messages , ' " . $itnIn->getPaymentStatus() . " ' , DATE_FORMAT(NOW(), '%Y-%m-%d %T.%f'), '\r\n') where idbm = $idbm;";
					$this->page_obj->database_obj->execute_query($zapytanie);
				}

				$itnResponse = $client->doItnInResponse($itnIn, $transactionConfirmed);
				return $itnResponse->getData()->toXml();
			}
		}
		//----------------------------------------------------------------------------------------------------
		private function get_link($idop)
		{
			$rettext = "";
			//--------------------
			if($idop < 1)
			{
				return "Nieprawidłowa wartość opłaty.";
			}
			//--------------------
			$amount = $this->page_obj->uczniowie_oplaty->get_kwota_do_zaplaty($idop);
			if( $amount <= 0 )
			{
				return "Nieprawidłowa kwota do zapłaty.";
			}
			//--------------------
			$description = "Płatność: $idop";
			$email = $this->page_obj->opiekunowie->get_email_opiekun($this->page_obj->opiekunowie->get_login_ido());
			//--------------------
			$amount = number_format($amount, 2, '.', '');
			//--------------------
			$payment_link = $this->get_current_payment($description, $amount, $email);
			if($payment_link != "")
			{
				//$rettext .= "Link: $payment_link";
				$rettext .= "<script>window.location.href='$payment_link'</script>";
			}
			else
			{
				$idbm = $this->get_new_payment($description, $amount, $email);
				if ( $idbm > 0 )
				{
					$orderID = $this->get_orderID($idbm);
					if ( $orderID != "" )
					{
						$client = new BlueMedia\Client('903764', '8424569ac0c061925ab883b6f34ca80ff3ebc165', 'sha256', '|');

						$result = $client->doTransactionInit([
							'gatewayUrl' => 'https://pay-accept.bm.pl',
							'transaction' => [
								'orderID' => $orderID,
								'amount' => $amount,
								'description' => $description ,
								'gatewayID' => '0',
								'currency' => 'PLN',
								'customerEmail' => $email 
							]
						]);

						$transactionContinue = $result->getData();

						if ($transactionContinue instanceof BlueMedia\Transaction\ValueObject\TransactionContinue)
						{
							$payment_link = $transactionContinue->getRedirectUrl(); // https://pay-accept.bm.pl/payment/continue/9IA2UISN/718GTV5E
							$this->set_payment_link($idbm, $payment_link);
							$this->set_payment_link($idbm, $payment_link);
							//$rettext .= "Link: $payment_link";
							$rettext .= "<script>window.location.href='$payment_link'</script>";
							
							//redirect
						}
						else if ($transactionContinue instanceof BlueMedia\Transaction\ValueObject\TransactionInit)
						{
							$rettext .= "Błąd generowania linku płatności. Error 3. <br /><br />";
							$rettext .= "Potwierdzenie: " . $transactionContinue->getConfirmation() . "<br />";
							$rettext .= "Przyczyna: " . $transactionContinue->getReason() . "<br />";
							$rettext .= "Order id: " . $transactionContinue->getOrderId() . "<br /><br />";
							$rettext .= "Trace: <br />";
							foreach($transactionContinue->toArray() as $key => $val)
							{
								$rettext .= "$key -> $val <br />";
							}
						}
						else
						{
							$rettext .= "Błąd generowania linku płatności. Error 4.";
						}
					}
					else
					{
						$rettext .= "Błąd generowania linku płatności. Error 2.";
					}
				}
				else
				{
					$rettext .= "Błąd generowania linku płatności. Error 1.";
				}
			}
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function get_current_payment($description, $amount, $customerEmail)
		{
			$payment_link = "";
			//--------------------
			$description =  $this->page_obj->text_obj->domysql($description);
			$customerEmail =  $this->page_obj->text_obj->domysql($customerEmail);
			//-----
			$wzor[1]="/,/";
         $zamiany[1]=".";
			$amount = preg_replace($wzor, $zamiany, $amount);
			//--------------------
			//select now() + interval 3600 second; 
			//select now() + interval 8640 minute; 
			//echo("select payment_link from " . get_class($this) . " where usuniety = 'nie' and description = '$description' and amount = $amount and customerEmail = '$customerEmail' and status = 'nowe' and (date + interval 8640 minute) > now();");
			$wynik = $this->page_obj->database_obj->get_data("select payment_link from " . get_class($this) . " where usuniety = 'nie' and description = '$description' and amount = $amount and customerEmail = '$customerEmail' and (status = 'nowe' or status = 'blad') and (date + interval 8640 minute) > now();");
			if($wynik)
			{
				list($payment_link) = $wynik->fetch_row();
			}
			//--------------------
			return $payment_link;
		}
		//----------------------------------------------------------------------------------------------------
		public function get_new_payment($description, $amount, $customerEmail)
		{
			$idbm = 0;
			//--------------------
			$description =  $this->page_obj->text_obj->domysql($description);
			$customerEmail =  $this->page_obj->text_obj->domysql($customerEmail);
			//-----
			$wzor[1]="/,/";
         $zamiany[1]=".";
			$amount = preg_replace($wzor, $zamiany, $amount);
			//-----
			$zapytanie = "insert into " . get_class($this) . "(description, amount, customerEmail, date) values ('$description', $amount, '$customerEmail', now());";
			if( $this->page_obj->database_obj->execute_query($zapytanie) )
			{
				$idbm = $this->page_obj->database_obj->last_id();
			}
			//--------------------
			return $idbm;
		}
		//----------------------------------------------------------------------------------------------------
		public function get_orderID($idbm)
		{
			$orderID = "NZPE_P_" . $idbm;
			$zapytanie = "update " . get_class($this) . " set orderID = '$orderID' where idbm = $idbm;";
			if( $this->page_obj->database_obj->execute_query($zapytanie) )
			{
				return $orderID;
			}
			return "";
		}
		//----------------------------------------------------------------------------------------------------
		public function set_payment_link($idbm, $payment_link)
		{
			$payment_link =  $this->page_obj->text_obj->domysql($payment_link);
			$zapytanie = "update " . get_class($this) . " set payment_link = '$payment_link', date = now() where idbm = $idbm;";
			return $this->page_obj->database_obj->execute_query($zapytanie);
		}
		//----------------------------------------------------------------------------------------------------
		public function get_payment_link_for_idbm($idbm)
		{
			$payment_link = "";
			//--------------------
			$wynik = $this->page_obj->database_obj->get_data("select payment_link from " . get_class($this) . " where idbm = $idbm;");
			if($wynik)
			{
				list($payment_link) = $wynik->fetch_row();
			}
			//--------------------
			return $payment_link;
		}
		//----------------------------------------------------------------------------------------------------
		public function get_payment_list($customerEmail)
		{
			$rettext = array();
			//--------------------
			$customerEmail =  $this->page_obj->text_obj->domysql($customerEmail);
			//-----
			$wynik = $this->page_obj->database_obj->get_data("select idbm from " . get_class($this) . " where usuniety = 'nie' and customerEmail = '$customerEmail';");
			if($wynik)
			{
				while( list($idbm) = $wynik->fetch_row() )
				{
					$rettext[] = (int)$idbm;
				}
			}
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function add_message($idbm, $message)
		{
			$message =  $this->page_obj->text_obj->domysql($message);
			$zapytanie = "update " . get_class($this) . " set messages = messages + '\r\n' + '$message' where idbm = $idbm;";
			return $this->page_obj->database_obj->execute_query($zapytanie);
		}
		//----------------------------------------------------------------------------------------------------
		public function set_status($idbm, $status)
		{
			$status =  $this->page_obj->text_obj->domysql($status);
			$zapytanie = "update " . get_class($this) . " set status = '$status' where idbm = $idbm;";
			return $this->page_obj->database_obj->execute_query($zapytanie);
		}
		//----------------------------------------------------------------------------------------------------
		private function add_itn_history($idbm, $status, $status_details, $payment_date, $amount)
		{
			$idbm =  $this->page_obj->text_obj->domysql($idbm);
			$status =  $this->page_obj->text_obj->domysql($status);
			$status_details =  $this->page_obj->text_obj->domysql($status_details);
			$payment_date =  $this->page_obj->text_obj->domysql($payment_date);
			$amount =  $this->page_obj->text_obj->domysql($amount);

			$zapytanie = "insert into " . get_class($this) . "_ITN" . " (idbm, status, status_details, payment_date, amount) values ($idbm, '$status', '$status_details', '$payment_date', $amount);";
			return $this->page_obj->database_obj->execute_query($zapytanie);
		}
		//----------------------------------------------------------------------------------------------------
		#region definicjabazy
		private function definicjabazy()
		{
			//definition is in ksiegowosc.nzpe.pl
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
	}
}//end if
else
	die("Class exists: ".__FILE__);
?>