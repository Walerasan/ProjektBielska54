<?php
 $conn = new Mysqli("localhost","root","","ksiegowosc_nzpe_pl");
 mysqli_set_charset($conn,"utf8");

 if(isset($_POST['submitcsv'])){

                $od = $_POST['od'];
                $do = $_POST['do'];
                $db_record = "WYCIAGI";
                $csv_filename = 'db_export_'.$db_record.'_'.date('Y-m-d').'.csv';

            // create empty variable to be filled with export data
            $csv_export = '';

            // query to get data from database
            $query = mysqli_query($conn, "SELECT rachuneknadawcy, dataoperacji, tytul, nazwanadawcy, adresnadawcy FROM wyciagi WHERE dataoperacji >= '$od' AND dataoperacji <= '$do' ORDER BY dataoperacji ASC;");
            $field = mysqli_field_count($conn);

            // create line with field names
            for($i = 0; $i < $field; $i++) {
                $csv_export.= mysqli_fetch_field_direct($query, $i)->name.';';
            }

            // newline (seems to work both on Linux & Windows servers)
            $csv_export.= '
            ';

            // loop through database query and fill export variable
            while($row = mysqli_fetch_array($query)) {
                // create line with field values
                for($i = 0; $i < $field; $i++) {
                    $csv_export.= '"'.$row[mysqli_fetch_field_direct($query, $i)->name].'";';
                }
                $csv_export.= '
            ';
            }

            // Export the data and prompt a csv file for download
            header("Content-Transfer-Encoding: UTF-8");
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            //header('Content-type: application/csv');
            header("Content-Disposition: attachment; filename=".$csv_filename."");
            //echo "\xEF\xBB\xBF";
            echo($csv_export);

    }

$conn->close();
 ?>