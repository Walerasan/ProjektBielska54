<?php
/**
 * 
 */
$polaczenie = @new Mysqli("localhost","root","","ksiegowosc_nzpe_pl",3306);
//--------------------------------------
//pobieram post: nr, od ,do
if(!empty($_POST['nr']) && !empty($_POST['od'])){
    $nr_konta = trim($_POST['nr']);
    $datawp = $_POST['od'];
    $zapytanie1 = $polaczenie->query("INSERT INTO nr_konta(numer_konta,datawp) values('$nr_konta','$datawp')");
}

if(!empty($_POST['dataoperacji']) && !empty($_POST['typtransakcji']) && !empty($_POST['opistransakcji']) && !empty($_POST['kwota'])){
    $dataoperacji = $_POST['dataoperacji'];
    $typtransakcji = $_POST['typtransakcji'];
    $opistransakcji = $_POST['opistransakcji'];
    $kwota = $_POST['kwota'];
    //$nrKonta = $_POST['nrKonta'];//rachuneknadawcy
           
    //$idwyciagu = mysqli_insert_id($polaczenie);
    $selectquery="SELECT idnk FROM nr_konta ORDER BY idnk DESC LIMIT 1";
    $result = $polaczenie->query($selectquery);
    list($idwyciagu) = mysqli_fetch_array($result);
    //---------------------------------------------------
    if($typtransakcji == "Przelew na rachunek"){
      
      $zapytanie2 = $polaczenie->query("INSERT INTO wyciagi(id_nr_konta,dataoperacji,opistransakcji,kwota) values($idwyciagu,'$dataoperacji','$opistransakcji','$kwota')");
    }
    

}
$polaczenie->close();
?>