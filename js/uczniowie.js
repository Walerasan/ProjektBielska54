if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.uczniowie = function()
{
};
//----------------------------------------------------------------------------------------------------
LabNode.uczniowie.prototype.close_all=function(idu)
{
	alert("zamykam wszystko");
}
//----------------------------------------------------------------------------------------------------
LabNode.uczniowie.prototype.open=function(idu)
{
	this.uczniowie_blocks = document.getElementById("uczniowie_blocks");
	if(this.uczniowie_blocks != null)
	{
		for (var i=1;i<this.uczniowie_blocks.rows.length;i++)//pierwszy pomijam tam są tytuły
		{
			if( this.uczniowie_blocks.rows[i].id == "wiersz"+idu+"_szczagoly")
			{
				if(this.uczniowie_blocks.rows[i].style.display === "none")
				{
					this.uczniowie_blocks.rows[i].style.display = "table-row";
				}
				else
				{
					this.uczniowie_blocks.rows[i].style.display = "none";
				}
				//alert(this.uczniowie_blocks.rows[i].style.display );//=== 'block'
			}
			else if(this.uczniowie_blocks.rows[i].id.includes("_szczagoly"))
			{
				if(this.uczniowie_blocks.rows[i].style.display === "table-row")
				{
					this.uczniowie_blocks.rows[i].style.display = "none";
				}
			}
		}
	}
	//alert(this.uczniowie_blocks.rows.length);
	//uczniowie.close_all($idu);
	//alert("otwieram lub zamykam "+idu);
}
//----------------------------------------------------------------------------------------------------
var uczniowie=new LabNode.uczniowie;
