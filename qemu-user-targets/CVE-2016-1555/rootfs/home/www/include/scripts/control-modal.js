/**
 * @author Ryan Johnson <ryan@livepipe.net>
 * @copyright 2007 LivePipe LLC
 * @package ModalWindow
 * @license MIT
 * @url http://livepipe.net/projects/control_modal/
 * @version 2.2.3
 */

ModalWindow = Class.create();
Object.extend(ModalWindow,{
	loaded: false,
	loading: false,
	loadingTimeout: false,
	isOpen: false,
	overlay: false,
	container: false,
	current: false,
	ie: false,
	effects: {
		containerFade: false,
		containerAppear: false,
		overlayFade: false,
		overlayAppear: false
	},
	targetRegexp: /#(.+)$/,
	imgRegexp: /\.(jpe?g|gif|png|tiff?)$/i,
	overlayStyles: {
		position: 'fixed',
		top: 0,
		left: 0,
		width: '100%',
		height: '100%',
		zIndex: 9998
	},
	overlayIEStyles: {
		position: 'absolute',
		top: 0,
		left: 0,
		zIndex: 9998
	},
	disableHoverClose: false,
	load: function(){
		if(!ModalWindow.loaded){
			ModalWindow.loaded = true;
			ModalWindow.ie = !(typeof document.body.style.maxHeight != 'undefined');
			ModalWindow.overlay = $(document.createElement('div'));
			ModalWindow.overlay.id = 'modal_overlay';
			Object.extend(ModalWindow.overlay.style,ModalWindow['overlay' + (ModalWindow.ie ? 'IE' : '') + 'Styles']);
			ModalWindow.overlay.hide();
			ModalWindow.container = $(document.createElement('div'));
			ModalWindow.container.id = 'modal_container';
			ModalWindow.container.hide();
			ModalWindow.loading = $(document.createElement('div'));
			ModalWindow.loading.id = 'modal_loading';
			ModalWindow.loading.hide();
			var body_tag = document.getElementsByTagName('body')[0];
			body_tag.appendChild(ModalWindow.overlay);
			body_tag.appendChild(ModalWindow.container);
			body_tag.appendChild(ModalWindow.loading);
			ModalWindow.container.observe(this.element,'mouseout',function(event){
				if(!ModalWindow.disableHoverClose && ModalWindow.current && ModalWindow.current.options.hover && !Position.within(ModalWindow.container,Event.pointerX(event),Event.pointerY(event)))
					ModalWindow.close();
			});
		}
	},
	open: function(contents,options){
		options = options || {};
		if(!options.contents)
			options.contents = contents;
		var modal_instance = new ModalWindow(false,options);
		modal_instance.open();
		this.isOpen = true;
		return modal_instance;
	},
	close: function(force){
		if(typeof(force) != 'boolean')
			force = false;
		if(ModalWindow.current)
			ModalWindow.current.close(force);
		this.isOpen = false;
	},
	attachEvents: function(){
		//Event.observe(window,'load',ModalWindow.load);
		//Event.observe(window,'unload',Event.unloadCache,false);
	},
	center: function(element){
		if(!element._absolutized){
			element.setStyle({
				position: 'absolute'
			});
			element._absolutized = true;
		}
		var dimensions = element.getDimensions();
		Position.prepare();
		var offset_left = (Position.deltaX + Math.floor((ModalWindow.getWindowWidth() - dimensions.width) / 2));
		var offset_top = (Position.deltaY + ((ModalWindow.getWindowHeight() > dimensions.height) ? Math.floor((ModalWindow.getWindowHeight() - dimensions.height) / 2) : 0));
		element.setStyle({
			top: ((dimensions.height <= ModalWindow.getDocumentHeight()) ? ((offset_top != null && offset_top > 0) ? offset_top : '0') + 'px' : 0),
			left: ((dimensions.width <= ModalWindow.getDocumentWidth()) ? ((offset_left != null && offset_left > 0) ? offset_left : '0') + 'px' : 0)
		});
	},
	getWindowWidth: function(){
		return (self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth || 0);
	},
	getWindowHeight: function(){
		return (self.innerHeight ||  document.documentElement.clientHeight || document.body.clientHeight || 0);
	},
	getDocumentWidth: function(){
		return Math.min(document.body.scrollWidth,ModalWindow.getWindowWidth());
	},
	getDocumentHeight: function(){
		return Math.max(document.body.scrollHeight,ModalWindow.getWindowHeight());
	},
	onKeyDown: function(event){
		if(event.keyCode == Event.KEY_ESC)
			ModalWindow.close();
	}
});
Object.extend(ModalWindow.prototype,{
	mode: '',
	html: false,
	href: '',
	element: false,
	src: false,
	imageLoaded: false,
	ajaxRequest: false,
	isOpen: false,
	initialize: function(element,options){
		this.element = $(element);
		this.options = {
			beforeOpen: Prototype.emptyFunction,
			afterOpen: Prototype.emptyFunction,
			beforeClose: Prototype.emptyFunction,
			afterClose: Prototype.emptyFunction,
			onSuccess: Prototype.emptyFunction,
			onFailure: Prototype.emptyFunction,
			onException: Prototype.emptyFunction,
			beforeImageLoad: Prototype.emptyFunction,
			afterImageLoad: Prototype.emptyFunction,
			autoOpenIfLinked: true,
			contents: false,
			loading: false, //display loading indicator
			fade: false,
			fadeDuration: 0.75,
			image: false,
			imageCloseOnClick: true,
			hover: false,
			iframe: false,
			iframeTemplate: new Template('<iframe src="#{href}" width="100%" height="100%" frameborder="0" id="#{id}"></iframe>'),
			evalScripts: true, //for Ajax, define here instead of in requestOptions
			requestOptions: {}, //for Ajax.Request
			overlayDisplay: true,
			overlayClassName: '',
			overlayCloseOnClick: true,
			containerClassName: '',
			opacity: 0.3,
			zIndex: 9998,
			width: null,
			height: null,
			offsetLeft: 0, //for use with 'relative'
			offsetTop: 0, //for use with 'relative'
			position: 'absolute' //'absolute' or 'relative'
		};
		Object.extend(this.options,options || {});
		var target_match = false;
		var image_match = false;
		if(this.element){
			target_match = ModalWindow.targetRegexp.exec(this.element.href);
			image_match = ModalWindow.imgRegexp.exec(this.element.href);
		}
		if(this.options.position == 'mouse')
			this.options.hover = true;
		if(this.options.contents){
			this.mode = 'contents';
		}else if(this.options.image || image_match){
			this.mode = 'image';
			this.src = this.element.href;
		}else if(target_match){
			this.mode = 'named';
			var x = $(target_match[1]);
			this.html = x.innerHTML;
			x.remove();
			this.href = target_match[1];
		}else{
			this.mode = (this.options.iframe) ? 'iframe' : 'ajax';
			this.href = this.element.href;
		}
		if(this.element){
			if(this.options.hover){
				this.element.observe(this.element,'mouseover',this.open.bind(this));
				this.element.observe(this.element,'mouseout',function(event){
					if(!Position.within(ModalWindow.container,Event.pointerX(event),Event.pointerY(event)))
						this.close();
				}.bindAsEventListener(this));
			}else{
				this.element.onclick = function(event){
					this.open();
					Event.stop(event);
					return false;
				}.bindAsEventListener(this);
			}
		}
		var targets = ModalWindow.targetRegexp.exec(window.location);
		this.position = function(event){
			if(this.options.position == 'absolute')
				ModalWindow.center(ModalWindow.container);
			else{
				var xy = (event && this.options.position == 'mouse' ? [Event.pointerX(event),Event.pointerY(event)] : Position.cumulativeOffset(this.element));
				ModalWindow.container.setStyle({
					position: 'absolute',
					top: xy[1] + (typeof(this.options.offsetTop) == 'function' ? this.options.offsetTop() : this.options.offsetTop) + 'px',
					left: xy[0] + (typeof(this.options.offsetLeft) == 'function' ? this.options.offsetLeft() : this.options.offsetLeft) + 'px'
				});
			}
			if(ModalWindow.ie){
				ModalWindow.overlay.setStyle({
					height: ModalWindow.getDocumentHeight() + 'px',
					width: ModalWindow.getDocumentWidth() + 'px'
				});
			}
		}.bind(this);
		if(this.mode == 'named' && this.options.autoOpenIfLinked && targets && targets[1] && targets[1] == this.href)
			this.open();
	},
	showLoadingIndicator: function(){
		if(this.options.loading){
			ModalWindow.loadingTimeout = window.setTimeout(function(){
				var modal_image = $('modal_image');
				if(modal_image)
					modal_image.hide();
				ModalWindow.loading.style.zIndex = this.options.zIndex + 1;
				ModalWindow.loading.update('<img id="modal_loading" src="' + this.options.loading + '"/>');
				ModalWindow.loading.show();
				ModalWindow.center(ModalWindow.loading);
			}.bind(this),250);
		}
	},
	hideLoadingIndicator: function(){
		if(this.options.loading){
			if(ModalWindow.loadingTimeout)
				window.clearTimeout(ModalWindow.loadingTimeout);
			var modal_image = $('modal_image');
			if(modal_image)
				modal_image.show();
			ModalWindow.loading.hide();
		}
	},
	open: function(force){
		if(!force && this.notify('beforeOpen') === false)
			return;
		if(!ModalWindow.loaded)
			ModalWindow.load();
		ModalWindow.close();
		if(!this.options.hover)
			Event.observe($(document.getElementsByTagName('body')[0]),'keydown',ModalWindow.onKeyDown);
		ModalWindow.current = this;
		if(!this.options.hover)
			ModalWindow.overlay.setStyle({
				zIndex: this.options.zIndex,
				opacity: this.options.opacity
			});
		ModalWindow.container.setStyle({
			zIndex: this.options.zIndex + 1,
			width: (this.options.width ? (typeof(this.options.width) == 'function' ? this.options.width() : this.options.width) + 'px' : null),
			height: (this.options.height ? (typeof(this.options.height) == 'function' ? this.options.height() : this.options.height) + 'px' : null)
		});
		if(ModalWindow.ie && !this.options.hover){
			$A(document.getElementsByTagName('select')).each(function(select){
				select.style.visibility = 'hidden';
			});
		}
		ModalWindow.overlay.addClassName(this.options.overlayClassName);
		ModalWindow.container.addClassName(this.options.containerClassName);
		switch(this.mode){
			case 'image':
				this.imageLoaded = false;
				this.notify('beforeImageLoad');
				this.showLoadingIndicator();
				var img = document.createElement('img');
				img.onload = function(img){
					this.hideLoadingIndicator();
					this.update([img]);
					if(this.options.imageCloseOnClick)
						$(img).observe($(img),'click',ModalWindow.close);
					this.position();
					this.notify('afterImageLoad');
					img.onload = null;
				}.bind(this,img);
				img.src = this.src;
				img.id = 'modal_image';
				break;
			case 'ajax':
				this.notify('beforeLoad');
				var options = {
					method: 'post',
					onSuccess: function(request){
						this.hideLoadingIndicator();
						this.update(request.responseText);
						this.notify('onSuccess',request);
						this.ajaxRequest = false;
					}.bind(this),
					onFailure: function(){
						this.notify('onFailure');
					}.bind(this),
					onException: function(){
						this.notify('onException');
					}.bind(this)
				};
				Object.extend(options,this.options.requestOptions);
				this.showLoadingIndicator();
				this.ajaxRequest = new Ajax.Request(this.href,options);
				break;
			case 'iframe':
				this.update(this.options.iframeTemplate.evaluate({href: this.href, id: 'modal_iframe'}));
				break;
			case 'contents':
				this.update((typeof(this.options.contents) == 'function' ? this.options.contents() : this.options.contents));
				break;
			case 'named':
				this.update(this.html);
				break;
		}
		if(!this.options.hover){
			if(this.options.overlayCloseOnClick && this.options.overlayDisplay)
				ModalWindow.overlay.observe(this.element,'click',ModalWindow.close);
			if(this.options.overlayDisplay){
				if(this.options.fade){
					if(ModalWindow.effects.overlayFade)
						ModalWindow.effects.overlayFade.cancel();
					ModalWindow.effects.overlayAppear = new Effect.Appear(ModalWindow.overlay,{
						queue: {
							position: 'front',
							scope: 'ModalWindow'
						},
						to: this.options.opacity,
						duration: this.options.fadeDuration / 2
					});
				}else
					ModalWindow.overlay.show();
			}
		}
		if(this.options.position == 'mouse'){
			this.mouseHoverListener = this.position.bindAsEventListener(this);
			this.element.observe(this.element,'mousemove',this.mouseHoverListener);
		}
		this.isOpen = true;
		this.notify('afterOpen');
	},
	isOpened: function(){
		return this.isOpen;
	},
	update: function(html){
		if(typeof(html) == 'string')
			ModalWindow.container.update(html);
		else{
			ModalWindow.container.update('');
			(html.each) ? html.each(function(node){
				ModalWindow.container.appendChild(node);
			}) : ModalWindow.container.appendChild(node);
		}
		if(this.options.fade){
			if(ModalWindow.effects.containerFade)
				ModalWindow.effects.containerFade.cancel();
			ModalWindow.effects.containerAppear = new Effect.Appear(ModalWindow.container,{
				queue: {
					position: 'end',
					scope: 'ModalWindow'
				},
				to: 1,
				duration: this.options.fadeDuration / 2
			});
		}else
			ModalWindow.container.show();
		this.position();
		//Event.observe(window,'resize',this.position,false);
		//Event.observe(window,'scroll',this.position,false);
	},
	close: function(force){
		if(!force && this.notify('beforeClose') === false)
			return;
		if(this.ajaxRequest)
			this.ajaxRequest.transport.abort();
		this.hideLoadingIndicator();
		if(this.mode == 'image'){
			var modal_image = $('modal_image');
			if(this.options.imageCloseOnClick && modal_image)
				modal_image.stopObserving('click',ModalWindow.close);
		}
		if(ModalWindow.ie && !this.options.hover){
			$A(document.getElementsByTagName('select')).each(function(select){
				select.style.visibility = 'visible';
			});
		}
		if(!this.options.hover)
			Event.stopObserving(window,'keyup',ModalWindow.onKeyDown);
		ModalWindow.current = false;
		Event.stopObserving(window,'resize',this.position,false);
		Event.stopObserving(window,'scroll',this.position,false);
		if(!this.options.hover){
			if(this.options.overlayCloseOnClick && this.options.overlayDisplay)
				ModalWindow.overlay.stopObserving('click',ModalWindow.close);
			if(this.options.overlayDisplay){
				if(this.options.fade){
					if(ModalWindow.effects.overlayAppear)
						ModalWindow.effects.overlayAppear.cancel();
					ModalWindow.effects.overlayFade = new Effect.Fade(ModalWindow.overlay,{
						queue: {
							position: 'end',
							scope: 'ModalWindow'
						},
						from: this.options.opacity,
						to: 0,
						duration: this.options.fadeDuration / 2
					});
				}else
					ModalWindow.overlay.hide();
			}
		}
		if(this.options.fade){
			if(ModalWindow.effects.containerAppear)
				ModalWindow.effects.containerAppear.cancel();
			ModalWindow.effects.containerFade = new Effect.Fade(ModalWindow.container,{
				queue: {
					position: 'front',
					scope: 'ModalWindow'
				},
				from: 1,
				to: 0,
				duration: this.options.fadeDuration / 2,
				afterFinish: function(){
					ModalWindow.container.update('');
					this.resetClassNameAndStyles();
				}.bind(this)
			});
		}else{
			ModalWindow.container.hide();
			ModalWindow.container.update('');
			this.resetClassNameAndStyles();
		}
		if(this.options.position == 'mouse')
			this.element.stopObserving('mousemove',this.mouseHoverListener);
		this.isOpen = false;
		this.notify('afterClose');
	},
	resetClassNameAndStyles: function(){
		ModalWindow.overlay.removeClassName(this.options.overlayClassName);
		ModalWindow.container.removeClassName(this.options.containerClassName);
		ModalWindow.container.setStyle({
			height: null,
			width: null,
			top: null,
			left: null
		});
	},
	notify: function(event_name){
		try{
			if(this.options[event_name])
				return [this.options[event_name].apply(this.options[event_name],$A(arguments).slice(1))];
		}catch(e){
			if(e != $break)
				throw e;
			else
				return false;
		}
	}
});
if(typeof(Object.Event) != 'undefined')
	Object.Event.extend(ModalWindow);
ModalWindow.attachEvents();