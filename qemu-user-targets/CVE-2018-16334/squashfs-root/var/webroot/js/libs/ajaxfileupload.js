
jQuery.extend({
	createUploadIframe : function (id, uri) {
		var frameId = 'jUploadFrame' + id,
		iframeHtml = '<iframe id="' + frameId + '" name="' + frameId + '" style="position:absolute; top:-9999px; left:-9999px"';

		if (window.ActiveXObject) {
			if (typeof uri == 'boolean') {
				iframeHtml += ' src="' + 'javascript:false' + '"';
			} else if (typeof uri == 'string') {
				iframeHtml += ' src="' + uri + '"';
			}
		}
		iframeHtml += '/>';
		jQuery(iframeHtml).appendTo(document.body);
		return jQuery('#' + frameId).get(0);
	},
	createUploadForm : function (id, fileElementId, data) {
		var formId = 'jUploadForm' + id;
		var fileId = 'jUploadFile' + id;
		var form = jQuery('<form ' + ' action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
		if (data) {
			for (var i in data) {
				jQuery('<input type="hidden" name="' + i + '" value="' + data[i] + '" />').appendTo(form);
			}
		}
		var oldElement = jQuery('#' + fileElementId);
		var newElement = jQuery(oldElement).clone();
		jQuery(oldElement).attr('id', fileId);
		jQuery(oldElement).before(newElement);
		jQuery(oldElement).appendTo(form);
		jQuery(form).css('position', 'absolute');
		jQuery(form).css('top', '-1200px');
		jQuery(form).css('left', '-1200px');
		jQuery(form).appendTo('body');
		return form;
	},
	ajaxFileUpload : function (s) {
		s = jQuery.extend({}, jQuery.ajaxSettings, s);
		var id = new Date().getTime();
		var form = jQuery.createUploadForm(id, s.fileElementId, (typeof(s.data) == 'undefined' ? false : s.data));
		var io = jQuery.createUploadIframe(id, s.secureuri);
		var frameId = 'jUploadFrame' + id;
		var formId = 'jUploadForm' + id;
		if (s.global && !jQuery.active++) {
			jQuery.event.trigger("ajaxStart");
		}
		var requestDone = false;
		var xml = {}
		if (s.global)
			jQuery.event.trigger("ajaxSend", [xml, s]);
		var uploadCallback = function (isTimeout) {
			var io = document.getElementById(frameId);
			try {
				if (io.contentWindow) {
					xml.responseText = io.contentWindow.document.body ? io.contentWindow.document.body.innerHTML : null;
					xml.responseXML = io.contentWindow.document.XMLDocument ? io.contentWindow.document.XMLDocument : io.contentWindow.document;
				} else if (io.contentDocument) {
					xml.responseText = io.contentDocument.document.body ? io.contentDocument.document.body.innerHTML : null;
					xml.responseXML = io.contentDocument.document.XMLDocument ? io.contentDocument.document.XMLDocument : io.contentDocument.document;
				}
			} catch (e) {
				jQuery.handleError(s, xml, null, e);
			}
			if (xml || isTimeout == "timeout") {
				requestDone = true;
				var status;
				try {
					status = isTimeout != "timeout" ? "success" : "error";
					if (status != "error") {
						//var data = jQuery.uploadHttpData(xml, s.dataType);
						//modify by zzc 这里只当作字符串处理，我们只用到0、1判断上传成功或者失败
						var data = xml.responseText;
						if (s.success)
							s.success(data, status);
						if (s.global)
							jQuery.event.trigger("ajaxSuccess", [xml, s]);
					} else
						jQuery.handleError(s, xml, status);
				} catch (e) {
					status = "error";
					jQuery.handleError(s, xml, status, e);
				}
				if (s.global)
					jQuery.event.trigger("ajaxComplete", [xml, s]);
				if (s.global && !--jQuery.active)
					jQuery.event.trigger("ajaxStop");
				if (s.complete)
					s.complete(xml, status);
				jQuery(io).unbind()
				setTimeout(function () {
					try {
						jQuery(io).remove();
						jQuery(form).remove();
					} catch (e) {
						jQuery.handleError(s, xml, null, e);
					}
				}, 100)
				xml = null
			}
		}
		if (s.timeout > 0) {
			setTimeout(function () {
				if (!requestDone)
					uploadCallback("timeout");
			}, s.timeout);
		}
		try {
			var form = jQuery('#' + formId);
			jQuery(form).attr('action', s.url);
			jQuery(form).attr('method', 'POST');
			jQuery(form).attr('target', frameId);
			if (form.encoding) {
				jQuery(form).attr('encoding', 'multipart/form-data');
			} else {
				jQuery(form).attr('enctype', 'multipart/form-data');
			}
			jQuery(form).submit();
		} catch (e) {
			jQuery.handleError(s, xml, null, e);
		}
		jQuery('#' + frameId).load(uploadCallback);
		return {
			abort : function () {}

		};
	},
	uploadHttpData : function (r, type) {
		var data = !type;
		data = type == "xml" || data ? r.responseXML : r.responseText;
		if (type == "script")
			jQuery.globalEval(data);
		if (type == "json")
			eval("data = \"" + data + "\"");
		if (type == "html")
			jQuery("<div>").html(data).evalScripts();
		return data;
	},
	handleError : function (s, xhr, status, e) {
		
		//If a local callback was specified, fire it
		if (s.error) {
			s.error.call(s.context || s, xhr, status, e);
		}
		
		//Fire the global callback
		if (s.global) {
			(s.context ? jQuery(s.context) : jQuery.event).trigger("ajaxError", [xhr, s, e]);
		}
	}
})
