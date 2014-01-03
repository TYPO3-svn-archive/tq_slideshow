$(document).ready(function() {

	$.fn.tqThumbnail = function(options,tqSlideshow){
		var options = $.extend( {
			'transitiontb'					: 'show',	// transition for the thumbnails
			'showThumbnails'				: true,			// show thumbnails
			'thumbnailDirection'			: true,			// vertical or horizontal
			'numberOfThumbnailsToDisplay'	: 3,			// number of Thumbnails to show
			'slideCount'					: null,			// current slide position
			'carousel'						: true			// jcarousel active for thumbnails
		}, options);

		var currentSlide	= -1;
		var tqSlideShow		= tqSlideshow;
		var element			= $(this);			// The div container
		var tbList			= $(element.children()[0]);	// the UL list of the thumbnails
		var imgList			= tbList.children().length;

		var dispSlides	= options.imageCount;


		var carouselItemWidth = $('li:eq(0)',tbList).outerWidth(true);
		var offsetLeft			= carouselItemWidth- $('li:eq(0)',tbList).outerWidth();

		var wrapperWidth	= parseInt( $('li',tbList).length*3 ) * carouselItemWidth;


		tbList.css({
			'list-style'	: 'none',
			'width'			: wrapperWidth
		});


		// add the additional li elements for scolling
		var orgItemList	= tbList.html();
		tbList.append(orgItemList).prepend(orgItemList);
		var tbListWidth		  = parseInt(options.numberOfThumbnailsToDisplay * carouselItemWidth );




		element.css({
			'width'	: tbListWidth,
			'overflow': 'hidden'
		});

		element.css({
			'marginLeft': -(element.outerWidth(true)/2)-offsetLeft
		});


		$('li',tbList).each(function(i) {
			var el	= $(this);
			el.addClass('tqThumbnail');
			el.attr('item',i+1);



			$(this).click(function() {
				var direction	= 1;
				if(currentSlide > i ) {
					direction	= 0;
				}

				tqSlideShow.cycleAbs( i, direction, i);
				goto( i+1);
			});
		});

		function toggleTb(mode){
			// define your own function for display the thumbnails
			var init = $.fn.tqSlideshow.transitiontb[options.transitiontb];
			if ($.isFunction(init)) {
				init(tbList,mode);
			} else {
				//	console.log('transition effect for thumbnails is not defined use default fade');
				var initDefault = $.fn.tqSlideshow.transitiontb['fade'];
				initDefault(tbList,mode);
			}
		}

		var gotoNext = this.gotoNext = function() {
			goto(currentSlide +1);
		}

		var gotoPrevious = this.gotoPrevious = function() {
			goto(currentSlide-1);
		}

		var gotoToolbar = this.gotoToolbar = function(value,direction) {

			var val	= currentSlide;

			cl("Go  to thumb: "+value);
			goto(value);
		}

		var goto = this.goto = function( number ) {
			var direction = null;


			number = parseInt(number);
			currentSlide = parseInt(currentSlide);
			var slideItemCount = parseInt( dispSlides );

			number = parseInt(number) % (dispSlides) ;


			var numberToSlide 	= 0;
			var targetSlide 	= 0;
			var jumpToTarget 	= -1;


			if( currentSlide == -1 ) {
				// Init mode
				numberToSlide	= slideItemCount-1;
				targetSlide		= slideItemCount+1
				currentSlide	= slideItemCount+1;
			} else {

				targetSlide	= number
				// Slide mode
				if( number == currentSlide ) {
//					console.log("No slide now");
					return;
				}
				if( number > currentSlide) {
					targetSlide	= targetSlide+dispSlides;
				}
				if( number < currentSlide) {
					targetSlide	= targetSlide-dispSlides;
				}
				targetSlide = slideItemCount + number;
			}

			if(targetSlide > currentSlide ) {
				direction = -1;
			} else {
				direction = 1;
			}

			numberToSlide	= targetSlide - currentSlide;



			cl("++++++++++++++++++++++++++++");

			if( currentSlide == slideItemCount &&
				targetSlide == slideItemCount*2-1
			) {
				currentSlide = (slideItemCount*2);
				numberToSlide	= -1;
				targetSlide = (slideItemCount*2)-1;
			}

			if(currentSlide ==  slideItemCount*2-1 &&
				targetSlide == slideItemCount ) {
				numberToSlide	= 1;
			}


			cl("DIRECTION D:"+direction);
			cl("TARGET N:"+targetSlide);
			cl("Number: "+number);
			cl("Current Slide = "+currentSlide);
			cl("All Thumbs = "+slideItemCount);
			cl("Show Virtual Thumb = "+ parseInt(number+slideItemCount));
			cl("number to slide:" + numberToSlide);


			tbList.css('left',parseInt( carouselItemWidth*(currentSlide-2))*-1 );

			var posMove = parseInt(tbList.css('left'));
			// set the slide to the middle
			posMove	= posMove - carouselItemWidth * numberToSlide;

			cl("Current Pos:"+ tbList.css('left'));
			cl("Slide to:"+ posMove);

			tbList.animate({
				left: posMove
			});

//			console.log("trigger move to "+number+" from slide "+currentSlide+" to target slide " +targetSlide+" => go "+numberToSlide+" slides, direction "+direction);
//			console.log("pos move "+posMove);

			currentSlide = targetSlide;


			// set active class to new active thumbnail
			$('li',tbList).removeClass('active');
			$('li[item="'+(parseInt(number+slideItemCount))+'"]',tbList).addClass('active');
			$('li[item="'+(parseInt(number+slideItemCount)*2)+'"]',tbList).addClass('active');
		}

		this.goto(1);
		toggleTb('init');
		element.mouseenter(function(){
			toggleTb('show');
		});
		element.mouseleave(function(){
			toggleTb('hide');
		});

		return this;
	}

	$.fn.tqSlideshow.transitiontn = {
		fade: function(element,mode) {

			switch(mode){
				case 'init':
					element.hide();
					break;
				case 'show':
					element.fadeIn();
					break;
				case 'hide':
					element.fadeOut();
					break;

			}
		},
		show: function(element,mode) {
			element.show();
		}
	};


});