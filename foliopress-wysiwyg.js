var g_iLink = 0;
var g_iLightbox = 0;

function KFMLinkLightboxStart( iLink, iLightbox ){
	if( !bAdvanced ){
		g_iLink = iLink;
		g_iLightbox = iLightbox;
	}
	
	var elChkLink = document.getElementById( 'chkKFMLink' );
	var elChkLightbox = document.getElementById( 'chkKFMLightbox' );

	if( 1 == iLightbox ) elChkLightbox.checked = true;
	if( 1 == iLink ) elChkLink.checked = true;
	else elChkLightbox.disabled = true;
}

function KFMLink_change(){
	var elChkLink = document.getElementById( 'chkKFMLink' );
	var elChkLightbox = document.getElementById( 'chkKFMLightbox' );

	if( elChkLink.checked ) elChkLightbox.disabled = false;
	else elChkLightbox.disabled = true;
}


var iThumbs = 0;

function KFMThumbsStart( aThumbs ){
	var elListThumbs = document.getElementById( 'listKFMThumbs' );
	var hidThumbsCount = document.getElementById( 'hidThumbCount' );
	
	for( i=0; i<aThumbs.length; i++ ){
		var elOptThumb = document.createElement( 'option' );
		elOptThumb.value = String( iThumbs );
		elOptThumb.text = String( aThumbs[i] );
		elListThumbs.options.add( elOptThumb, i );
		
		var elHid = document.createElement( 'input' );
		elHid.id = 'hid_KFMThumb_' + String( iThumbs );
		elHid.type = 'hidden';
		elHid.name = 'KFMThumb' + String( iThumbs );
		elHid.value = String( aThumbs[i] );
		elListThumbs.parentNode.appendChild( elHid );
		iThumbs++;
		
		hidThumbsCount.value = String( iThumbs );
	}
}

function KFMRemoveThumbnail(){
	var elListThumbs = document.getElementById( 'listKFMThumbs' );

	for( i=0; i<elListThumbs.childNodes.length; i++ ){
		if( 1 != elListThumbs.childNodes.item(i).nodeType ) continue;
		
		if( elListThumbs.childNodes.item(i).selected ){
			var hidThumbInfo = document.getElementById( 'hid_KFMThumb_' + elListThumbs.childNodes.item(i).value );
			if( null != hidThumbInfo ) elListThumbs.parentNode.removeChild( hidThumbInfo );
			elListThumbs.removeChild( elListThumbs.childNodes.item(i) );
			break;
		}
	}
}

function KFMAddThumbnail(){
	var elListThumbs = document.getElementById( 'listKFMThumbs' );
	var txtThumbSize = document.getElementById( 'txtAddThumbSize' );
	var hidThumbsCount = document.getElementById( 'hidThumbCount' );
	
	if( isNaN( txtThumbSize.value ) ){
		alert( 'Please write correct size into text box !' );
		return;
	}
	
	var elOptThumb = document.createElement( 'option' );
	elOptThumb.value = String( iThumbs );
	elOptThumb.text = txtThumbSize.value;
	elListThumbs.options.add( elOptThumb, elListThumbs.options.length );
	
	var elHid = document.createElement( 'input' );
	elHid.id = 'hid_KFMThumb_' + String( iThumbs );
	elHid.type = 'hidden';
	elHid.name = 'KFMThumb' + String( iThumbs );
	elHid.value = String( txtThumbSize.value );
	elListThumbs.parentNode.appendChild( elHid );
	iThumbs++;
	
	hidThumbsCount.value = String( iThumbs );
	txtThumbSize.value = '';
}



var iFPClean = 0;

function FPCleanTextStart( aFPClean ){
	var elListFP = document.getElementById( 'listFPClean' );
	var hidFPCount = document.getElementById( 'hidFPCleanCount' );
	
	for( var i=0; i<aFPClean.length; i++ ){
		var elOptFP = document.createElement( 'option' );
		elOptFP.value = String( iFPClean );
		elOptFP.text = String( aFPClean[i] );
		elListFP.options.add( elOptFP, i );
		
		var elHid = document.createElement( 'input' );
		elHid.id = 'hid_FPClean_' + String( iFPClean );
		elHid.type = 'hidden';
		elHid.name = 'FPClean' + String( iFPClean );
		elHid.value = String( aFPClean[i] );
		elListFP.parentNode.appendChild( elHid );
		iFPClean++;
		
		hidFPCount.value = String( iFPClean );
	}
}

function FPCleanRemoveText(){
	var elListFP = document.getElementById( 'listFPClean' );

	for( i=0; i<elListFP.childNodes.length; i++ ){
		if( 1 != elListFP.childNodes.item(i).nodeType ) continue;
		
		if( elListFP.childNodes.item(i).selected ){
			var hidFPInfo = document.getElementById( 'hid_FPClean_' + elListFP.childNodes.item(i).value );
			if( null != hidFPInfo ) elListFP.parentNode.removeChild( hidFPInfo );
			elListFP.removeChild( elListFP.childNodes.item(i) );
			break;
		}
	}
}

function FPCleanAddText(){
	var elListFP = document.getElementById( 'listFPClean' );
	var txtFP = document.getElementById( 'txtAddFPClean' );
	var hidFPCount = document.getElementById( 'hidFPCleanCount' );
	
	if( !FV_Regex.VerifyString( txtFP.value ) ){
		alert( 'String you entered is not well bracketed !' );
		return;
	}
	
	var elOptThumb = document.createElement( 'option' );
	elOptThumb.value = String( iFPClean );
	elOptThumb.text = txtFP.value;
	elListFP.options.add( elOptThumb, elListFP.options.length );
	
	var elHid = document.createElement( 'input' );
	elHid.id = 'hid_FPClean_' + String( iFPClean );
	elHid.type = 'hidden';
	elHid.name = 'FPClean' + String( iFPClean );
	elHid.value = String( txtFP.value );
	elListFP.parentNode.appendChild( elHid );
	iFPClean++;
	
	hidFPCount.value = String( iFPClean );
	txtFP.value = '';
}



var bAdvanced = false;
function ShowAdvancedOptions(){
	var elDivAdvanced = document.getElementById( 'divAdvanced' );
	
	if( bAdvanced ){
		/*var elChkLink = document.getElementById( 'chkKFMLink' );
		if( elChkLink.checked ) g_iLink = 1;
		else g_iLink = 0;
		
		var elChkLightbox = document.getElementById( 'chkKFMLightbox' );
		if( elChkLightbox.checked ) g_iLightbox = 1;
		else g_iLightbox = 0;*/
		
		elDivAdvanced.style.visibility = "hidden";
		elDivAdvanced.style.position = "absolute";
		
		if( bExpert ) ShowExpertOptions();
		
		bAdvanced = false;
	}else{
		elDivAdvanced.style.visibility = "visible";
		elDivAdvanced.style.position = "relative";

		bAdvanced = true;
	}
}


var bExpert = false;
function ShowExpertOptions(){
	var elDivExpert = document.getElementById( 'divExpert' );
	
	if( bExpert ){
		elDivExpert.style.visibility = "hidden";
		elDivExpert.style.position = "absolute";

		bExpert = false;
	}else{
		elDivExpert.style.position = "relative";
		elDivExpert.style.visibility = "visible";
		
		bExpert = true;
	}
}

function KFM_CheckPNG( bSelect ){
	var elPNG = document.getElementById( 'chkPNGTransform' );
	var elPNGLimit = document.getElementById( 'txtPNGLimit' );
	
	if( bSelect ){
		elPNG.checked = true;
		elPNGLimit.disabled = false;
	}
	else{
		elPNG.checked = false;
		elPNGLimit.disabled = true;
	}
	
	bPNGTransform = bSelect;
}
function KFM_CheckDIR( bSelect ){
	var elDIR = document.getElementById( 'chkDIRset' );
	
	if( bSelect ){
		elDIR.checked = true;
	}
	else{
		elDIR.checked = false;
	}
	
	bDIRTransform = bSelect;
}

function FVWYSIWYGPermisssionsUser() {
  var dirperm = document.getElementById( 'dirperm' );
  var fileperm = document.getElementById( 'fileperm' );
  if( dirperm ) {
    dirperm.value = '755';
  }
  if( fileperm ) {
    fileperm.value = '644';
  }
}
function FVWYSIWYGPermisssionsDefault() {
  var dirperm = document.getElementById( 'dirperm' );
  var fileperm = document.getElementById( 'fileperm' );
  if( dirperm ) {
    dirperm.value = '777';
  }
  if( fileperm ) {
    fileperm.value = '666';
  }
}