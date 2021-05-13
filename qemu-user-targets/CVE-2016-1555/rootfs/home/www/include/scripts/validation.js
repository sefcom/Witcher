var layeredWin;
var inputForm;
Event.onDOMReady ( function() {
	//alert("DOM Ready!!!");
	//var tooltip = new globalTooltip();
	var tooltip = false;
	inputForm = new LiveValidationForm('dataForm');
	
	//inputFormNew = inputForm.getInstance('dataForm');
	var inputs = Form.getInputs(document.dataForm);
	inputs.each(function(input) {
	  	if (input.getAttribute("validate") && input.getAttribute("validate") != "") {
			if (/onlyOnSubmit/.test(input.getAttribute("validate"))) {
				var valid = new LiveValidation(input.id, { onlyOnBlur: true, onlyOnSubmit: true });
			}
			else {
				var valid = new LiveValidation(input.id, { onlyOnBlur: true });
			}
      		if (/^/.test(input.getAttribute("validate"))) {
				input.getAttribute("validate").split('^').each( function(validator) {
		      		eval("valid.add( Validate."+validator+");");
				});
			}
			else {
				eval("valid.add( Validate."+input.getAttribute("validate")+");");
			}
			inputForm.addField(valid);
	  	}
		//inputForm.addField(valid);
    });
	//return inputForm;
	if ($('layeredWindow') != undefined) {
		layeredWin = new ModalWindow($('layeredWindow'), {    
			contents: function() { return $('layeredWindow').innerHTML; },
			overlayCloseOnClick: false,
			overlayClassName: 'LayeredWin_overlay',
			containerClassName: 'LayeredWin_container',
			opacity: 0.8,
			iframe: true
		});
	}
	progressBar.close();
	
/*var tooltipObj = new formTooltip();
tooltipObj.setTooltipPosition('below');
tooltipObj.initFormFieldTooltip();*/    
});

