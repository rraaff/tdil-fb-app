var arrWindows = new Array('');
var blnPopupFound = false;
var objWindowFounded = false;

function openPopup(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, blnShowBorders, blnShowScroll, strName, blnRefresh, strParametros){
	
	if (arrWindows.length == 1)
		openPopupWindow(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, blnShowBorders, blnShowScroll, strName, false, false, false, strParametros);
	else{
		blnPopupFound = false;
		objWindowFounded = false;
		if (!blnRefresh)
			findPopupWindow(strName);

		if (!blnPopupFound)
			openPopupWindow(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, blnShowBorders, blnShowScroll, strName, false, arrWindows.length, false, strParametros);
		else
			objWindowFounded.focus();
	}
}

function findPopupWindow(strName){
	for (i = 1; i < arrWindows.length; i++){
		if (!arrWindows[i].closed){
			if (arrWindows[i].name == strName){
				blnPopupFound = true;
				objWindowFounded = arrWindows[i];
				break;
			}
		}
	}
}

function openPopupWindow(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, blnShowBorders, blnShowScroll, strName, blnPrintMode, intIndex, blnShowStatus, strParametros){
	if (!blnPrintMode){
		strProperties = 'left=' + (((screen.availWidth - intAncho) / 2) + ((intDesfasajeAncho) ? intDesfasajeAncho : 0));
		strProperties += ',top=' + (((screen.availHeight - intAlto) / 2) + ((intDesfasajeAlto) ? intDesfasajeAlto : 0));
		strProperties += ',width=' + intAncho + ',height=' + intAlto + ',menubar=no,resizable=no';
		strProperties += (blnShowStatus) ? ',status=yes' : ',status=no';
		strProperties += (blnShowScroll) ? ',scrollbars=yes' : ',scrollbars=no';
		strProperties += (blnShowBorders) ? ',fullscreen=yes' : '';
	}else
		strProperties = 'left=2000,top=2000,width=0,height=0,menubar=no,resizable=no,status=no';

	intWindow = (intIndex) ? intIndex : arrWindows.length;
	strURL = (strParametros) ? ((strURL.indexOf("?") > -1) ? strURL + "&" + strParametros : strURL + "?" + strParametros) : strURL;
	arrWindows[intWindow] = window.open(strURL, strName, strProperties);
	if (blnShowBorders && !blnPrintMode && (document.all) && (!document.getElementById)){
		self.focus();
		intLeft = ((window.screen.width - intAncho) / 2) + ((intDesfasajeAncho) ? intDesfasajeAncho : 0);
		intTop = ((window.screen.height - intAlto) / 2) + ((intDesfasajeAlto) ? intDesfasajeAlto : 0);
		setTimeout("arrWindows[" + intWindow + "].resizeTo(" + intAncho + "," + intAlto + ")", 500);
		setTimeout("arrWindows[" + intWindow + "].moveTo(" + intLeft + ", " + intTop + ")", 500);
	}
	setTimeout("arrWindows[" + intWindow + "].focus()", 500);
}

function openPopupPrint(strURL, strName){
	openPopupWindow(strURL, 0, 0, 0, 0, false, false, strName, true, false);
}

function closePopup(strName){
	findPopupWindow(strName);
	if (blnPopupFound)
		objWindowFounded.close();
	else{
		openPopupPrint("", strName);
		if (arrWindows[arrWindows.length])
			setTimeout("arrWindows[arrWindows.length].close()", 50);
	}
}

function openPopUpSite(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, strName){
	openPopupWindow(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, false, true, strName, false, false, true);
}
