<?php

require_once __DIR__ . '/vendor/autoload.php';

if(isset($_POST['submitpdf'])){

	$od = $_POST['od'];
	$do = $_POST['do'];
	$kwota = $_POST['kwota'];
	$mpdf = new \Mpdf\Mpdf();
    
    $data = '';
    
    $data .= '<h1>Lista wpłat - Raport</h1>';
    
    $polaczenie = new Mysqli("localhost","root","","ksiegowosc_nzpe_pl");

    $data.="<table style='width:100%;font-size:10pt;' cellspacing='0' border='1'><tbody>";
				$data.="<tr style='font-weight:bold;'>";
				$data.="<td>Lp.</td><td>Wpływ</td><td>Tytuł</td><td>Data</td><td>Typ</td>";
				$data.="</tr>";
				$zapytanie = $polaczenie->query("SELECT kwota,tytul,dataoperacji,typ FROM wyciagi WHERE dataoperacji >= '$od' AND dataoperacji <= '$do' ORDER BY dataoperacji ASC;");
					if($zapytanie)
					{
						$lp=1;
                        while(list($kwota,$tytul,$dataoperacji,$typ)=mysqli_fetch_array($zapytanie)){
							$dataop = date("Y-m-d",strtotime($dataoperacji));  
							$data.="<tr>";
								$data.="<td>$lp</td><td>$kwota</td><td>$tytul</td><td>$dataop</td><td>$typ</td>";
							$data.="</tr>";
							$lp++;
						}
					}
	$data.="<tbody></table>";



    $mpdf->WriteHTML($data);
    $mpdf->Output('mojpdf.pdf','D');
    $polaczenie->close();
}



//https://getcomposer.org/download/
//https://github.com/mpdf/mpdf
?>