/*
Opis: Przetwarzenie html
Wersja: 1.0
Author: Hydrotrade Polska
Data utworzenia: 04.05.2021.
*/

const tabelaRachunek = document.getElementsByTagName("table")[0];//w DOM 1 el. table
//const tabelaPusta = document.getElementsByTagName("table")[1];//w DOM 2 el. table
const tabelaTransakcje = document.getElementsByTagName("table")[2];////w DOM 3 el. table

//pobieram zakres historii transakcji--------------------------------

const iloscwierszytabelaRachunek = tabelaRachunek.rows.length;
const iloscwierszytabelaTransakcje = tabelaTransakcje.rows.length;

for (var i=0;i<iloscwierszytabelaRachunek;i++){
    if(tabelaRachunek.rows[i].getElementsByTagName("td").length > 0){

        var dl = tabelaRachunek.rows[i].getElementsByTagName("td").length;
        
        for (let j = 0; j < dl; j++) {
            //pobieram nr konta z historii wyciągu
            if(i==0 && j==1){
                var nrKonta = tabelaRachunek.rows[i].getElementsByTagName("td")[j].innerHTML;
                console.log(nrKonta);
            }
            //pobieram datę "od dnia" wygenerowanego wyciągu---------------------------------
            if(i==1 && j==1){
                var od_dnia = tabelaRachunek.rows[i].getElementsByTagName("td")[j].innerHTML;
                console.log(od_dnia);
            }
            //pobieram datę "do dnia" wygenerowanego wyciągu----------------------------------
            if(i==2 && j==1){
                var do_dnia = tabelaRachunek.rows[i].getElementsByTagName("td")[j].innerHTML;
                console.log(do_dnia);
            }   
        }
    }
}


var transakcje = function(){

    for (var i=1;i<iloscwierszytabelaTransakcje;i++){
        if(tabelaTransakcje.rows[i].getElementsByTagName("td").length > 0){
                //pobieram Data operacji
                
                    var dataoperacji = tabelaTransakcje.rows[i].getElementsByTagName("td")[0].innerHTML;
                    //console.log(dataoperacji);
                    
                
                //pobieram Data waluty
              
                    var datawaluty = tabelaTransakcje.rows[i].getElementsByTagName("td")[1].innerHTML;
                    //console.log(datawaluty);
                   
               
                //pobieram typ transakcji
                
                    var typtransakcji = tabelaTransakcje.rows[i].getElementsByTagName("td")[2].innerHTML;
                    //console.log(typtransakcji);
                   
                
                //pobieram Opis transakcji
                
                    var opistransakcji = tabelaTransakcje.rows[i].getElementsByTagName("td")[3].innerHTML;
                    //console.log(opistransakcji);
                
                //pobieram Kwota
                
                    var kwota = tabelaTransakcje.rows[i].getElementsByTagName("td")[4].innerHTML;
                    //console.log(kwota);
              
                //pobieram Waluta
            
                    var waluta = tabelaTransakcje.rows[i].getElementsByTagName("td")[5].innerHTML;
                    //console.log(waluta);
            
                //pobieram saldo po transakcji
                
                    var saldopotransakcji = tabelaTransakcje.rows[i].getElementsByTagName("td")[6].innerHTML;
                    //console.log(saldopotransakcji);      
        }

        $.ajax({
            method:"POST",
            url: "zapisdobazy.php",
            data:{
                dataoperacji:dataoperacji,
                datawaluty:datawaluty,
                typtransakcji:typtransakcji,
                opistransakcji:opistransakcji,
                kwota:kwota,
                waluta:waluta,
                saldopotransakcji:saldopotransakcji
            }
        }).done(function(msg){
            //alert("Dodano do bazy danych 2: " + msg);
        });  
    }
}

$.ajax({
    method:"POST",
    url: "zapisdobazy.php",
    data:{nr:nrKonta,od:od_dnia,do:do_dnia}
}).done(function(msg){
    alert("Dodano do bazy danych 1: " + msg);
    transakcje();
});

console.log(iloscwierszytabelaRachunek);

//--------------------------------------------------------------------