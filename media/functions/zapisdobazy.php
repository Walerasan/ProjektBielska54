<?php
//konfiguracja połączenia do bazy danych
/* 
Baza danych: `bazaprzetwarzanie`
Struktura tabeli dla tabeli `dane`

CREATE TABLE `dane` (
  `idd` int(11) NOT NULL,
  `nrkonta` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `od` timestamp NOT NULL DEFAULT current_timestamp(),
  `do` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


CREATE TABLE `transakcje` (
  `idt` int(11) NOT NULL,
  `dataoperacji` timestamp NOT NULL DEFAULT current_timestamp(),
  `datawaluty` timestamp NOT NULL DEFAULT current_timestamp(),
  `typtransakcji` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `opistransakcji` text COLLATE utf8_polish_ci NOT NULL,
  `kwota` decimal(10,0) NOT NULL,
  `waluta` varchar(255) NOT NULL,
  `saldopotransakcji` decimal(10,0) NOT NULL,
  `idwyciagu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

TRUNCATE transakcje;
TRUNCATE dane;

 */

$polaczenie = @new Mysqli("localhost","root","","bazaprzetwarzanie",3306);
//--------------------------------------
//pobieram post: nr, od ,do
if(!empty($_POST['nr']) && !empty($_POST['od']) && !empty($_POST['do'])){
    $nr_konta = trim($_POST['nr']);
    $data_od = $_POST['od'];
    $data_do = $_POST['do'];

    $zapytanie1 = $polaczenie->query("INSERT INTO dane(nrkonta,od,do) values('$nr_konta','$data_od','$data_do')");
}

if(!empty($_POST['dataoperacji']) && !empty($_POST['datawaluty']) && !empty($_POST['typtransakcji']) && !empty($_POST['opistransakcji']) && !empty($_POST['kwota']) && !empty($_POST['waluta']) && !empty($_POST['saldopotransakcji'])){
    
    $dataoperacji = $_POST['dataoperacji'];
    $datawaluty = $_POST['datawaluty'];
    $typtransakcji = $_POST['typtransakcji'];
    $opistransakcji = $_POST['opistransakcji'];
    $kwota = $_POST['kwota'];
    $waluta = $_POST['waluta'];
    $saldopotransakcji = $_POST['saldopotransakcji'];
    
    //$idwyciagu = mysqli_insert_id($polaczenie);
    $selectquery="SELECT idd FROM dane ORDER BY idd DESC LIMIT 1";
    $result = $polaczenie->query($selectquery);
    list($idwyciagu) = mysqli_fetch_array($result);
    //---------------------------------------------------

    $zapytanie2 = $polaczenie->query("INSERT INTO transakcje(idwyciagu,dataoperacji,datawaluty,typtransakcji,opistransakcji,kwota,waluta,saldopotransakcji) values($idwyciagu,'$dataoperacji','$datawaluty','$typtransakcji','$opistransakcji','$kwota','$waluta','$saldopotransakcji')");

}
$polaczenie->close();
?>