var mediabrowser;
function tinymceinicjuj(szerokosc,wysokosc,selektor)
{
	mediabrowser=new LabNode.mediabrowser('mediabrowser');
	if (typeof tinymce != 'undefined')
		tinymce.init({
			fontsize_formats: '8px 10px 12px 14px 18px 20px 24px',
			selector: selektor,
			theme: "modern",
			width: szerokosc,
			height: wysokosc,
			doctype : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			plugins: ["advlist autolink link image lists charmap print preview hr anchor pagebreak","searchreplace visualblocks visualchars insertdatetime nonbreaking","table contextmenu directionality emoticons paste textcolor colorpicker youTube code "],
			toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | fontsizeselect | forecolor backcolor",
			toolbar2: "| bullist numlist outdent indent | link unlink anchor | image | youTube | print preview code | media",
			image_advtab: true,
			advimagescale_maintain_aspect_ratio: false,
			object_resizing : "iframe",
			extended_valid_elements:"iframe[src|title|width|height|allowfullscreen|frameborder|class|id|*]",
			content_css : "./css/tinymce.css",
			file_browser_callback :function(field_name, url, type, win){mediabrowser.katalog(field_name,win,type)}
			
		});
}