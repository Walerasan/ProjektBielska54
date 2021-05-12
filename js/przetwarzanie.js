/*
Opis: Przetwarzenie html
Wersja: 1.0
Author: Hydrotrade Polska
Data utworzenia: 04.05.2021.
*/
//alert("test skryptu");

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
                //var nrKonta = tabelaRachunek.rows[i].getElementsByTagName("td")[j].innerHTML;
                var nrKonta = tabelaRachunek.rows[i].cells[j].innerHTML;
            }
            //pobieram datę "od dnia" wygenerowanego wyciągu---------------------------------
            if(i==1 && j==1){
                var od_dnia = tabelaRachunek.rows[i].cells[j].innerHTML;
            }  
        }
    }
}


var transakcje = function(){

    for (var i=1;i<iloscwierszytabelaTransakcje;i++){
        if(tabelaTransakcje.rows[i].getElementsByTagName("td").length > 0){
                //pobieram Data operacji
                //var dataoperacji = tabelaTransakcje.rows[i].getElementsByTagName("td")[0].innerHTML;
                    var dataoperacji = tabelaTransakcje.rows[i].cells[0].innerHTML;
                //pobieram typ transakcji
                    var typtransakcji = tabelaTransakcje.rows[i].cells[2].innerHTML;
                //pobieram Opis transakcji
                    var opistransakcji = tabelaTransakcje.rows[i].cells[3].innerHTML;
                //wyciagam nr konta: Rachunek nadawcy z Opisu transakcji
                    //var opis = opistransakcji.split(":");
                    //var nrkonta = opis[1].substr(0,opis[1].indexOf("<br>"));
                //var nrkonta = nr.replace(/&nbsp;/g,'').nr.replace(/=(\r\n|\n|\r)/gm,"");
                //pobieram Kwota
                    var kwota = tabelaTransakcje.rows[i].cells[4].innerHTML;   
        }

        $.ajax({
            method:"POST",
            url: "./media/functions/zapisdobazy.php",
            data:{
                dataoperacji:dataoperacji,
                typtransakcji:typtransakcji,
                opistransakcji:opistransakcji,
                kwota:kwota
                //nrkonta:nrKonta
            }
        }).done(function(msg){
            //alert("Dodano do bazy danych 2: " + msg);
        });  
    }
}

$.ajax({
    method:"POST",
    url: "./media/functions/zapisdobazy.php",
    data:{nr:nrKonta,od:od_dnia}
}).done(function(msg){
    alert("Dodano do bazy danych 1: " + msg);
    transakcje();
});

console.log(iloscwierszytabelaRachunek);

//--------------------------------------------------------------------