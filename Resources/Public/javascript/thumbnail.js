$(document).ready(function() {

	$.fn.tqThumbnail = function(options,tqSlideshow){
		var options = $.extend( {
			'transitiontb'					: 'slideDown',	// transition for the thumbnails
			'showThumbnails'				: true,			// show thumbnails
			'thumbnailDirection'			: true,			// vertical or horizontal
			'numberOfThumbnailsToDisplay'	: 3,			// number of Thumbnails to show
			'slideCount'					: null,			// current slide position
			'carousel'						: true			// jcarousel active for thumbnails
		}, options);

		var currentSlide	= -1;
		var boxWidth		= 163;
		var tqSlideShow		= tqSlideshow;


		function toggleTb(mode){

			// define your own function for display the thumbnails
			var init = $.fn.tqSlideshow.transitiontn[options.transitiontn];
			if ($.isFunction(init)) {
				init(tbList,mode);
			} else {
			//	console.log('transition effect for thumbnails is not defined use default fade');
				var initDefault = $.fn.tqSlideshow.transitiontn['fade'];
				initDefault(tbList,mode);
			}
		}


		var gotoNext = this.gotoNext = function() {
			goto(currentSlide +1);
		}

		var gotoPrevious = this.gotoPrevious = function() {
			goto(currentSlide-1);
		}


		var gotoToolbar = this.gotoToolbar = function(value) {
			goto(value+3);
		}


		var goto = this.goto = function( number, direction ) {
			var direction = null;

			number = parseInt(number);
			currentSlide = parseInt(currentSlide);
			var slideItemCount = parseInt( options.numberOfThumbnailsToDisplay);
			var numberToSlide = 0;
			var targetSlide = 0;

			var jumpToTarget = -1;

			if( currentSlide == -1 ) {
				// Init mode
				numberToSlide	= slideItemCount-1;
				targetSlide		= slideItemCount+1;
			} else {

				targetSlide	= number

				// Slide mode
				if( number == currentSlide ) {
					return;
				}

				if( number > currentSlide) {
					direction = 1;
				}

				if( number < currentSlide) {
					direction	= -1;
				}

//				console.log("target slide raw "+targetSlide);
				if( targetSlide <= 1 ) {
					targetSlide = slideItemCount + number;
				} else if( targetSlide > (slideItemCount) ) {
					targetSlide = (targetSlide % slideItemCount);

					if( targetSlide == 0 ) {
						targetSlide = slideItemCount;
					}
				}
				if( targetSlide <= 2 ) {
					//console.log("JUMP forward");
					targetSlide = slideItemCount + targetSlide;
				}

				numberToSlide = parseInt( currentSlide - targetSlide );
			}

			if( direction == 1 && numberToSlide > 0  ) {
				var jumpTo = parseInt(currentSlide - slideItemCount)-2;
//				console.log("JUMP to "+jumpTo);
				tbList.css('left',boxWidth*(jumpTo)*-1);
			} else if( direction == -1 && numberToSlide < 0 ) {
				var jumpTo = parseInt(currentSlide + slideItemCount)-2;
//				console.log("JUMP to "+jumpTo);
				tbList.css('left',parseInt( boxWidth*(jumpTo)*-1 ) );
			}

			var posMove = parseInt(-1 * boxWidth * (targetSlide-2) );

			tbList.animate({
				left: posMove
			});

//			console.log("trigger move to "+number+" from slide "+currentSlide+" to target slide " +targetSlide+" => go "+numberToSlide+" slides, direction "+direction);
			//console.log("pos move "+posMove);

			currentSlide = targetSlide;


			// set active class to new active thumbnail
			$('li',tbList).removeClass('active');
			$('li[item="'+(targetSlide)+'"]',tbList).addClass('active');
		}


		var target			= $(this);			// The div container
		var tbList			= $(target.children()[0]);	// the UL list of the thumbnails


		var carouselItemWidth = $('li:eq(0)',tbList).outerWidth();
		var wrapperWidth	= parseInt( $('li',tbList).length*3 ) * carouselItemWidth;

		tbList.css({
			'list-style'	: 'none',
			'width'			: wrapperWidth
		});

		// add the additional li elements for scolling
		var orgItemList	= tbList.html();
		tbList.append(orgItemList).prepend(orgItemList);
		var tbListWidth		  = parseInt(options.numberOfThumbnailsToDisplay * carouselItemWidth);

		target.css({
			'width'	: tbListWidth,
			'overflow': 'hidden'
		});

		$('li',tbList).each(function(i) {
			var el	= $(this);
			el.addClass('tqThumbnail');
			el.attr('item',i+1);

			$(this).click(function() {
				tqSlideShow.cycleAbs( i, 0, i);
				goto( i+1);
			});
		});

		this.goto(1);
		toggleTb('init');
		target.mouseenter(function(){
			toggleTb('show');
		});
		target.mouseleave(function(){
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