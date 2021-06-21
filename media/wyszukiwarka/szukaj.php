<?php 

$polaczenie = new mysqli("localhost", "root", "", "ksiegowosc_nzpe_pl"); 
 
// Check connection 
if ($polaczenie->connect_error) { 
    die("błąd połączenia: " . $polaczenie->connect_error); 
} 
$nrkonta = $_GET['term']; //uwaga nie zmieniać nazwy globalnej 'term' !!!
$query = $polaczenie->query("SELECT * FROM wyciagi WHERE rachuneknadawcy LIKE '%".$nrkonta."%' GROUP BY rachuneknadawcy;"); 
 
// Generate array
$tablicaKont = array(); 
if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 
        $data['id'] = $row['idw']; 
        $data['value'] = $row['rachuneknadawcy']; 
        array_push($tablicaKont, $data); 
    } 
} 
 
// Return results as json encoded array 
echo json_encode($tablicaKont);
//-------------------------------------- 

$polaczenie->close();
?>