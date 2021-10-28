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
			$this->page_obj=$page_obj;
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
			$content_text = "<p class='title'>BLUE MEDIA</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( $this->page_obj->template == "index" )
			{
				switch($this->page_obj->target)
				{
					case "get_link":
						$content_text .= $this->get_link();
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
						$status = $_POST['transactions'];
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
		private function platnosc($status)
		{
			$result = $client->doItnIn($status);

			$itnIn = $result->getData();
			$transactionConfirmed = $client->checkHash($itnIn);

			$filename = 'platnosci.txt';
			if (!$handle = fopen($filename, 'a'))
			{
			}
			else
			{
				fwrite($handle, var_dump($result));
				fwrite($handle, "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~");
				fwrite($handle, var_dump($itnIn));
				fclose($handle);
			}
			
			// Jeżeli status płatności z ITN jest potwierdzony i hash jest poprawny - zakończ płatność w systemie
			if ($itnIn->getPaymentStatus() === 'SUCCESS' && $transactionConfirmed)
			{
				$order = $this->orderRepository->find($itnIn->getOrderId());

				$order->setPaymentCompleted();
			}

			$itnResponse = $client->doItnInResponse($itnIn, $transactionConfirmed);

			return new Response($itnResponse->getData()->toXml());
		}
		//----------------------------------------------------------------------------------------------------
		private function get_link()
		{
			$rettext = "";
			//--------------------
			$amount = 1.50;
			$description = "Płatność: 12, 13, 14";
			$email = "sioleskr@gmail.com";
			//--------------------
			$amount = number_format($amount, 2, '.', '');
			//--------------------
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
						$rettext .= "Link: $payment_link";
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
			//--------------------
			return $rettext;
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
			$zapytanie = "insert into " . get_class($this) . "(description, amount, customerEmail) values ('$description', $amount, '$customerEmail');";
			if($this->page_obj->database_obj->execute_query($zapytanie))
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
			$zapytanie = "update " . get_class($this) . " set payment_link = '$payment_link' where idbm = $idbm;";
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