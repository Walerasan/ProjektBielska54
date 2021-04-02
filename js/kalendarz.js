/**
 * Rafał Oleśkowicz
 * Kalendarz do wyboru daty w formularzach
 */
if(!window.LabNode)
	window.LabNode={};

LabNode.kalendarz=function(id)
{
	this.id=id;
	this.warstwanosnika;
	this.warstwazaciemnienia;
	this.dayName=new Array("Niedziela","Poniedziałek","Wtorek","Środa","Czwartek","Piątek","Sobota");
	this.monthName=new Array("Styczeń","Luty","Marzec","Kwiecień","Maj","Czerwiec","Lipiec","Sierpień","Wrzesień","Październik","Listopad","Grudzień");
	this.monthDays=new Array(31,28,31,30,31,30,31,31,30,31,30,31);
	this.kolornapisow='white';
	this.kolortla='black';
	this.kolordniaoff='black';
	this.kolordniaon='blue';
	this.kolordniatoday='orange';
	this.kolornapisowniedziela='red';
	this.kolornapisowsobota='gray';
	this.withtime=true;
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.pokaz=function(e,year,month,day,hour,minute,second,parentobj,withtime)
{
	this.parentobj=parentobj;
	this.withtime=withtime;
	var data=new Date();
	if(year==""&& month=="" && day=="" && hour=="" && minute=="" && second=="")
	{
		//odczytuje zawartość pola - jezeli jest to probuje zrobić z tego datę.
		//rozbijam date wedlug spacji
		if(this.parentobj.value)
		{
			var DataGodzina = this.parentobj.value.split(' ');
			if(DataGodzina!="")
			{
				//rozbijam date wedlug -
				var datapola=DataGodzina[0].split('-');
				//rozbijam godzine wedlug :
				var godzinapola=new Array;
				if(typeof DataGodzina[1]!='undefined' && DataGodzina[1]!="")
					godzinapola=DataGodzina[1].split(':');
				else
				{
					godzinapola[0]=godzinapola[1]=godzinapola[2]=0;
				}
				if(this.sprawdzdate(datapola[0],datapola[1],datapola[2]) && this.sprawdzgodzine(godzinapola[0],godzinapola[1],godzinapola[2]))
				{
					day=datapola[2];
					month=datapola[1]-1;
					year=datapola[0];
					hour=godzinapola[0];
					minute=godzinapola[1];
					second=godzinapola[2];
				}
			}
		}
	}
	
	this.nowd=(day==null || day==="")?data.getDate():day;
	this.nowm=(month==null || month==="")?data.getMonth():month;
	this.nowy=(year==null || year==="")?data.getFullYear():year;
	this.h=(hour==null || hour==="")?data.getHours():hour;
	this.m=(minute==null || minute==="")?data.getMinutes():minute;
	this.s=(second==null || second==="")?data.getSeconds():second;
	//wlanczam zaciemnienie
	if(!e)e=window.event;
	
	//this.warstwanosnika=this.warstwanosnikaon(this.parentobj.offsetLeft+this.parentobj.offsetWidth,this.parentobj.offsetTop);
	this.warstwanosnika=this.warstwanosnikaon(e.clientX,e.clientY+this.getScrollY());
	
	//wyswietlam kalendarz
	if ((this.nowy%4==0||this.nowy%100==0)&&(this.nowy%400==0))this.monthDays[1]=29;else this.monthDays[1]=28;//sprawdzam rok przestępny
	
	var wybierzmiesiac="<select id='wybierzmc'>" +
	"<option value='0' "+(this.nowm==0?"selected='selected'":"")+">"+this.monthName[0].toUpperCase()+"</option>"+
	"<option value='1' "+(this.nowm==1?"selected='selected'":"")+">"+this.monthName[1].toUpperCase()+"</option>"+
	"<option value='2' "+(this.nowm==2?"selected='selected'":"")+">"+this.monthName[2].toUpperCase()+"</option>"+
	"<option value='3' "+(this.nowm==3?"selected='selected'":"")+">"+this.monthName[3].toUpperCase()+"</option>"+
	"<option value='4' "+(this.nowm==4?"selected='selected'":"")+">"+this.monthName[4].toUpperCase()+"</option>"+
	"<option value='5' "+(this.nowm==5?"selected='selected'":"")+">"+this.monthName[5].toUpperCase()+"</option>"+
	"<option value='6' "+(this.nowm==6?"selected='selected'":"")+">"+this.monthName[6].toUpperCase()+"</option>"+
	"<option value='7' "+(this.nowm==7?"selected='selected'":"")+">"+this.monthName[7].toUpperCase()+"</option>"+
	"<option value='8' "+(this.nowm==8?"selected='selected'":"")+">"+this.monthName[8].toUpperCase()+"</option>"+
	"<option value='9' "+(this.nowm==9?"selected='selected'":"")+">"+this.monthName[9].toUpperCase()+"</option>"+
	"<option value='10' "+(this.nowm==10?"selected='selected'":"")+">"+this.monthName[10].toUpperCase()+"</option>"+
	"<option value='11' "+(this.nowm==11?"selected='selected'":"")+">"+this.monthName[11].toUpperCase()+"</option>"+
	"</select>";
	
	var polegodziny="<input type='text' name='godzinaKal' id='godzinaKal' value='"+this.h+"' maxlength='2' style='width:20px;text-align:center;'/>";
	var poleminuty="<input type='text' name='minuta' id='minuta' value='"+this.m+"' maxlength='2' style='width:20px;text-align:center;'/>";
	var polesekundy="<input type='text' name='sekunda' id='sekunda' value='"+this.s+"' maxlength='2' style='width:20px;text-align:center;'/>";
	var przyciskanuluj="<button title='anuluj' id='anuluj' type='button' style='width:50px;font-size:8pt;'>anuluj</button>";
	var przyciskok="<button title='ok' id='ok' type='button' style='width:50px;font-size:8pt;'>ok</button>";
	var dzisiaj="<a id='dzisiaj' style='cursor:hand;cursor:pointer;color:"+this.kolordniatoday+";'>*</a>";
	
	var wybierzrok="<select id='wybierzrok'>";
	for(var j=1980;j<2200;j++)
		wybierzrok+="<option value='"+j+"' "+(this.nowy==j?"selected='selected'":"")+">"+j+"</option>";
	wybierzrok+="</select>";
	
	//wyświetlam nazwę mc i rok
	var tytul=wybierzmiesiac+" "+wybierzrok;
	
	//wyświetlam nazwy tygodni
	var nazwydni="";
	for(var i=1;i<this.dayName.length;i++)//tu zmienilem z i=0
		if(i==6)//sobota
			nazwydni+="<td style='color:"+this.kolornapisowsobota+"'>"+this.dayName[i].substring(0,2)+"</td>";
		else
			nazwydni+="<td style='color:"+this.kolornapisow+"'>"+this.dayName[i].substring(0,2)+"</td>";
	//dodaje niedziele
	nazwydni+="<td style='color:"+this.kolornapisowniedziela+"'>"+this.dayName[0].substring(0,2)+"</td>";
	
	
	//przesuwam się na odpowiednia pozycje (pod dzien tygodnia)
	var odstepstartu="";
	var firstDay=new Date(this.nowy,this.nowm,1).getDay();
	if(firstDay==0)firstDay=7;//jezeli niedziela to na koniec
	for (var i=1;i<firstDay;i++)//tu zmieniłem z i=0
		odstepstartu+="<td> </td>\n";
	
	//wyświetlam dni miesiaca
	var dayCount=1;
	var calStr="<tr>\n"+odstepstartu;
	var kolordnia='black';
	var kolordniadzisiejszego='black';
	for(var i=0;i<this.monthDays[this.nowm];i++)
	{
		kolordnia=(i+1==this.nowd?this.kolordniaon:this.kolordniaoff);
		if(data.getDate()==dayCount && data.getMonth()==this.nowm && data.getFullYear()==this.nowy)
			kolordniadzisiejszego=this.kolordniatoday;
		else
			kolordniadzisiejszego=this.kolortla;
		//ustalam kolor dnia tygodnia
		if((i+firstDay)%7==0)//to znaczy ze jest niedziela
			kolordniatygodnia=this.kolornapisowniedziela;
		else if((i+firstDay+1)%7==0)//to znaczy ze jest sobota
			kolordniatygodnia=this.kolornapisowsobota;
		else
			kolordniatygodnia=this.kolornapisow;
		
		calStr+="<td style='background:"+kolordnia+";border:1px solid "+kolordniadzisiejszego+";'><a id='dzien"+dayCount+"' style='cursor:hand;cursor:pointer;color:"+kolordniatygodnia+";'>"+dayCount+"</a></td>\n";
		dayCount++;
		if((i+firstDay)%7==0&&(dayCount<this.monthDays[this.nowm]+1))//tu zmienilem z (i+firstDay)%7+1==
			calStr+="</tr>\n<tr>\n";
	}
	calStr+="</tr>\n";
	
	
	var rettext="";
	rettext+="<table>";
	rettext+="<tr><td colspan='7'><a id='kalcback' style='cursor:hand;cursor:pointer;color:"+this.kolornapisow+"'>&lt;</a> "+tytul+" <a id='kalcgo' style='cursor:hand;cursor:pointer;color:"+this.kolornapisow+"'>&gt;</a></td></tr>";
	rettext+="<tr>"+nazwydni+"</tr>";
	rettext+=calStr;
	rettext+="</table>";
	rettext+="<div style='text-align:right;padding-right:10px;'>"+dzisiaj+" "+polegodziny+":"+poleminuty+":"+polesekundy+" "+przyciskok+" "+przyciskanuluj+"</div>";
	this.warstwanosnika.innerHTML=rettext;
	
	document.getElementById('kalcback').onclick=function(par1,par2,par3,par4,par5,par6,par7,par8){return function(){var e=arguments[0] || event;par1.uaktualnij(e,par1,par2,par3,par4,document.getElementById('godzinaKal').value,document.getElementById('minuta').value,document.getElementById('sekunda').value,par8);};}(this,this.nowy,this.nowm-1,this.nowd,document.getElementById('godzinaKal').value,this.m,this.s,this.parentobj);
	document.getElementById('kalcgo').onclick=function(par1,par2,par3,par4,par5,par6,par7,par8){return function(){var e=arguments[0] || event;par1.uaktualnij(e,par1,par2,par3,par4,document.getElementById('godzinaKal').value,document.getElementById('minuta').value,document.getElementById('sekunda').value,par8);};}(this,this.nowy,this.nowm+1,this.nowd,document.getElementById('godzinaKal').value,this.m,this.s,this.parentobj);
	document.getElementById('wybierzmc').onchange=function(par1,par2,par3,par4,par5,par6,par7,par8){return function(){var e=arguments[0] || event;par1.uaktualnij(e,par1,par2,this.value,par4,document.getElementById('godzinaKal').value,document.getElementById('minuta').value,document.getElementById('sekunda').value,par8);};}(this,this.nowy,this.nowm,this.nowd,document.getElementById('godzinaKal').value,this.m,this.s,this.parentobj);
	document.getElementById('wybierzrok').onchange=function(par1,par2,par3,par4,par5,par6,par7,par8){return function(){var e=arguments[0] || event;par1.uaktualnij(e,par1,this.value,par3,par4,document.getElementById('godzinaKal').value,document.getElementById('minuta').value,document.getElementById('sekunda').value,par8);};}(this,this.nowy,this.nowm,this.nowd,document.getElementById('godzinaKal').value,this.m,this.s,this.parentobj);
	document.getElementById('anuluj').onclick=function(par1){return function(){par1.wylaczkalendarz();};}(this);
	document.getElementById('ok').onclick=function(par1,par2,par3,par4,par5,par6,par7){return function(){par1.wstaw(par1,par2,par3,par4,document.getElementById('godzinaKal').value,document.getElementById('minuta').value,document.getElementById('sekunda').value);};}(this,this.nowy,this.nowm,this.nowd,this.h,this.m,this.s);
	document.getElementById('dzisiaj').onclick=function(par1,par2,par3,par4,par5,par6,par7,par8){return function(){var e=arguments[0] || event;par1.uaktualnij(e,par1,this.value,par3,par4,document.getElementById('godzinaKal').value,document.getElementById('minuta').value,document.getElementById('sekunda').value,par8);};}(this,"","","","","","",this.parentobj);

	//nadaje operacje dniom tygodnia
	var dayCount=1;
	for(var i=0;i<this.monthDays[this.nowm];i++)
	{
		document.getElementById('dzien'+dayCount).ondblclick=function(par1,par2,par3,par4,par5,par6,par7){return function(){par1.wstaw(par1,par2,par3,par4,document.getElementById('godzinaKal').value,document.getElementById('minuta').value,document.getElementById('sekunda').value);};}(this,this.nowy,this.nowm,dayCount,this.h,document.getElementById('godzinaKal').value,this.s);
		document.getElementById('dzien'+dayCount).onclick=function(par1,par2,par3,par4,par5,par6,par7,par8){return function(){var e=arguments[0] || event;par1.uaktualnij(e,par1,par2,par3,par4,document.getElementById('godzinaKal').value,document.getElementById('minuta').value,document.getElementById('sekunda').value,par8);};}(this,this.nowy,this.nowm,dayCount,this.h,document.getElementById('godzinaKal').value,this.s,this.parentobj);
		dayCount++;
	}
		
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.wstaw=function(par1,par2,par3,par4,par5,par6,par7)
{
	par3++;
	var wyrazenie = new RegExp("^[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$");
	var answer;
	var bezpiecznik=1000;
	do
	{
		if(par5=="" || par6=="")
		{
			answer=prompt("Godzina?",par5+":"+par6+":"+par7);
			if(answer==null)
			{
				answer="";
				break;
			};
			if(!wyrazenie.test(answer))
				alert("Prawidłowa składnia to GG:MM:SS");
		}
		else
		{
			if(par7=="")par7="00";
			answer=par5+":"+par6+":"+par7;
		};
		
	}while(!wyrazenie.test(answer) && --bezpiecznik>0);
	if(this.withtime==false)answer="";
	if(bezpiecznik==0)alert("Spalono bezpiecznik: "+par2+","+par3+","+par4+","+par5+","+par6+","+par7);
	par1.parentobj.value=par2+"-"+par3+"-"+par4+" "+answer;
	par1.anulujkalendarz();
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.uaktualnij=function(e,klasa,year,month,day,hour,minute,second,parentobj)
{
	klasa.warstwanosnika.innerHTML="";
	if(month<0)
	{
		year--;
		month=11;
	}
	if(month>11)
	{
		year++;
		month=0;
	}
	klasa.pokaz(e,year,month,day,hour,minute,second,parentobj);
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.anulujkalendarz=function()
{
	//usuwam warstwy
	this.usunwarstwe(this.warstwanosnika);
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.wylaczkalendarz=function()
{
	//usuwam warstwy
	this.usunwarstwe(this.warstwanosnika);
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.usunwarstwe=function(uchwyt)
{
	var rodzic=uchwyt.parentNode;
	rodzic.removeChild(uchwyt);
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.warstwanosnikaon=function(x,y)
{
	if(!document.getElementById('nosnikkalendarza'))
	{
		//tworze diva na cala szerokosc strony z zaciemnieniem
		//tworzymy warstwe na ktorej to bedzie lezec
		var nosnikedytora= document.createElement('div');
		nosnikedytora.setAttribute('id','nosnikkalendarza');
		nosnikedytora.setAttribute('style', 'background:'+this.kolortla+';color:white;text-align:center;width:225px;height:230px;position:absolute;left:'+x+'px;top:'+y+'px;z-index:200;');
		//wstawiam div do body
		document.body.appendChild(nosnikedytora);
		return nosnikedytora;
	}
	else
		return document.getElementById('nosnikkalendarza');
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.sprawdzdate=function(y,m,d)
{
	m--;
	if(y==new Date(y,m,d).getFullYear() && m==new Date(y,m,d).getMonth() && d==new Date(y,m,d).getDate())
		return true;
	return false;
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.sprawdzgodzine=function(g,m,s)
{
	if(g>=0 && g<=23 && m>=0 && m<=59 && s>=0 && s<=59)
		return true;
	return false;
};
//----------------------------------------------------------------------------------------------------
LabNode.kalendarz.prototype.getScrollY=function()
{
	  var scrOfY = 0;
	  if(typeof( window.pageYOffset )=='number' )
	  {
	    //Netscape compliant
	    scrOfY = window.pageYOffset;
	  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) )
	  {
	    //DOM compliant
	    scrOfY = document.body.scrollTop;
	  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) )
	  {
	    //IE6 standards compliant mode
	    scrOfY = document.documentElement.scrollTop;
	  };
	  return scrOfY;
};