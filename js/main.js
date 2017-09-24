/**
 *  Submit form
 */
function appFormSubmit(frm_name_id, vars){
	if(document.getElementById(frm_name_id)){
		if(vars != null){
			var vars_pairs = vars.split('&');
			var pair = '';
			for(var i=0; i<vars_pairs.length; i++){
				pair = vars_pairs[i].split('=');
				for(var j=0; j<pair.length; j+=2) {
					if(document.getElementById(pair[j]))
          document.getElementById(pair[j]).value = pair[j+1];
				}
			}
		}
		document.getElementById(frm_name_id).submit();
	}
}


/**
 *   Show element
 */
function appShowElement(key){
	if(key.indexOf('#') !=-1 || key.indexOf('.') !=-1){
		jQuery(key).show('fast');
	}else{
		jQuery('#'+key).show('fast');
	}
}

/**
 *   Hide element
 */
function appHideElement(key){
	if(key.indexOf('#') !=-1 || key.indexOf('.') !=-1){
		jQuery(key).hide('fast');
	}else{
		jQuery('#'+key).hide('fast');
	}
}
