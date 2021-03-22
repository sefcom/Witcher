/*!
 * reasy-ui.js v1.0.3 2014-06-03
 * Copyright 2014 ET.W
 * Licensed under Apache License v2.0
 *
 * The REasy UI for router, and themes built on top of the HTML5 and CSS3..
 */

if ("undefined" === typeof jQuery && "undefined" === typeof REasy) {
	throw new Error("REasy-UI requires jQuery or REasy");
}

(function (win, doc) {
	"use strict";

	var rnative = /^[^{]+\{\s*\[native code/,
		_ = window._;

	// ReasyUI 全局变量对象
	$.reasyui = {};

	// 记录已加载的 REasy模块
	$.reasyui.mod = 'core ';

	// ReasyUI 多语言翻译对象对象
	$.reasyui.b28n = {};

	// 全局翻译函数
	if (!_) {
		window._ = _ = function (str, replacements) {
			var ret = $.reasyui.b28n[str] || str,
				len = replacements && replacements.length,
				count = 0,
				index;

			if (len > 0) {
				while ((index = ret.indexOf('%s')) !== -1) {
					ret = ret.substring(0, index) + replacements[count] +
						ret.substring(index + 2, ret.length);
					count = ((count + 1) === len) ? count : (count + 1);
				}
			}

			return ret;
		}
	}

	// HANDLE: When $ is jQuery extend include function 
	if (!$.include) {
		$.include = function (obj) {
			$.extend($.fn, obj);
		};
	}

	$.extend({
		keyCode: {
			ALT: 18,
			BACKSPACE: 8,
			CAPS_LOCK: 20,
			COMMA: 188,
			COMMAND: 91,
			COMMAND_LEFT: 91, // COMMAND
			COMMAND_RIGHT: 93,
			CONTROL: 17,
			DELETE: 46,
			DOWN: 40,
			END: 35,
			ENTER: 13,
			ESCAPE: 27,
			HOME: 36,
			INSERT: 45,
			LEFT: 37,
			MENU: 93, // COMMAND_RIGHT
			NUMPAD_ADD: 107,
			NUMPAD_DECIMAL: 110,
			NUMPAD_DIVIDE: 111,
			NUMPAD_ENTER: 108,
			NUMPAD_MULTIPLY: 106,
			NUMPAD_SUBTRACT: 109,
			PAGE_DOWN: 34,
			PAGE_UP: 33,
			PERIOD: 190,
			RIGHT: 39,
			SHIFT: 16,
			SPACE: 32,
			TAB: 9,
			UP: 38,
			WINDOWS: 91 // COMMAND
		},

		//获取视口宽度，不包含滚动条
		viewportWidth: function () {
			var de = doc.documentElement;

			return (de && de.clientWidth) || doc.body.clientWidth ||
				win.innerWidth;
		},

		//获取视口高度，不包含滚动条
		viewportHeight: function () {
			var de = doc.documentElement;

			return (de && de.clientHeight) || doc.body.clientHeight ||
				win.innerHeight;
		},

		//获取输入框中光标位置，ctrl为你要获取的输入框
		getCursorPos: function (ctrl) {
			var Sel,
				CaretPos = 0;
			//IE Support
			if (doc.selection) {
				ctrl.focus();

				Sel = doc.selection.createRange();
				Sel.moveStart('character', -ctrl.value.length);
				CaretPos = Sel.text.length;
			} else if (ctrl.selectionStart || parseInt(ctrl.selectionStart, 10) === 0) {
				CaretPos = ctrl.selectionStart;
			}
			return CaretPos;
		},

		//设置文本框中光标位置，ctrl为你要设置的输入框，pos为位置
		setCursorPos: function (ctrl, pos) {
			var range;

			if (ctrl.setSelectionRange) {
				ctrl.focus();
				ctrl.setSelectionRange(pos, pos);
			} else if (ctrl.createTextRange) {
				range = ctrl.createTextRange();
				range.collapse(true);
				range.moveEnd('character', pos);
				range.moveStart('character', pos);
				range.select();
			}

			return ctrl;
		},

		getUtf8Length: function (str) {
			var totalLength = 0,
				charCode,
				len = str.length,
				i;

			for (i = 0; i < len; i++) {
				charCode = str.charCodeAt(i);
				if (charCode < 0x007f) {
					totalLength++;
				} else if ((0x0080 <= charCode) && (charCode <= 0x07ff)) {
					totalLength += 2;
				} else if ((0x0800 <= charCode) && (charCode <= 0xffff)) {
					totalLength += 3;
				} else {
					totalLength += 4;
				}
			}
			return totalLength;
		},

		/**
		 * For feature detection
		 * @param {Function} fn The function to test for native support
		 */
		isNative: function (fn) {
			return rnative.test(String(fn));
		},

		isHidden: function (elem) {
			if (!elem) {
				return;
			}

			return $.css(elem, "display") === "none" ||
				$.css(elem, "visibility") === "hidden" ||
				(elem.offsetHeight == 0 && elem.offsetWidth == 0);
		},

		getValue: function (elem) {
			if (typeof elem.value !== "undefined") {
				return elem.value;
			} else if ($.isFunction(elem.val)) {
				return elem.val();
			}
		}
	});

	/* Cookie */
	$.cookie = {
		get: function (name) {
			var cookieName = encodeURIComponent(name) + "=",
				cookieStart = doc.cookie.indexOf(cookieName),
				cookieEnd = doc.cookie.indexOf(';', cookieStart),
				cookieValue = null;

			if (cookieStart > -1) {
				if (cookieEnd === -1) {
					cookieEnd = doc.cookie.length;
				}
				cookieValue = decodeURIComponent(doc.cookie.substring(cookieStart +
					cookieName.length, cookieEnd));
			}
			return cookieValue;
		},
		set: function (name, value, path, domain, expires, secure) {
			var cookieText = encodeURIComponent(name) + "=" +
				encodeURIComponent(value);

			if (expires instanceof Date) {
				cookieText += "; expires =" + expires.toGMTString();
			}
			if (path) {
				cookieText += "; path =" + path;
			}
			if (domain) {
				cookieText += "; domain =" + domain;
			}
			if (secure) {
				cookieText += "; secure =" + secure;
			}
			doc.cookie = cookieText;

		},
		unset: function (name, path, domain, secure) {
			this.set(name, '', path, domain, new Date(0), secure);
		}
	};

}(window, document));

/*!
 * REasy UI Dialog @VERSION
 * http://reasyui.com
 *
 * Copyright 2013 reasy Foundation and other contributors
 *
 * Depends:
 *	reasy-ui-core.js
 */

(function (doc) {
	"use strict";
	/* Dialog */
	$.dialog = (function () {
		var defaults = {
			show: true,
			showNoprompt: false,
			model: 'dialog',
			title: '来自网页的消息',
			content: ''
		};

		function createDialogHtml(options) {
			var model = options.model,
				ret,
				nopromptClass;

			if (model === 'dialog') {
				nopromptClass = options.showNoprompt ? "dialog-nocheck" :
					"dialog-nocheck none";

				ret = '<h2 class="dialog-title">' +
					'<span id="dialog-title">' + options.title + '</span>' +
					'<button type="button" class="close btn" id="dialog-close">&times;</button>' +
					'</h2>' +
					'<div class="dialog-content">' + options.content + '</div>' +
					'<div class="' + nopromptClass + '">' +
					'<label class="checkbox" for="nocheck">' +
					'<input type="checkbox" id="dialog-noprompt" />不再提示' +
					'</label>' +
					'</div>' +
					'<div class="dialog-btn-group">' +
					'<button type="button" class="btn" id="dialog-apply">确定</button>' +
					'<button type="button" class="btn" id="dialog-cancel">取消</button>' +
					'</div>';
			} else if (model === 'message') {
				ret = '<h2 class="dialog-title">' +
					'<span id="dialog-title">' + options.title + '</span>' +
					'</h2>' +
					'<div class="dialog-content dialog-content-massage">' + options.content + '</div>';
			}

			return ret;
		}

		function Dialog(options) {
			this.element = null;
			this.id = 'r-dialog';
			this.overlay = null;
			this.noprompt = 'false';

			if ($.type(options) === 'object') {
				this.options = $.extend(defaults, options);
			} else {
				this.options = $.extend(defaults, {
					content: options
				});
			}
		}

		Dialog.prototype = {
			init: function () {
				var $overlay = $('#overlay'),
					overlayElem = $overlay[0],
					$dialog = $('#r-dialog'),
					dialogElem = $dialog[0],
					bodyElem = $('body')[0],
					thisDialog = this,
					dialogLeft,
					modelHtml;

				modelHtml = createDialogHtml(this.options);

				if (!overlayElem) {
					overlayElem = doc.createElement('div');
					overlayElem.id = 'overlay';
					overlayElem.className = 'overlay';
					bodyElem.appendChild(overlayElem);
				}
				if (!dialogElem) {
					dialogElem = doc.createElement('div');
					dialogElem.id = 'r-dialog';
					dialogElem.className = 'dialog';
					bodyElem.appendChild(dialogElem);

					$dialog = $('#r-dialog');
					dialogElem = $dialog[0];
					$dialog.html(modelHtml);
				}

				// 计算居中需设计左边距为多少
				dialogLeft = ($.viewportWidth() - dialogElem.offsetWidth) /
					(2 * $.viewportWidth()) * 100;
				dialogLeft = dialogLeft > 0 ? dialogLeft : 0;

				$dialog.css('left', dialogLeft + '%');
				this.element = dialogElem;
				this.overlay = overlayElem;

				$dialog.on('click', function (e) {
					var curElem = e.target || e.srcElement,
						curId = curElem.id,
						funName = curId.split('-')[1];

					if ($('#dialog-noprompt')[0] && $('#dialog-noprompt')[0].checked) {
						thisDialog.noprompt = 'true';
					} else {
						thisDialog.noprompt = 'flase';
					}
					if (funName && thisDialog[funName]) {
						thisDialog[funName]();
					}
				});

				if (this.options.show) {
					this.open();
				}
			},

			close: function () {
				$(this.element).hide();
				$(this.overlay).hide();
			},

			open: function () {
				var nopromptElem = $('#dialog-noprompt')[0];

				$(this.element).show();
				$(this.overlay).show();
				if (nopromptElem) {
					nopromptElem.checked = false;
				}

			},

			apply: function () {
				if ($.type(this.options.apply) === 'function') {
					this.options.apply.apply(this, arguments);
				}
				this.close();
			},

			cancel: function () {
				if ($.type(this.options.cancel) === 'function') {
					this.options.cancel.apply(this, arguments);
				}
				this.close();
			}
		};

		return function (options) {
			var dialog = new Dialog(options);

			dialog.init();
			return dialog;
		};
	}());
})(document);

/*!
 * REasy UI Textboxs @VERSION
 * http://reasyui.com
 *
 * Copyright 2013 reasy Foundation and other contributors
 *
 * Depends:
 *	reasy-ui-core.js
 */

(function () {
	"use strict";
	var Textboxs = {
		// type类型现在支持的有：“ip”，“ip-mini”，“mac”
		create: function (elem, type, defVal) {

			if (elem.toTextboxsed) {
				return elem;
			}

			var $elem = $(elem),
				len = 4,
				maxlength = 3,
				divide = '.',
				replaceRE = /[^0-9]/g,
				textboxs = [],
				htmlArr = [],
				classStr,
				i;

			defVal = defVal || '';
			type = type || 'ip';
			classStr = type === 'ip-mini' ? 'text input-mic-mini' : 'text input-mini-medium';
			elem.textboxsType = type;
			elem.defVal = defVal;

			if (type === 'mac') {
				len = 6;
				maxlength = 2;
				divide = ':';
				replaceRE = /[^0-9a-fA-F]/g;
				classStr = 'text input-mic-mini';
			}

			if ($.trim(elem.innerHTML) === '') {
				for (i = 0; i < len; i++) {
					if (i !== 0) {
						htmlArr[i] = '<input type="text" class="' + classStr + '"' +
							' maxlength="' + maxlength + '">';
					} else {
						htmlArr[i] = '<input type="text" class="' + classStr + ' first"' +
							' maxlength="' + maxlength + '">';
					}

				}
				elem.innerHTML = htmlArr.join(divide);
			}

			textboxs = elem.getElementsByTagName('input');
			len = textboxs.length;

			for (i = 0; i < len; i++) {
				textboxs[i].index = i;
			}

			$(textboxs).on('focus', function () {
				var val = Textboxs.getValue(this.parentNode);

				if (val === '') {
					Textboxs.setValue(elem, defVal, true);

					// 如果是按回退而聚集的，光标定位到最后
				} else if (this.back === "back") {
					$.setCursorPos(this, this.value.length);
					this.back = "";
				}

			}).on('blur', function () {
				if (this.value > 255) {
					this.value = '255';
				}

			});

			$elem.on('keydown', function (e) {
				var elem = e.target || e.srcElement;

				elem.pos1 = +$.getCursorPos(elem);
				this.curIndex = elem.index;
				if (elem.value.length <= 0) {
					elem.emptyInput = true;
				} else {
					elem.emptyInput = false;
				}

			}).on('keyup', function (e) {
				var elem = e.target || e.srcElement,
					myKeyCode = e.keyCode || e.which,
					skipNext = false,
					skipPrev = false,
					pos = +$.getCursorPos(elem),
					val = elem.value,
					replacedVal = val.replace(replaceRE, ''),
					ipReplacedVal = parseInt(replacedVal, 10).toString(),
					isIp = type.indexOf('ip') !== -1;

				// HACK: 由于把事件添加在input元素的父元素上，IE下按“Tab”键而跳转，
				// “keydown” 与 “keyup” 事件会在不同 “input”元素中触发。
				if (this.curIndex !== elem.index) {
					return false;
				}

				//处理与向前向后相关的特殊按键
				switch (myKeyCode) {
				case $.keyCode.LEFT: //如果是左键
					skipPrev = (pos - elem.pos1) === 0;
					if (skipPrev && pos === 0 && elem.index > 0) {
						textboxs[elem.index - 1].focus();
					}
					return true;

				case $.keyCode.RIGHT: //如果是右键
					if (pos === val.length && elem.index < (len - 1)) {
						textboxs[elem.index + 1].focus();
						$.setCursorPos(textboxs[elem.index + 1], 0);
					}
					return true;

				case $.keyCode.BACKSPACE: //如果是回退键
					if (elem.emptyInput && elem.value === "" && elem.index > 0) {
						textboxs[elem.index - 1].focus();
						textboxs[elem.index - 1].back = "back";
					}
					return true;

					//没有 default
				}

				//如果有禁止输入的字符，去掉禁用字符
				if (val !== replacedVal) {
					elem.value = replacedVal;
				}

				//修正ip地址中类似‘012’为‘12’
				if (isIp && !isNaN(ipReplacedVal) &&
					ipReplacedVal !== val) {

					elem.value = ipReplacedVal;
				}

				//如果value不为空或不是最后一个文本框
				if (elem.index !== (len - 1) && elem.value.length > 0) {

					//达到最大长度，且光标在最后
					if (elem.value.length === maxlength && pos === maxlength) {
						skipNext = true;

						//如果是IP地址，如果输入小键盘“.”或英文字符‘.’则跳转到下一个输入框
					} else if (isIp && (myKeyCode === $.keyCode.NUMPAD_DECIMAL ||
							myKeyCode === $.keyCode.PERIOD)) {

						skipNext = true;
					}
				}

				//跳转到下一个文本框,并全选
				if (skipNext) {
					textboxs[elem.index + 1].focus();
					textboxs[elem.index + 1].select();
				}
			});

			elem.toTextboxsed = true;
			return elem;
		},

		setValue: function (elem, val, setDefault) {
			var textboxs = elem.getElementsByTagName('input'),
				len = textboxs.length,
				textboxsValues,
				i;

			if (val !== '' && $.type(val) !== 'undefined') {
				textboxsValues = val.split('.');

				if (elem.textboxsType === 'mac') {
					textboxsValues = val.split(':');
				}
			} else {
				textboxsValues = ['', '', '', '', '', ''];
			}

			for (i = 0; i < len; i++) {
				textboxs[i].value = textboxsValues[i];
			}

			// TODO: IE下聚焦隐藏的元素会报错
			try {
				if (elem.defVal && setDefault) {
					textboxs[i - 1].focus();
					$.setCursorPos(textboxs[i - 1], textboxs[i - 1].value.length);
				}
			} catch (e) {}

			return elem;
		},

		getValues: function (elem) {
			var valArr = [],
				textboxs,
				len,
				i;

			textboxs = elem.getElementsByTagName('input');
			len = textboxs.length;
			for (i = 0; i < len; i++) {
				valArr[i] = textboxs[i].value;
			}

			return valArr;
		},

		getValue: function (elem) {
			var valArr = Textboxs.getValues(elem),
				divide = '.',
				emptyReg = /^[.:]{0,}$/,
				ret;

			if (elem.textboxsType === 'mac') {
				divide = ':';
			}
			ret = valArr.join(divide).toUpperCase();

			return emptyReg.test(ret) ? '' : ret;
		},

		disable: function (elem, disabled) {
			var textboxs = $('input.text', elem),
				len = textboxs.length,
				i;

			for (i = 0; i < len; i++) {
				textboxs[i].disabled = disabled;
			}

			return elem;
		}
	};

	$.fn.toTextboxs = function (type, delVal) {
		return this.each(function () {
			Textboxs.create(this, type, delVal);
			$(this).addClass('textboxs');

			this.val = function (val) {
				if ($.type(val) !== 'undefined') {
					if (typeof val !== 'string') {
						return false;
					}
					Textboxs.setValue(this, val);
				} else {
					return Textboxs.getValue(this);
				}

			};

			this.disable = function (disabled) {
				Textboxs.disable(this, disabled);
			};
			this.toFocus = function () {
				this.getElementsByTagName('input')[0].focus();
			};
		});
	};

})();


/*!
 * REasy UI Inputs @VERSION
 * http://reasyui.com
 *
 * Copyright 2013 reasy Foundation and other contributors
 *
 * Depends:
 *	reasy-ui-core.js
 */

(function (win, doc) {
	"use strict";
	var Inputs = {
		addCapTip: function (newField, pasElem, func) {
			var $newField = $(newField);

			function hasCapital(value, pos) {
				var pattern = /[A-Z]/g,
					myPos = pos || value.length;

				return pattern.test(value.charAt(myPos - 1));
			}

			//add capital tip 
			$newField.on("keyup", function (e) {
				var msgId = this.id + "-caps",
					myKeyCode = e.keyCode || e.which,
					$message,
					massageElm,
					repeat,
					pos;

				// HANDLE: Not input letter
				if (myKeyCode < 65 || myKeyCode > 90) {
					return true;
				}

				if (!this.capDetected && !func) {

					massageElm = doc.createElement('span');
					massageElm.className = "help-inline text-info";
					massageElm.id = msgId;
					massageElm.innerHTML = '您输的是大写字母！';
					if (pasElem) {
						this.parentNode.insertBefore(massageElm, pasElem.nextSibling);
					} else {
						this.parentNode.insertBefore(massageElm, newField.nextSibling);
					}

					this.capDetected = true;
				}
				pos = $.getCursorPos(this);
				if (!func) {

					$message = $('#' + msgId);



					if (hasCapital(this.value, pos)) {
						$message.show();
						repeat = "$('#" + msgId + "').hide()";
						win.setTimeout(repeat, 1000);
					} else {
						$message.hide();
					}
				} else {
					if (hasCapital(this.value, pos)) {
						func();
					}
				}
			});
		},

		supChangeType: 'no',

		// 检测是否支持input元素 ‘type’ 属性的修改，IE下修改会报错
		isSupChangeType: function (passwordElem) {
			try {
				passwordElem.setAttribute("type", "text");
				if (passwordElem.type === 'text') {
					passwordElem.setAttribute("type", "password");
					return true;
				}
			} catch (d) {
				return false;
			}

		},

		// For IE6 not suppost change input type
		createTextInput: function (elem) {
			var $elem = $(elem),
				newField = doc.createElement('input'),
				$newField;

			newField.setAttribute("type", "text");
			newField.setAttribute("maxLength", elem.getAttribute("maxLength"));
			newField.setAttribute("id", elem.id + "_");
			newField.className = elem.className;
			newField.setAttribute("placeholder", elem.getAttribute("placeholder") || "");
			if (elem.getAttribute('data-options')) {
				newField.setAttribute("data-options", elem.getAttribute('data-options'));
			}

			if (elem.getAttribute('required')) {
				newField.setAttribute("required", elem.getAttribute('required'));
			}
			elem.parentNode.insertBefore(newField, elem);
			$newField = $(newField);

			$elem.on("focus", function () {
				var thisVal = elem.value;

				if (thisVal !== "") {
					newField.value = thisVal;
				}
				$(this).hide();
				$newField.show();
				$newField.focus();
				$.setCursorPos(newField, thisVal.length);
			});

			$newField.on("blur", function () {
					var $this = $(this);

					if (this.value !== "") {
						elem.value = this.value;


						if (!$this.hasClass("validatebox-invalid")) {
							$(this).hide();
							$elem.show();
						}

					} else {
						elem.value = "";
					}
				})
				.on("keyup", function () {
					if (newField.value !== "") {
						elem.value = newField.value;

					} else {
						elem.value = "";

					}
				});

			if (elem.value !== "") {
				$newField.hide();
				newField.value = elem.value;
			} else {
				$elem.hide();
				$newField.show();
			}

			return newField;
		},

		toTextType: function (elem) {
			var $elem = $(elem),
				newField;

			// HANDLE: 只有在第一次进来检测是否可直接修改 ‘type’属性
			if (Inputs.supChangeType === 'no') {
				Inputs.supChangeType = Inputs.isSupChangeType(elem);
			}

			// HANDLE: 可直接修改 ‘type’属性
			if (Inputs.supChangeType) {
				newField = elem;
				$elem.on("focus", function () {
						this.type = 'text';
					})
					.on("blur", function () {
						if (this.value !== "") {
							this.type = 'password';
						}
					});
				/*.on("keyup", function(){
								this.type = 'password';	
							});*/

				if ($elem.value === "") {
					$elem.type = 'text';
				}

				// HANDLE: 不支持‘type’属性修改，创建一个隐藏的文本框来实现
			} else {
				newField = Inputs.createTextInput(elem);
			}

			elem.textChanged = true;
			return newField;
		},

		addPlaceholder: function (elem, placeholderText) {
			var text = elem.getAttribute('placeholder'),
				$text = $(elem),
				placehodereElem;

			if (text !== placeholderText) {
				elem.setAttribute("placeholder", placeholderText);
			}

			function isPlaceholderVal(elem) {
				return (elem.value === elem.getAttribute("placeholder"));
			}

			function supportPlaceholder() {
				var i = doc.createElement('input');
				return 'placeholder' in i;
			}

			function createPlaceholderElem(elem) {
				var ret = doc.createElement('span');

				ret.className = "placeholder-content";
				ret.innerHTML = '<span class="placeholder-text" style="width:' +
					(elem.offsetWidth || 210) + 'px;line-height:' +
					(elem.offsetHeight || 28) + 'px">' +
					(placeholderText || "") + '</span>';

				elem.parentNode.insertBefore(ret, elem);

				$(ret).on('click', function () {
					elem.focus();
				});

				return ret;
			}

			function showPlaceholder(node) {

				if (typeof placehodereElem == "undefined") {
					placehodereElem = $(node).parent().find(".placeholder-content")[0];
				}

				if (node.value === "") {
					if (node.placeholdered !== 'ok') {
						placehodereElem = createPlaceholderElem(elem);
						node.placeholdered = "ok";
					}
					$(placehodereElem).show();
				} else {

					$(placehodereElem).hide();
				}
			}

			// HANDLE: Not support placehoder 
			if (!supportPlaceholder()) {
				$text.on("click", function () {
					showPlaceholder(this);
				}).on("keyup", function () {
					showPlaceholder(this);
				}).on("focus", function () {
					showPlaceholder(this);
				});

				showPlaceholder(elem);

				// HANDLE: Support placehoder, But not change placeholder text color
			} else {
				$text.on("blur", function () {
						if (isPlaceholderVal(this)) {
							this.value = "";
						}
						if (this.value === "") {
							$(this).addClass("placeholder-text");
						}
					})
					.on("keyup", function () {
						if (this.value !== "") {
							$(this).removeClass("placeholder-text");
						}
					});

				if (elem.value === "") {
					$text.addClass("placeholder-text");
				} else {
					$text.removeClass("placeholder-text");
				}
			}
		},

		initInput: function (elem, placeholderText, capTip, func) {
			var $text,
				textElem;

			if (elem !== null) {
				textElem = elem;
			} else {
				return 0;
			}

			//HANDLE: Input password, If add capital detect 
			if (elem.type === "password" && !elem.textChanged) {
				textElem = Inputs.toTextType(elem, capTip);
				if (capTip) {
					Inputs.addCapTip(textElem, elem, func);
				}

				//HANDLE: Input text, If add capital detect 
			} else if (elem.type === "text" && capTip) {
				Inputs.addCapTip(textElem);
			}
			$text = $(textElem);

			if (placeholderText) {
				Inputs.addPlaceholder(textElem, placeholderText);
			} else if (elem.getAttribute("placeholder")) {
				placeholderText = elem.getAttribute("placeholder");
				Inputs.addPlaceholder(textElem, placeholderText);
			}
			textElem.value = elem.value;
			return textElem;
		},
		//func表示需要特殊处理大小写的函数
		initPassword: function (elem, placeholderText, capTip, hide, func) {
			var inputVal = elem.value,
				$elem = $(elem);

			if (inputVal === "") {
				Inputs.initInput(elem, placeholderText, capTip, func);
			} else {
				if (hide) {
					$elem.on("keyup", function () {
						if (this.value === "") {
							Inputs.initInput(elem, placeholderText, capTip, func);
						}
					});
				} else {
					Inputs.initInput(elem, placeholderText, capTip, func);
				}
			}
		}
	};

	$.include({
		addPlaceholder: function (text) {
			return this.each(function () {
				Inputs.addPlaceholder(this, text);
			});
		},

		initPassword: function (text, capTip, hide, func) {
			return this.each(function () {
				Inputs.initPassword(this, text, capTip, hide, func);
			});
		},

		initInput: function (text, capTip, func) {
			return this.each(function () {
				Inputs.initInput(this, text, capTip, func);
			});
		},

		addCapTip: function (newField, pasElem, func) {
			return this.each(function () {
				Inputs.addCapTip(newField, pasElem, func);
			});
		},

		toTextType: function () {
			return this.each(function () {
				Inputs.toTextType(this);
			});
		}
	});
})(window, document);

/*!
 * REasy UI Massage @VERSION
 * http://reasyui.com
 *
 * Copyright 2013 reasy Foundation and other contributors
 *
 * Depends:
 *	reasy-ui-core.js
 */

(function (doc) {
	"use strict";
	$.ajaxMassage = (function () {
		function AjaxMsg() {
			this.$elem = null;
		}

		AjaxMsg.prototype = {
			constructor: AjaxMsg,

			init: function (msg) {
				var msgElem = document.getElementById('ajax-massage');

				if (msgElem) {
					msgElem.style.display = "block";
				} else {
					msgElem = document.createElement("div");
					msgElem.id = 'ajax-massage';
					$("body").append(msgElem);
				}

				msgElem.className += ' massage-ajax';
				msgElem.innerHTML = msg;
				this.$elem = $(msgElem);
			},

			show: function () {
				this.$elem.show();
			},

			hide: function () {
				this.$elem.hide();
			},

			remove: function () {
				this.$elem.remove();
			},

			text: function (msg) {
				this.$elem.html(msg);
			}
		};

		return function (msg) {
			var ajaxMsg = new AjaxMsg();
			ajaxMsg.init(msg);

			return ajaxMsg;
		};
	})();
})(document);

/*!
 * REasy UI Select @VERSION
 * http://reasyui.com
 *
 * Copyright 2013 reasy Foundation and other contributors
 *
 * Depends:
 *	reasy-ui-core.js
 */

(function (document) {
	"use strict";
	var Inputselect = {
		initSelected: false,
		count: 0,
		defaults: {
			"toggleEable": true,
			"editable": true,
			"size": "",
			"seeAsTrans": false,
			"unitStr": "",
			"options": [{
				"nothingthere": "nothingthere"
			}]
		},
		create: function (elem, obj) {
			var liContent = '',
				inputAble = '',
				toggleAble = '',
				inputClass,
				inputBoxStr,
				dropBtnStr,
				ulStr,
				inputSelStr,
				aVal,
				liVal,
				id,
				i,
				len,
				options,
				root,
				inputBox,
				firstOpt,
				validateClass;

			obj = $.extend(Inputselect.defaults, obj);
			validateClass = $(elem).hasClass("validatebox") ? "validatebox" : "";

			if (obj.size == 'small') {
				inputClass = 'span1';
			} else if (obj.size === 'large') {
				inputClass = 'span3';
			} else {
				inputClass = 'span2';
			}
			len = obj.options.length;

			for (i = 0; i < len; i++) {
				if ((Inputselect.count === i) && obj.options[i]) {
					if (len > 1) {
						if (Inputselect.count < len - 1) {
							Inputselect.count++;
						} else {
							Inputselect.count = 0;
						}
					}
					options = obj.options[i];
					for (id in options) {
						if (options.hasOwnProperty(id)) {
							if (options[id] === '.divider' && id === '.divider') {
								liContent += '<li class="divider"></li>';
							} else {
								if (!firstOpt) {
									firstOpt = id;
								}
								liContent += '<li data-val="' + id + '"><a>' + (options[id] || id) + '</a></li>';
							}
						}
					}
					//break;
				}
			}
			/*初始值转换成数组对象，第一个值为显示给用户的，第二个值为传给后台的*/
			if (!obj.initVal && obj.initVal !== "") { //未定义初始值（不是设为空）
				obj.initVal = firstOpt;
			}
			if (typeof obj.initVal === 'string') {
				obj.initVal = [obj.initVal, obj.initVal];
			}
			/*end of initVal handling*/
			if (obj.editable == 0) { //为了兼容0和false，请勿改成 '==='
				inputAble = "disabled";
			} else {
				inputAble = "";
			}
			if (obj.toggleEable == 0) {
				toggleAble = "disabled";
			} else {
				toggleAble = "";
			}
			inputBoxStr = '<input class="input-box ' + inputClass + '" type="text" ' + inputAble + ' value="' + obj.initVal[0] + '">' +
				'<input type="hidden" value="' + obj.initVal[1] + '">';
			dropBtnStr = '<div class="btn-group"><button type="button"' + toggleAble + ' class=" toggle btn btn-small"><span class="caret"></span></button></div>';
			ulStr = '<div class="input-select"><ul class="dropdown-menu">' + liContent + '</ul></div>';

			inputSelStr = inputBoxStr + dropBtnStr + ulStr;
			elem.innerHTML = inputSelStr;
			$(elem).addClass('input-append');

			var root = elem.getElementsByTagName('ul')[0],
				inputBox = elem.getElementsByTagName('input')[0],
				inputBoxHide = elem.getElementsByTagName('input')[1];

			$(root).on('mouseover', function (e) {
				var target = e.target || e.srcElement;

				if (target.tagName.toLowerCase() !== "a") {
					return;
				}
				liVal = target.parentElement.getAttribute('data-val'); //隐藏的值
				aVal = target.innerText || target.textContent; //用户看到的值

			}).on('click', function (e) {
				var target = e.target || e.srcElement;

				if (target.tagName.toLowerCase() !== "a") {
					return;
				}
				//手动设定必须要传".hand-set":"***"
				if ($.trim(liVal) == ".hand-set") {
					//inputBox.select();
					inputBox.value = "";
					inputBox.focus();
					return;
				}
				if (obj.seeAsTrans == false) {
					inputBox.value = liVal;
					inputBoxHide.value = liVal;
				} else {
					inputBox.value = aVal;
					inputBoxHide.value = liVal;
				}
				if (validateClass != "")
					$.validate().check(elem);
			});

			$(inputBox).on('click', function () {
				inputBox.select();
			}).on('focus', function () {}).on('blur', function () {
				if (validateClass != "")
					$.validate().check(elem);
			}).on('keyup', function () {
				inputBoxHide.value = inputBox.value;
				if (validateClass != "")
					$.validate().check(elem);
			});

			if (!Inputselect.initSelected) {
				Inputselect.initSelected = true;
				$(document).on('click', function (e) {
					var target = e.target || e.srcElement,
						hasToggle,
						ulList,
						targetDis;

					if ($(target.parentNode).hasClass('toggle')) {
						target = target.parentNode;
					}
					hasToggle = $(target).hasClass('toggle');
					if (hasToggle) {
						ulList = target.parentNode.parentNode.getElementsByTagName('ul')[0];
						targetDis = ulList.style.display;
					}
					$('.toggle').each(function () {
						this.parentNode.parentNode.getElementsByTagName('ul')[0].style.display = 'none';
					});

					if (hasToggle) {
						ulList.style.display = (targetDis === 'none' ||
							$.trim(targetDis) === '') ? 'block' : 'none';
					}
				});
			}
			return elem;
		},

		setValue: function (elem, val) {
			var inputBox = elem.getElementsByTagName('input')[0],
				inputBoxHide = elem.getElementsByTagName('input')[1];
			if (typeof val === 'string') {
				inputBox.value = val;
				inputBoxHide.value = val;
			} else if (typeof val === 'object') {
				inputBoxHide.value = val[0];
				inputBox.value = val[1];
			}
			return elem;
		},

		getValue: function (elem) {
			var inputBoxHide = elem.getElementsByTagName('input')[1];
			return inputBoxHide.value;
		},

		append: function (elem, options) {
			var ulList = elem.getElementsByTagName('ul')[0],
				ulHtml = ulList.innerHTML,
				id,
				liContent = '';

			for (id in options) {
				if (options.hasOwnProperty(id)) {
					if (options[id] === '.divider') {
						liContent += '<li class="divider"></li>';
					} else {
						liContent += '<li data-val="' + id + '"><a>' +
							(options[id] || id) + '</a></li>';
					}
				}
			}
			ulHtml += liContent;
			ulList.innerHTML = ulHtml;
		},

		remove: function (elem, idx) {
			var opts = elem.getElementsByTagName('li'),
				rmOpt;

			if (idx < opts.length) {
				rmOpt = opts[idx];
				rmOpt.parentNode.removeChild(rmOpt);
			} else {
				return "out of range!";
			}
		}
	};

	$.fn.toSelect = function (obj) {
		Inputselect.count = 0;
		return this.each(function () {
			Inputselect.create(this, obj);

			this.val = function (val) {
				if ($.type(val) !== 'undefined') {
					if (typeof val !== 'string' && typeof val !== 'object') {
						return false;
					}
					Inputselect.setValue(this, val);
				} else {
					return Inputselect.getValue(this);
				}

				return this;
			};

			this.appendLi = function (options) {
				if ($.type(options) === 'object') {
					Inputselect.append(this, options);
				}

				return this;
			};

			this.removeLi = function (idx) {
				idx = parseInt(idx, 10);
				if ($.type(idx) === 'number') {
					Inputselect.remove(this, idx);
				}
				return this;
			};
		});
	};
})(document);

/*
 * REasy UI Validate @VERSION
 * http://reasyui.com
 *
 * Copyright 2013 reasy Foundation and other contributors
 *
 * Depends:
 *	reasy-ui-core.js
 */

(function (window, document) {
	"use strict";
	var utils = {
			errorNum: 0,

			getOptions: function (elem) {
				var options = elem.getAttribute("data-options");

				return $.parseJSON(options);
			},

			getVal: function (elem) {
				var ret = elem.value;

				if (!ret && $.isFunction(elem.val)) {
					ret = elem.val();
				}

				return ret || '';
			},

			isEmpty: function () {
				var val = utils.getVal(this);

				return val === "" || val === this.getAttribute('placeholder');
			},

			check: function (eventType) {
				var $this = $(this),
					thisVal = utils.getVal(this),
					data = utils.getOptions(this) || null,
					valid = $.validate.valid,
					str = "",
					isEmpty,
					args,
					msg = "",
					validType;

				args = [thisVal];

				isEmpty = thisVal === "" || thisVal === this.getAttribute('placeholder');

				if ((this.getAttribute('required') === 'required' || this.required) && isEmpty) {
					if (eventType !== 'keyup' && eventType !== 'focus') {
						str = _("This field cannot be blank.");
					}

				} else if (thisVal && data !== null) {
					args = args.concat(data.args || []);
					validType = valid[data.type];
					msg = data.msg;
					// 如果validType为函数，说明错误都很明确
					if (typeof validType === "function") {
						str = validType.apply(valid, args);

						// 错误类型需要分类处理
					} else {

						//如果是keyup或focus事件
						if (eventType === 'keyup' || eventType === 'focus') {

							// 只验证明确的错误，提示修改方案
							if (validType && typeof validType.specific === 'function') {
								str = validType.specific.apply(validType, args);
							}

							//其他类型事件
						} else {

							// 完整性验证，不明确的错误，无法给出修改方案
							if (validType && typeof validType.all === 'function') {
								str = validType.all.apply(validType, args);
							}
						}

					}

				}

				/*if (!this['data-check-error']) {
					utils.errorNum++;
				}*/
				// change by zzc another change of errorNum in the function 'checkAll'
				utils.errorNum++;
				$this.removeValidateTip(true);

				if (str && !$.isHidden(this)) {
					if (msg) {
						str = msg;
					}
					$this.addValidateTip(str, true)
						.showValidateTip()
						.addClass("validatebox-invalid");

					this['data-check-error'] = true;
				} else {
					$this.removeClass("validatebox-invalid");
					utils.errorNum--;

					this['data-check-error'] = false;
				}

				return isEmpty;

			},
			show: function () {
				$(this).showValidateTip();
			},

			hide: function () {
				$(this).hideValidateTip();
			}
		},
		valid;

	/******** 数据验证 *******/
	$.validate = (function () {
		var handler = {
			focus: function (e) {
				var isEmpty,
					eventType = e ? e.type : null;

				this.bluring = false;
				utils.check.call(this, eventType);

			},

			blur: function (e) {
				var that = this,
					eventType = e ? e.type : null;

				this.bluring = true;

				window.setTimeout(function () {

					if (!that.bluring) {
						return;
					}
					utils.check.call(that, eventType);
					utils.show.call(that);
				}, 180);
			}
		};

		function Validate() {
			this.ok = false;
			this.$elem = {};
			this.options = {
				custom: null,
				success: function () {},
				error: function () {}
			};
		}

		Validate.prototype = {
			constructor: Validate,

			init: function (options) {
				var $elems = $(".validatebox");

				this.options = $.extend(this.options, options);
				this.$elems = $elems;

				$elems.each(function () {
					var $this = $(this);

					$this.on("focus", handler.focus)
						.on("blur", handler.blur)
						.on("keyup", function () {
							utils.check.call(this, 'keyup');
						});

				});

				$(".textboxs").each(function () {
					var textBox = this;

					if ($(this).hasClass('validatebox')) {

						$(this).find('input').on("focus", function (e) {
							handler.focus.call(textBox, e);
						}).on('blur', function (e) {
							handler.blur.call(textBox, e);
						});

					}
				});
			},

			addElems: function (elems) {
				$(elems).on("focus", handler.focus)
					.on("blur", handler.blur)
					.on("keyup", function () {
						utils.check.call(this, 'keyup');
					});
			},

			check: function (elems) {
				$(elems).each(utils.check);
			},

			checkAll: function (id) {
				var customResult = '',
					selector = id ? "#" + id + " .validatebox" : ".validatebox";

				utils.errorNum = 0; //add by zzc errorNum will increase when the elem is unvalid, see function 'check' in util
				$(selector).each(function () {
					utils.check.apply(this, []);
				});

				if (utils.errorNum === 0) {
					if (typeof this.options.custom === 'function') {
						customResult = this.options.custom();
					}

					if (!customResult) {
						this._success();

						return true;
					}

				}

				this._error(customResult);
			},

			message: function () {

			},

			_success: function () {
				this.ok = true;

				if (typeof this.options.success === 'function') {
					this.options.success();
				}
			},

			_error: function (customResult) {
				this.ok = false;

				if (typeof this.options.error === 'function') {
					this.options.error(customResult);
				}
			}
		};

		return function (options) {
			var validataInstance = new Validate();
			validataInstance.init(options);

			return validataInstance;
		};
	}());

	/* 数据验证函数集合对象 */
	valid = {
		len: function (str, min, max) {
			var len = str.length;

			if (len < min || len > max) {
				return _("Value length range: %s - %s bytes", [min, max]);
			}
		},

		byteLen: function (str, min, max) {
			var totalLength = 0,
				charCode;

			for (var i = str.length - 1; i >= 0; i--) {
				charCode = str.charCodeAt(i);
				if (charCode <= 0x007f) {
					totalLength++;
				} else if ((charCode >= 0x0080) && (charCode <= 0x07ff)) {
					totalLength += 2;
				} else if ((charCode >= 0x0800) && (charCode <= 0xffff)) {
					totalLength += 3;
				} else {
					totalLength += 4;
				}
			}

			if (totalLength < min || totalLength > max) {
				return _("Value length range: %s - %s characters", [min, max]);
			}
		},

		num: function (str, min, max) {

			if (!(/^[0-9]{1,}$/).test(str)) {
				return _("Enter digits.");
			}
			if (typeof min != "undefined" && typeof max != "undefined") {
				if (parseInt(str, 10) < min || parseInt(str, 10) > max) {

					return _("Value range: %s - %s", [min, max]);
				}
			}
		},

		mac: {
			all: function (str) {
				var ret = this.specific(str);

				if (ret) {
					return ret;
				}

				if (!(/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/).test(str)) {
					return _("Please enter a valid MAC address.");
				}
			},

			specific: function (str) {
				var subMac1 = str.split(':')[0];

				if (subMac1.charAt(1) && parseInt(subMac1.charAt(1), 16) % 2 !== 0) {
					return _("The second character in the MAC address must be an even number.");
				}
				if (str === "00:00:00:00:00:00") {
					return _("The MAC address cannot be 00:00:00:00:00:00.");
				}
			}
		},

		ip: {
			all: function (str) {
				var ret = this.specific(str);

				if (ret) {
					return ret;
				}

				if (!(/^([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.){2}([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-4])$/).test(str)) {
					return _("Please enter a valid IP address.");
				}
			},

			specific: function (str) {
				var ipArr = str.split('.'),
					ipHead = ipArr[0];

				if (ipArr[0] === '127') {
					return _("An IP address that begins with 127 is a loopback IP address. Adopt another value ranging from 1 through 223.");
				}
				if (ipArr[0] > 223) {
					return _("An IP address that begins with %s is invalid. Adopt another value ranging from 1 through 223.", [ipHead]);
				}
			}
		},

		dns: function () {},

		mask: {
			all: function (str) {
				var rel = /^(255|254|252|248|240|224|192|128)\.0\.0\.0$|^(255\.(254|252|248|240|224|192|128|0)\.0\.0)$|^(255\.255\.(254|252|248|240|224|192|128|0)\.0)$|^(255\.255\.255\.(248|240|224|192|128|0))$/;
				if (!rel.test(str)) {
					return _("Please enter a valid subnet mask.");
				}

			}
		},

		/*function (str) {
			var rel = /^(254|252|248|240|224|192)\.0\.0\.0$|^(255\.(254|252|248|240|224|192|128|0)\.0\.0)$|^(255\.255\.(254|252|248|240|224|192|128|0)\.0)$|^(255\.255\.255\.(252|248|240|224|192|128|0))$/;
			if(!rel.test(str)) {
				return _("Please enter a valid subnet mask.");
			}
		}*/

		email: function (str) {
			var rel = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			if (!rel.test(str)) {
				return _("Please input a validity E-mail address");
			}

		},

		time: function (str) {
			if (!(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/).test(str)) {
				return _("Please input a valid time.");
			}
		},

		hex: function (str) {
			if (!(/^[0-9a-fA-F]{1,}$/).test(str)) {
				return _("Must be hex.");
			}
		},

		ascii: function (str, min, max) {
			if (!(/^[ -~]+$/g).test(str)) {
				return _("Enter only ASCII characters.");
			}
			if (min || max) {
				return valid.len(str, min, max);
			}
		},

		pwd: function (str, minLen, maxLen) {
			var ret;

			if (!(/^[0-9a-zA-Z_]+$/).test(str)) {
				return _("Only digits, letters, and underscores are allowed.");
			}

			if (minLen && maxLen) {
				ret = $.validate.valid.len(str, minLen, maxLen);
				if (ret) {
					return ret;
				}
			}
		},

		username: function (str) {
			if (!(/^\w{1,}$/).test(str)) {
				return _("Please input a validity username.");
			}
		},

		ssidPasword: function (str, minLen, maxLen) {
			var ret;
			ret = $.validate.valid.ascii(str);
			if (!ret && minLen && maxLen) {
				ret = valid.len(str, minLen, maxLen);
				if (ret) {
					return ret;
				}
			}
			return ret;
		},

		remarkTxt: function (str, banStr) {
			var len = banStr.length,
				curChar,
				i;

			for (i = 0; i < len; i++) {
				curChar = banStr.charAt(i);
				if (str.indexOf(curChar) !== -1) {
					return _("'%s' is not allowed.", [curChar]);
				}
			}
		},

		routeCheck: {
			all: function (str) {
				var ret = this.specific(str);

				if (ret) {
					return ret;
				}

				if (!(/^([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.){2}([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])$/).test(str)) {
					return _("Please enter an IP network segment.");
				}
			},

			specific: function (str) {
				var ipArr = str.split('.'),
					ipHead = ipArr[0];

				if (ipArr[0] === '127') {
					return _("An IP address that begins with 127 is a loopback IP address. Adopt another value ranging from 1 through 223.");
				}
				if (ipArr[0] > 223) {
					return _("An IP address that begins with %s is invalid. Adopt another value ranging from 1 through 223.", [ipArr[0]]);
				}
			}
		}

	};

	$.validate.utils = utils;
	$.validate.valid = valid;

	// 中文翻译

	$.validate.valid = valid;

	/* Validate Tip */
	$.validateTipId = 0;
	$.include({
		addValidateTip: function (str) {
			var $this = this;

			function createTipElem(id, str, elem) {
				var tipElem = document.createElement('span'),
					tipId = "reasy-validate-tip-" + id,
					span;

				elem.validateTipId = tipId;

				tipElem.innerHTML = '<span id="' + tipId +
					'" class="validatebox-tip">' +
					'<span class="validatebox-tip-content">' + _(str) + '</span>' +
					'<span class="validatebox-tip-pointer"></span>' +
					'</span>';

				return tipElem;
			}

			return this.each(function () {
				var tipElem,
					$tipElem;

				tipElem = createTipElem($.validateTipId++, _(str), this);
				$tipElem = $(tipElem).css({
					"position": "absolute",
					"width": "0",
					"height": "0"
				});
				//$this.parent().append(tipElem);
				$this.before(tipElem);
			});
		},

		showValidateTip: function () {
			return this.each(function () {
				$("#" + this.validateTipId).css("visibility", "visible");
			});
		},

		hideValidateTip: function () {
			return this.each(function () {
				$("#" + this.validateTipId).css("visibility", "hidden");
			});
		},

		removeValidateTip: function (valid) {
			return this.each(function () {
				var $tipElem = $("#" + this.validateTipId);

				if (!$tipElem) {
					return;
				}
				$("#" + this.validateTipId).parent().remove();
				this.validateTipId = '';
			});
		}
	});
})(window, document);


$.extend($.validate.valid, {
	//fh1901 的一版用户名和密码的有效验证，英文字符，不能包含非法字符 \ ~ ; ' & “ 6个特殊字符
	fh1901Ascii: function (str, min, max) {
		var asciiTestStr = $.validate.valid.ascii(str, min, max);
		if (!asciiTestStr) {
			//通过了一般ascii测试
			if (/[\\\~;'&\s"]/.test(str)) {
				return _("'%s' and space are not allowed.", ["\~\;\'\&\""]);
			}
		} else {
			return asciiTestStr;
		}
	},
	domain: function (str) {
		if (!/^[\d\.]+$/.test(str)) {
			if (/^([\w-]+\.)+(\w)+$/.test(str))
				return;
		} else {
			if (!$.validate.valid.ip.all(str))
				return;
		}

		return _("Please enter a valid domain name.");
	},

	ssid: {
		all: function (str) {
			var ret = this.specific(str);
			//ssid 前后不能有空格，可以输入任何字符包括中文，仅32个字节的长度
			if (ret) {
				return ret;
			}

			if (str.charAt(0) == " " || str.charAt(str.length - 1) == " ") {
				return _("The first and last characters of WiFi Name cannot be spaces.");
			}
		},
		specific: function (str) {
			var ret = str;
			if ((null == str.match(/[^ -~]/g) ? str.length : str.length + str.match(/[^ -~]/g).length * 2) > 32) {
				return _("The WiFi name can contain only a maximum of %s bytes.", [32]);
			}
		}
	},

	ssidPwd: {
		all: function (str) {
			var ret = this.specific(str);

			if (ret) {
				return ret;
			}
			if (str.length < 8 || str.length > 63) {
				return _("The password must consist of %s-%s characters.", [8, 63]);
			}
			//密码不允许输入空格
			//if (str.indexOf(" ") >= 0) {
			//	return _("The WiFi password cannot contain spaces.");
			//}
			//密码前后不能有空格
			if (str.charAt(0) == " " || str.charAt(str.length - 1) == " ") {
				return _("The first and last characters of WiFi Password cannot be spaces.");
			}
		},
		specific: function (str) {
			var ret = str;
			if (/[^\x00-\x80]/.test(str)) {
				return _("Invalid characters are not allowed.");
			}
		}
	}
});