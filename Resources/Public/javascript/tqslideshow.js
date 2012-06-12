$(document).ready(function() {

	$.fn.tqSlideshow = function(options,arg){

		var options = $.extend( {
		'transitiontn'					: 'fade',		// default transition of the next image
		'transitiontb'					: 'slideDown',	// transition for the thumbnails
		'showThumbnails'				: true,			// show thumbnails
		'showToolbar'					: true,			// show toolbars
		'thumbnailDirection'			: true,			// vertical or horizontal
		'numberOfThumbnailsToDisplay'	: 3,			// number of Thumbnails to show
		'imageCount'					: null,			// count of images
		'containerWidth'				: null,			// the optional container width
		'containerHeight'				: null,			// the optional container height
		'slideCount'					: null,			// current slide position
		'carousel'						: true,			// jcarousel active for thumbnails
		'changeTime'					: false,		// The changetime of the slideshow
		'mode'							: 'normal',		// in which order the images should display
		'keyEvents'						: true			// active the key event handling
		}, options);


		if(options.imageCount <= 1 ) {
			options.showThumbnails	= false;
			options.showToolbar		= false;
			options.keyEvents		= false;
			options.changeTime		= false;
		}

		var id					= options.id;
		var element				= $(this);
		var wrapper				= $('#slideshow-wrapper-'+id);
		var counterTmp			= options.imageCount-1;
		var eventLock			= false;
		var debug				= true;
		var carouselObj			= null;
		var carouselItemWidth	= null;

		function cl() {
			if(debug && console && console.log && console.log.apply) {
				return console.log.apply(console, arguments);
			}
		}

		function setContainerProporties(){
			var tmpWidth	= options.containerWidth;
			var tmpHeight	= options.containerHeight;
			if(tmpWidth == null){
				$('.slideshow-image',element).each(function(){
					var imgWidth	= $(this).width();
					if( imgWidth > tmpWidth ) {
						tmpWidth	= imgWidth;
					}
				});
				options.containerWidth	= tmpWidth;
			}
			element.css('width',tmpWidth);

			if(tmpHeight == null){
				$('.slideshow-image',element).each(function(){
					var imgHeight	= $(this).height();

					if( imgHeight > tmpHeight ){
						tmpHeight	= imgHeight;
					}
				});
				options.containerHeight	= tmpHeight;
			}
			element.css('height',tmpHeight);
		}

		var currentSlide	= options.imageCount;
		var countSlider		= 0;
		var bc				= $('#toolbar-list-'+id);

		$('.slideshow-preview-text',wrapper).hide();

		var tbWrapper		= $('#slideshow-thumbnail-list-wrapper-'+id);
		var tbList			= null;

		var timeoutStorage	= false;
		var lightboxActive	= false;



		if(options.imageCount > 1) {
			element.cycle({
				fx: options.stdEffect,
				speed			: 300,
				autostop		: 1,
				autostopCount	: 1,
				sync			: 1,
				fit				: 1,
				startingSlide	: options.startItem,
				before			: onBeforeCycle,
				after			: onAfterCycle
			});
		}

		function onBeforeCycle(curr,next,opts) {
			var previewTextContainer	= $('.preview-text-'+opts.currSlide,wrapper);
			var init = 	$.fn.tqSlideshow.transitiontb[options.transitiontb];
			if ($.isFunction(init)) {

				init(previewTextContainer,'before');
			} else {
				var initDefault = $.fn.tqSlideshow.transitiontb['show'];
				initDefault(previewTextContainer,mode);
			}
		}


		function onAfterCycle(curr,next,opts) {
			var previewTextContainer	= $('.preview-text-'+opts.currSlide,wrapper);
			var init = 	$.fn.tqSlideshow.transitiontb[options.transitiontb];
			if ($.isFunction(init)) {
				init(previewTextContainer,'after');
			} else {
				var initDefault = $.fn.tqSlideshow.transitiontn['show'];
				initDefault(previewTextContainer,mode);
			}
			currentSlide	= opts.currSlide+1;
			countSlider		= opts.slideCount;

			if(options.changeTime > 0 ) {
				resetIntervall();
			}
		}

		function keyEvents(){
			var body	= $('body');
			body.keypress(function(event){
				event.stopPropagation();
				// up		= 38
				// left		= 37
				// right	= 39
				// down		= 40
				var keyCode	= event.keyCode;
				if(options.thumbnailDirection == "true"){
					if( keyCode == 37 ) {
						prevSlide();
					} else if ( keyCode == 39 ) {
						nextSlide();
					}
				} else {
					if( keyCode == 38 ) {
						nextSlide();
					} else if ( keyCode == 40 ) {
						prevSlide();
					}
				}
			});
		}

		if(options.keyEvents){
			keyEvents();
		}

		function itemVisibleOutCallback(a,b,c,d){
			var nextState	= (c % options.imageCount);
			if(nextState == 0){
				nextState	= options.imageCount;
			}
			if( currentSlide	== nextState ){
				$(b).addClass('active');
			}
		}

		function setToolbar(){
			element.children().each(function(i) {
				$('<li><span class="slideshow-toolbar-list-element list-element-'+(i+options.imageCount+1)+'">'+(i+options.imageCount+1)+'</span></li>').appendTo(bc).click(function() {
					var el	 =$(this);
					cycleAbs( i, 0, i);

						thumbnailObj.gotoToolbar(i+1);

					if(options.changeTime > 0 ) {
						resetIntervall();
					}
					return false;
				});
			});
			$('li:eq(0)',bc).addClass('active');
		}

		function setEvent() {
			$('.backward.controller',wrapper).click(function() {
				prevSlide();
				return false;
			});
			$('.forward.controller',wrapper).click(function() {
				nextSlide();
				return false;
			});
		}

		function checkEventLock() {
			if( eventLock ) {
				return true;
			}
			try {
				window.clearTimeout(eventLock);
			} catch(e) {}
			eventLock = window.setTimeout(function() {eventLock = false;}, 750);

			return false;
		}

		/**
		 * Cycle the element and set the active states of the toolbar and thumbnails
		 */
		function cycle(imageNum, direction){
			counterTmp	= counterTmp + direction;
			counterTmp	= (counterTmp % options.imageCount) + options.imageCount;
			cycleAbs(imageNum, direction, counterTmp);

		}

		this.cycleAbs = cycleAbs = function(imageNum, direction, carouselPos){
			imageNum	 = imageNum % options.imageCount;
			element.cycle(imageNum, options.effectList[imageNum]);

			if(bc){
				$('li',bc).removeClass('active');
				var newActiveListlement	= $('li:eq('+imageNum+')',bc);
				newActiveListlement.addClass('active');
			}
		}

		this.nextSlide = nextSlide = function () {
			if(checkEventLock()) return;
			// untested beta status
			if(options.mode == 'random') {
				var offset			= options.imageCount;
				var tmpCurrentSlide	=  Math.floor(Math.random() * parseInt(offset));
				thumbnailObj.gotoToolbar(tmpCurrentSlide+1);
				cycleAbs( tmpCurrentSlide, 0, tmpCurrentSlide);
			} else {
				thumbnailObj.gotoNext();
				cycle(currentSlide, 1);
			}

			resetIntervall();
		}

		function prevSlide(){
			if(checkEventLock()) return;

			if( currentSlide == 1) {
				currentSlide	= countSlider-1;
			} else {
				currentSlide	= currentSlide-2;
			}
			thumbnailObj.gotoPrevious();
			cycle(currentSlide, -1);
			resetIntervall();
		}

		element.mouseover(function(){
			stopIntervall()
		});

		element.mouseout(function(){
			if(!lightboxActive){
				startIntervall();
			}
		});

		function resetIntervall(){
			stopIntervall();
			startIntervall();
		}

		function stopIntervall(){
			if(timeoutStorage){
				window.clearTimeout(timeoutStorage);
			}
			timeoutStorage	= false;
		}

		function startIntervall(){
//			cl('start Intervall timer');
			if( options.changeTime > 0 ) {
				if(timeoutStorage) {
					try {
						window.clearTimeout(timeoutStorage);
					} catch(e) {}
				}

				timeoutStorage = window.setTimeout(function() {
					nextSlide();
				}, options.changeTime);
			}
		}
		startIntervall();

		// show thumbnails if active
		if(options.showThumbnails && tbWrapper ) {
			// initialize Thumbnails
		} else {
			tbWrapper.hide();
		}

		// show toolbar if active
		if(options.showToolbar){
			setToolbar();
		}

		// set events
		setEvent();
		jQuery('.tq-rzColobor-Content'+id).colorbox({
			onOpen: function(){
				lightboxActive	= true;
				stopIntervall();
			},
			onClosed: function(){
				lightboxActive	= false;
				startIntervall();
			}
		});

		var thumbnailObj	= $('#slideshow-thumbnail-list-wrapper-'+id).tqThumbnail(options,this);
		return this;
	}

	$.fn.tqSlideshow.transitiontb = {
		fade: function(element,mode) {
			if (mode != 'after'){
				element.fadeOut('fast');
			} else {
				element.fadeIn('slow');
			}
		},

		slideDown: function(element,mode) {

			if (mode != 'after'){
				element.slideUp();
			} else {
				element.slideDown();
			}
		},
		show: function(element,mode) {
			element.show();
		}
	};





});