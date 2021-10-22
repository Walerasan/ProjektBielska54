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
			$content_text="<p class='title'>UCZNIOWIE</p>";
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
			if( $this->page_obj->template=="raw" )
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
			if ($itnIn->getPaymentStatus() === 'SUCCESS' && $transactionConfirmed) {
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
			
			$client = new BlueMedia\Client(
				'903764', 
				'8424569ac0c061925ab883b6f34ca80ff3ebc165',
				'sha256', // tryb hashowania, domyślnie sha256, można użyć stałej z BlueMedia\Common\Enum\ClientEnum
				'|' // separator danych, domyślnie |
			);

			$result = $client->getTransactionRedirect([
				'gatewayUrl' => 'https://pay-accept.bm.pl', // Adres bramki BlueMedia
				'transaction' => [
					'orderID' => '12021', // Id transakcji, wymagany
					'amount' => '1.20', // Kwota transakcji, wymagany
					'description' => 'Transakcja 122021', // Tytuł transakcji, opcjonalny
					'gatewayID' => '0', // Identyfikator kanału płatności, opcjonalny, w tym przypadku można ustawić jako 0 lub pominąć
					'currency' => 'PLN', // Waluta transakcji, opcjonalny, domyślnie PLN
					'customerEmail' => 'sioleskr@gmail.com' // Email klienta, opcjonalny, zalecany ze względu na automatyczne uzupełnienie pola po stronie serwisu BM
				]
			 ]);
 
			$rettext $result->getData();
 
			return $rettext;
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