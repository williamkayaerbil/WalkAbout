var Utils = {};
Utils.ajaxGet = function(url, params, callback, beforeSend, always){
	params = params || {};

	var jqXHR = $.ajax({
		url: url,
		type: 'POST',
		data: {formdata: JSON.stringify(params)},
		// dataType: "json",

		beforeSend: function(){
			if ($.isFunction(beforeSend)){ beforeSend(); }
		}
	})

	.fail(function(jqXHR, textStatus, errorThrown) { console.log(errorThrown); })

	.always(function() {
		if ($.isFunction(always)){ always(); }
		jqXHR = null; 
	})

	.done(function(data){
		console.log(data);
		if ($.isFunction(callback)) { callback(data); }
	});

	return jqXHR;

};


Utils.previewImage = function(fileElmtID, imgElmtID) {
	if ("string"!==typeof fileElmtID || "string"!==typeof imgElmtID) return;
	
	var oFReader = new FileReader();
	oFReader.readAsDataURL(document.getElementById(fileElmtID).files[0]);

	oFReader.onload = function (oFREvent) {
	   document.getElementById(imgElmtID).src = oFREvent.target.result;
	};
};