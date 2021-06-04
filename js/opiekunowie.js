if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.opiekunowie = function()
{
	this.blocks_count = 0;
	this.select_opiekunowie_options = new Array();
	this.opiekunowie_blocks = document.getElementById("opiekunowie_blocks");
};
//----------------------------------------------------------------------------------------------------
LabNode.opiekunowie.prototype.create_block=function(ido)
{
	this.blocks_count++;

	var select_field = document.createElement("select");
	select_field.name = "ido["+this.blocks_count.toString()+"]";
	select_field.options[select_field.options.length] = new Option("",0);
	select_field.options[select_field.options.length] = new Option("nowy->",-1);
	for(i = 0; i < this.select_opiekunowie_options.length; i++)
	{
		select_field.options[select_field.options.length] = new Option(this.select_opiekunowie_options[i][1],this.select_opiekunowie_options[i][0],(ido == this.select_opiekunowie_options[i][0]),(ido == this.select_opiekunowie_options[i][0]));
	}
	select_field.onchange = function(par1,par2){return function(){if(this.value == -1)par1.show_additional_fields("additional_fields"+par2); else par1.hidde_additional_fields("additional_fields"+par2);};}(this,this.blocks_count.toString());

	var delete_button = document.createElement("button");
	delete_button.innerHTML = "-";
	delete_button.type = "button";
	delete_button.setAttribute("style", "margin-left:10px;");
	delete_button.onclick = function(par1,par2){return function(){par1.delete_block("opiekunowie_blok"+par2);par1.delete_block("additional_fields"+par2);}}(this,this.blocks_count.toString());

	this.create_wiersz("opiekunowie_blok"+this.blocks_count.toString(),"Opiekun:",select_field,delete_button);

	//tworze div na nowe pola
	var additional_fields = document.createElement("div");
	additional_fields.setAttribute("id", "additional_fields"+this.blocks_count.toString());
	additional_fields.setAttribute("style", "display:none;");

	document.getElementById("opiekunowie_blocks").parentNode.insertBefore(additional_fields, document.getElementById("opiekunowie_blocks"));

	//dodaje nowe pola imie
	var  additional_fields_input_imie_opiekun = document.createElement("input");
	additional_fields_input_imie_opiekun.type = "text";
	additional_fields_input_imie_opiekun.name = "imie_opiekun["+this.blocks_count.toString()+"]";
	this.create_wiersz2("additional_fields"+this.blocks_count.toString(),"imię:",additional_fields_input_imie_opiekun,null);

	//dodaje nowe pola nazwisko
	var  additional_fields_input_imie_opiekun = document.createElement("input");
	additional_fields_input_imie_opiekun.type = "text";
	additional_fields_input_imie_opiekun.name = "nazwisko_opiekun["+this.blocks_count.toString()+"]";
	this.create_wiersz2("additional_fields"+this.blocks_count.toString(),"nazwisko:",additional_fields_input_imie_opiekun,null);

	//dodaje nowe pola telefon
	var  additional_fields_input_imie_opiekun = document.createElement("input");
	additional_fields_input_imie_opiekun.type = "text";
	additional_fields_input_imie_opiekun.name = "telefon_opiekun["+this.blocks_count.toString()+"]";
	this.create_wiersz2("additional_fields"+this.blocks_count.toString(),"telefon:",additional_fields_input_imie_opiekun,null);
	
	//dodaje nowe pola e-mail
	var  additional_fields_input_imie_opiekun = document.createElement("input");
	additional_fields_input_imie_opiekun.type = "text";
	additional_fields_input_imie_opiekun.name = "email_opiekun["+this.blocks_count.toString()+"]";
	this.create_wiersz2("additional_fields"+this.blocks_count.toString(),"e-mail:",additional_fields_input_imie_opiekun,null);
}
//----------------------------------------------------------------------------------------------------
LabNode.opiekunowie.prototype.delete_block=function(blok_id)
{
	var block_to_delete = document.getElementById(blok_id);
	block_to_delete.innerHTML = "";
	block_to_delete.parentNode.removeChild(block_to_delete);
}
//----------------------------------------------------------------------------------------------------
LabNode.opiekunowie.prototype.show_additional_fields=function(blok_id)
{
	var block_to_unhide = document.getElementById(blok_id);
	block_to_unhide.style.display = "block";
}
//----------------------------------------------------------------------------------------------------
LabNode.opiekunowie.prototype.hidde_additional_fields=function(blok_id)
{
	var block_to_hide = document.getElementById(blok_id);
	block_to_hide.style.display = "none";
}
//----------------------------------------------------------------------------------------------------
LabNode.opiekunowie.prototype.create_wiersz=function(id,title,field,field2)
{
	var additional_fields_wiersz = document.createElement("div");
	additional_fields_wiersz.className = "wiersz";
	additional_fields_wiersz.setAttribute("id", id);

	var additional_fields_formularzkom1 = document.createElement("div");
	additional_fields_formularzkom1.className = "formularzkom1";
	additional_fields_formularzkom1.innerHTML = title;

	var additional_fields_formularzkom2 = document.createElement("div");
	additional_fields_formularzkom2.className = "formularzkom2";

	additional_fields_formularzkom2.appendChild(field);
	if(field2)
	{
		additional_fields_formularzkom2.appendChild(field2);
	}

	additional_fields_wiersz.appendChild(additional_fields_formularzkom1);
	additional_fields_wiersz.appendChild(additional_fields_formularzkom2);

	document.getElementById("opiekunowie_blocks").parentNode.insertBefore(additional_fields_wiersz, document.getElementById("opiekunowie_blocks"));
}
//----------------------------------------------------------------------------------------------------
LabNode.opiekunowie.prototype.create_wiersz2=function(place,title,field,field2)
{
	var additional_fields_wiersz = document.createElement("div");
	additional_fields_wiersz.className = "wiersz";

	var additional_fields_formularzkom1 = document.createElement("div");
	additional_fields_formularzkom1.className = "formularzkom1";
	additional_fields_formularzkom1.innerHTML = title;

	var additional_fields_formularzkom2 = document.createElement("div");
	additional_fields_formularzkom2.className = "formularzkom2";

	additional_fields_formularzkom2.appendChild(field);
	if(field2)
	{
		additional_fields_formularzkom2.appendChild(field2);
	}

	additional_fields_wiersz.appendChild(additional_fields_formularzkom1);
	additional_fields_wiersz.appendChild(additional_fields_formularzkom2);

	document.getElementById(place).appendChild(additional_fields_wiersz);
}
//----------------------------------------------------------------------------------------------------
var opiekunowie=new LabNode.opiekunowie;