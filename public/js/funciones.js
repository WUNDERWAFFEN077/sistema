function getSelectionStart(o) {
    if (o.createTextRange) {
        var r = document.selection.createRange().duplicate();
        r.moveEnd('character', o.value.length);
        if (r.text == '') return o.value.length;
        return o.value.lastIndexOf(r.text);
    } else return o.selectionStart
}

function precise_round(num, decimals) {
	var t=Math.pow(10, decimals);
	return (Math.round((num * t) + (decimals>0?1:0)*(Math.sign(num) * (10 / Math.pow(100, decimals)))) / t).toFixed(decimals);
}


function tipo_documento(valor){
	var tipo = '';
	switch (valor)
	{
	   case "01":
	   		tipo = 'Factura';
	   		break;
	   case "03":
	   		tipo = 'Boleta';
	   		break;
	   case "12": 
			tipo = 'Ticket';
			break;
	   default: 
			tipo = '';
			break;
	}
	return tipo;
}