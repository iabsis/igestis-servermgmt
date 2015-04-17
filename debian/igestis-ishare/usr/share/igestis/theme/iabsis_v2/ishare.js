function ishare_draw_progress_bar(procent, width) {
	
	var r = 0;
	var g = 0;
	var b = 60;
	
	g = parseInt(255 * (100 - procent) / 100);
	r = parseInt(255 * procent / 100);
	
	document.write("<div style=\"float:left; background-color:#FFFFFF; border:1px solid #BBBBBB; width:" + width + "px; height:15px;\">");
	document.write("<div style=\"background-color:#" + hexa_color(r, g, b) + "; width:" + parseInt(width * procent / 100) + "px; height:15px;\"></div>");
	document.write("</div>&nbsp;" + parseInt(procent) + " %");

}

function hexa_color(r, g, b) {
	var hexa = "";
	hexa = add_zeros(r.toString(16)) + add_zeros(g.toString(16)) + add_zeros(b.toString(16));
	return hexa;
}

function add_zeros(string) {
	if(string.length == 0) string = "0";
	if(string.length == 1) string = "0" + string;
	return string;
}