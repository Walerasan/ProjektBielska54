/**
 * Rafał Oleśkowicz
 * Pole tekstowe z możliwością nadawania atrybutów tekstowi.
 */
if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.zmienijacezdjecia=function(){};
//--------------------
LabNode.zmienijacezdjecia.prototype.init=function()
{
	var zmzi=1;
	var czaszmiany=7000;
	var predkosczmiany=1000;
	var tresc01='<p class="ramkazdjeciatytul">WILK SPORT TEAM</p><p class="ramkazdjeciatresc">TRENINGI DLA SPORTOWCÓW</p><p class="ramkazdjeciatresc">ORAZ OSÓB AKTYWNYCH FIZYCZNIE</p>';
	var tresc02='<p class="ramkazdjeciatytul">WILK SPORT TEAM</p><p class="ramkazdjeciatresc">NAJWYŻSZEJ KLASY SPRZĘT POMIAROWY</p><p class="ramkazdjeciatresc">ZWIĘKSZAJĄCY EFEKTYWNOŚĆ TRENINGÓW</p>';
	var tresc03='<p class="ramkazdjeciatytul">WILK SPORT TEAM</p><p class="ramkazdjeciatresc">PROFESJONALNE PRZYGOTOWANIE MOTORYCZNE</p>';
									  
	var images = ['./media/desktop/Zdjecie01.jpg','./media/desktop/Zdjecie02.jpg','./media/desktop/Zdjecie03.jpg'];
	var imagestlo=['#000000','#000000','#000000'];
	var tresci=[tresc01,tresc02,tresc03];
									  
	//Initial Background image setup and text
	$('#ramkazdjecia').css('background-image', 'url(' + images [zmzi-1] +')');
	$('#ramkazdjeciatresc').html(tresci[0]);
	$('#ramkazdjeciatresc').css('textShadow','#000000 1px 1px 1px');
	
	//Change image at regular intervals
	setInterval(function(){
		$('#ramkazdjecia,#ramkazdjeciatresc').fadeOut(predkosczmiany).promise().done(function () {
			$('#ramkazdjecia').css('background-image', 'url(' + images [zmzi++] +')');
			$('#ramkazdjeciatresc').html(tresci[zmzi-1]);
			$('#ramkazdjeciatresc').css('textShadow','#000000 1px 1px 1px');
			$('#ramkazdjecia,#ramkazdjeciatresc').fadeIn(predkosczmiany).promise().done(function(){});
		});
		if(zmzi == images.length) zmzi=0;
	}, czaszmiany);           
}
//--------------------