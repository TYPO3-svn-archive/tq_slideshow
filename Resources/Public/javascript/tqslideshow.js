(function($){
	$.fn.tqSlideshow = function(options,arg){

			var options = $.extend( {
				'transitiontn'					: 'fade',		// default transition of the next image
				'transitiontb'					: 'show',		// transition for the thumbnails
				'transitionPreview'				: 'fade',		// transition for the preview-text
				'showThumbnails'				: true,			// show thumbnails
				'showToolbar'					: true,			// show toolbars
				'thumbnailDirection'			: true,			// vertical or horizontal
				'imageCount'					: null,			// count of images
				'containerWidth'				: null,			// the optional container width
				'containerHeight'				: null,			// the optional container height
				'slideCount'					: null,			// current slide position
				'carousel'						: true,			// jcarousel active for thumbnails
				'changeTime'					: false,		// The changetime of the slideshow
				'mode'							: 'normal',		// in which order the images should display
				'keyEvents'						: true,			// active the key event handling
				'responsive'					: true
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
			var stopChangeTime		= false;
			var begin				= true;

			function cl() {
				if(debug && console && console.log && console.log.apply) {
					return console.log.apply(console, arguments);
				}
			}

		var currentSlide	= options.imageCount;

		var countSlider		= 0;
		var bc				= $('#toolbar-list-'+id);

		$('.slideshow-preview-text',wrapper).hide();

		var tbWrapper		= $('#slideshow-thumbnail-list-wrapper-'+id);
		var tbList			= null;

		var timeoutStorage	= false;
		var lightboxActive	= false;


		if(options.imageCount == 1) {
			$(".slideshow-controls",wrapper).hide();
		}

		if(options.imageCount > 1) {
			element.cycle({
				fx: options.stdEffect,
				speed			: 300,
				autostop		: 1,
				autostopCount	: 1,
				speedIn			: 500,
				speedOut		: 500,
				easeIn			:  'easeInCirc',
				easeOut			:  'easeInCirc',
				sync			: 1,
				fit				: 1,
				startingSlide	: options.startItem,
				before			: onBeforeCycle,
				after			: onAfterCycle
			});
		} else {
			$('.preview-text-0',wrapper).show();
		}

		if(options.keyEvents){
			keyEvents();
		}
		setEvent();
		startIntervall();
		jPlayerControls();

		var newActiveImg	= $('div.mediaContainer:eq(0)',element);
		newActiveImg.addClass('active');


		function onBeforeCycle(curr,next,opts) {
			// stop all movies inside container
			// for a better solution please contact nico@teqneers.de
			$.each(($.jPlayer.prototype.instances), function(i, element) {
				if(element.parents('#slideshow-wrapper-'+id).length) {
					if(element.data("jPlayer").status.srcSet) { // Check that media is set otherwise would cause error event.
						element.jPlayer("pause");
						$(".jp-play",wrapper).addClass("active");
						$(".jp-pause",wrapper).removeClass("active");
					}
				}
			});


			var youtubeCheck	= false;
			var currentEl		= element.children().eq(opts.currSlide);
			var ytubeEl			= $(".video iframe",currentEl );

			if( ytubeEl.length ) {
				var timer	= 1000;

				var yTube	= window.setTimeout(function(){
					var src	= ytubeEl.attr("src");
					ytubeEl.attr("src","");
					ytubeEl.attr("src",src);
				}, timer);
			}



			var fCtr	= $(".forward.controller",wrapper);
			var bCtr	= $(".backward.controller",wrapper);

			if(!begin) {
				if(fCtr.length) {
					fCtr.fadeOut();
				}

				if(bCtr.length) {
					bCtr.fadeOut();
				}
			}

			var previewTextContainer	= $('.preview-text-'+opts.currSlide,wrapper);

			var init = 	$.fn.tqSlideshow.transitiontb[options.transitionPreview];
			if ($.isFunction(init)) {
				init(previewTextContainer,'before');
			} else {
				var initDefault = $.fn.tqSlideshow.transitiontb['hide'];
				initDefault(previewTextContainer,mode);
			}
			begin = false;
			checkResponsive();
		}


		function onAfterCycle(curr,next,opts) {

			var previewTextContainer	= $('.preview-text-'+opts.currSlide,wrapper);

			var init = 	$.fn.tqSlideshow.transitiontb[options.transitionPreview];
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

			var fCtr	= $(".forward.controller",wrapper);
			var bCtr	= $(".backward.controller",wrapper);

			if(fCtr.length) {
				fCtr.fadeIn();
			}

			if(bCtr.length) {
				bCtr.fadeIn();
			}
		}

		var movieIsPlaying	= false;
		function jPlayerControls() {
			var playBtnList		= $(".jp-play",element);
			var pauseBtnList	= $(".jp-pause",element);

			playBtnList.each(function(){
				var el = $(this);
				el.click(function(){
					var trigger	= $(this);
					trigger.removeClass("active");
					$('.jp-pause',trigger.parents(".jp-controls")).addClass("active");
					movieIsPlaying = true;
					stopIntervall();
				});
			});

			pauseBtnList.each(function(){
				var el = $(this);
				el.click(function(){
					var trigger	= $(this);
					trigger.removeClass("active");
					$('.jp-play',trigger.parents(".jp-controls")).addClass("active");
					movieIsPlaying = false;
					resetIntervall();

				});
			});

			wrapper.mouseleave(function(){
				$('.jp-pause.active',wrapper).fadeOut();
			});

			wrapper.mouseenter(function(){
				$('.jp-pause.active',wrapper).fadeIn();
			});

		}

		function checkResponsive(){
			if(options.responsive) {
				element.css('width','100%');
				$('.slideshow-image',element).css({
					'width': '100%',
					'height': 'auto'
				});
			}
		}

		function keyEvents(){
			var body	= $('body');

			var keypressFn	= function(event){
				event.stopPropagation();
				// up		= 38
				// left		= 37
				// right	= 39
				// down		= 40
				var keyCode	= event.keyCode;
				if(options.thumbnailDirection){
					if( keyCode == 38 ) {
						nextSlide();
					} else if ( keyCode == 40 ) {
						prevSlide();
					}
				} else {
					if( keyCode == 37 ) {
						prevSlide();
					} else if ( keyCode == 39 ) {
						nextSlide();
					}
				}
			}

			if ($.browser.mozilla) {
				$(document).keypress (keypressFn);
			} else {
				$(document).keydown (keypressFn);
			}
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
				$('<li class="tq-t-item"><span class="slideshow-toolbar-list-element list-element-'+(i+options.imageCount+1)+'">'+(i+options.imageCount+1)+'</span></li>').appendTo(bc).click(function() {
					var el	 =$(this);

					var direction	= 1;
					if(currentSlide > i ) {
						direction	= 0;
					}
					cycleAbs( i, direction, i);
					thumbnailObj.gotoToolbar(i+1,direction);
					if(options.changeTime > 0 ) {
						resetIntervall();
					}
					return false;
				});
			});
			$('li.tq-t-item:eq(0)',bc).addClass('active');
		}

		function setEvent() {
			$('.backward.controller',wrapper).click(function() {
				lightboxActive = false;
				prevSlide();
				return false;
			});

			$('.forward.controller',wrapper).click(function() {
				lightboxActive = false;
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

		 var cycleAbs = function(imageNum, direction, carouselPos) {
			imageNum	 = imageNum % options.imageCount;

			var effect	= '';
			if(direction > 0 ) {
				effect	= options.effectList[imageNum][0];
			} else {
				effect	= options.effectList[imageNum][1];
			}
			element.cycle(imageNum, effect);

			if(bc){
				$('li.tq-t-item',bc).removeClass('active');
				var newActiveListlement	= $('li.tq-t-item:eq('+imageNum+')',bc);
				newActiveListlement.addClass('active');
			}

			$('div.mediaContainer',element).removeClass("active");
			var newActiveImg	= $('div.mediaContainer:eq('+imageNum+')',element);
			newActiveImg.addClass('active');

		}

		this.cycleAbs = cycleAbs;

		function nextSlide () {
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

		function prevSlide() {
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

		wrapper.mouseover(function(){
			stopIntervall()
		});


		wrapper.mouseout(function(){
			if(!stopChangeTime && !movieIsPlaying ) {
				if(!lightboxActive){
					startIntervall();
				}
			}
		});


		$('div.mediaContainer a', wrapper).click(function(){
			stopIntervall();
			lightboxActive = true;
			autoPlayBtn.removeClass("run");
		});


		var autoPlayBtn	= $(".tq-auto-play",wrapper);
		if(autoPlayBtn.length) {

			if(options.changeTime > 0 ) {
				autoPlayBtn.addClass("run");
			} else {
				autoPlayBtn.hide();
			}

			autoPlayBtn.click(function(){
				if(autoPlayBtn.hasClass('run')){
					stopChangeTime = true;
					stopIntervall();
				} else {
					lightboxActive = false;
					stopChangeTime = false;
				}
				autoPlayBtn.toggleClass("run");
			});
		}

		function resetIntervall(){
			if(!stopChangeTime) {
				stopIntervall();
				startIntervall();
			}
		}

		function stopIntervall(){
			if(timeoutStorage){
				window.clearTimeout(timeoutStorage);
			}
			timeoutStorage	= false;
		}

		function startIntervall(){
			//cl('start Intervall timer');
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

		if(options.imageCount > 1) {
			var tbElement	= $('#tq-tb-wrapper-'+id, wrapper);
			var thumbnailObj = tbElement.tqThumbnail(options,this);
		}
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

})(jQuery);