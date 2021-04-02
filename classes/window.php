<?php
if(!class_exists('window'))
{
    class window
	{
		//----------------------------------------------------------------------------------------------------
	    public function __construct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function create($content)
		{
			$rettext="
					<script>
					
						window.onclick = function(e)
						{
							if(e.target.tagName=='A' || e.target.tagName=='INPUT')
							{
								var element=e.target;
								
								var bezpiecznik=10;
								while(element.parentNode!=null && bezpiecznik--)
								{
									if(element.parentNode.id=='dxy')
									{
										bezpiecznik=0;
										e.target.href;
										try
										{
											var searchEles = element.parentNode.children;
											var znaleziony=null;
											for (var i = 0; i < searchEles.length; i++)
											{
    										if (searchEles[i].id.indexOf('dxy_zawartosc') == 0)
    										{
        									znaleziony=searchEles[i];
        									break;
        								}
        							}
        							if(znaleziony)
        							{
        								var bezpiecznik=10;
        								var el=e.target;
        								while(el!=null && el.tagName!='A' && el.tagName!='FORM' && bezpiecznik--)el=el.parentNode;
        								var formData=null;
        								if(el.tagName=='FORM')
        									formData = new FormData(el);
												ajax.pobierzzawartosc(el.action?el.action:el.href?el.href:'',znaleziony,formData);
											}
										}
										catch(err)
										{
											console.log(err);
										}
										finally
										{
    									return false;
										}
									}
									element=element.parentNode
								}
							}
						};
						
						
						
    					var xPosition=0;
        				var yPosition=0;
        				var startX=0;
        				var startY=0;
        				var startWidth=0;
        				var startHeight=0;
    				
        				function setlistner(e)
        				{
        					var div = document.getElementById('dxy');
      						xPosition = e.clientX-div.offsetLeft;
    						yPosition = e.clientY-div.offsetTop;
        					window.addEventListener(\"mousemove\", divMove, true);
        				}
        				
        				function stoplistner()
        				{
        					var div = document.getElementById('dxy');
        					window.removeEventListener(\"mousemove\", divMove, true);
        				}
        				
    					function divMove(e)
    					{
    				        var div = document.getElementById('dxy');
    				        div.style.top = (e.clientY-yPosition) + 'px';
    				        div.style.left = (e.clientX-xPosition) + 'px';
    					}   
						
                        function setlistner2(e)
                        {
				            startX = e.clientX;
				            startY = e.clientY;
                            startWidth = parseInt(document.getElementById('dxy').style.width, 10);
                            startHeight = parseInt(document.getElementById('dxy').style.height, 10);
                            document.documentElement.addEventListener('mousemove', doDrag, false);
                            document.documentElement.addEventListener('mouseup', stopDrag, false);
				        }
						
					
						function doDrag(e)
						{
                            document.getElementById('dxy').style.width = (startWidth + e.clientX - startX) + 'px';
							document.getElementById('dxy').style.height = (startHeight + e.clientY - startY) + 'px';
						}
						
						function stopDrag(e)
                        {
                            document.documentElement.removeEventListener('mousemove', doDrag, false);    
                            document.documentElement.removeEventListener('mouseup', stopDrag, false);
						}
						
                    </script>
					<div id='dxy'  style='position:absolute;width:300px;height:300px;border:5px solid red;background:yellow;overflow:hidden;'>
						<div style='width:100%;height:20px;background:green;cursor:pointer;' onmousedown='setlistner(event)' onmouseup='stoplistner()'></div>
						<div id='dxy_zawartosc'>
							$content
						</div>
						<div id='resizer' onmousedown='setlistner2(event)' style='border:2px solid black;width: 15px; height: 15px; background: blue; position:absolute; right: 0; bottom: 0; cursor: se-resize;'></div>
					</div>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>