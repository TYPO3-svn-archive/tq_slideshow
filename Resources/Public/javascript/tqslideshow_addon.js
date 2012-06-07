
// boxrain
$.fn.cycle.transitions.boxrain = function($cont, $slides, opts) {

	opts.before.push(function(curr, next, opts ) {
		$.fn.cycle.commonReset(curr,next,opts);

		opts.animOut.top   = 0;
		opts.animOut.left   = 0;

		opts.animIn.top   = 0;
		opts.animIn.left   = 0;

		opts.animIn.width = next.cycleW;
		opts.animIn.height = next.cycleH;

		opts.animOut.width = next.cycleW;
		opts.animOut.height = next.cycleH;

		opts.cssBefore.height = 0;

		var nextImageEl	= $('img',next);
		var nextImage = nextImageEl.attr('src');

		var currImageEl	= $('img',curr);
		var currImage = currImageEl.attr('src');

		var sliderContainer	= $(next).parent().parent();
		$('.tqExplode-el', sliderContainer).remove();
		
		var factor = 0;
		var complete = 0;
		var total = 48;
		var animationSpeed	= 500;
		cols	= 12;
		rows	= 4;
		frameWidth	= 960;
		frameHeight	= 400;

		$('<div/>').css({
			width: w, height: h,
			left: 0,
			top: 0,
			width: frameWidth,
			height: 408,
			opacity: 1,
			zIndex : 5,
			position: 'absolute',
			backgroundImage: 'url(' + 	currImage + ')'
		}).addClass('tqExplode-el tqExplode-holder-'+opts.slideCount).appendTo(sliderContainer);

		timeFactor = animationSpeed / total;
		var w = Math.ceil(frameWidth / cols);
		var h = Math.ceil(frameHeight / rows);
		var coordinates = new Array();
		for (var row = 0; row < rows; row++) {
			for (var col = 0; col < cols; col++) {
				$('<div/>').css({
					width: w, height: h,
					left: Math.ceil((frameWidth / 2 - w / 2)),
					top: Math.ceil((frameHeight / 2 - h / 2)),
					opacity: 0,
					position: 'absolute',
					zIndex:6 ,
					backgroundImage: 'url(' + nextImage + ')',
					backgroundPosition: '' + (-(col * w)) + 'px ' + (-(row * h)) + 'px'
				}).addClass('tqExplode-el tqExplode-el-'+opts.slideCount).appendTo(sliderContainer);
				var position = [{ left: (col * w), top: (row * h)}];
				coordinates.push(position);
			}
		}
		$('.tqExplode-el-'+opts.slideCount, sliderContainer).each(function (index) {
			var el = $(this);
			var p = coordinates[index][0];

			el.animate({ left: p.left, top: p.top, opacity: 1, zIndex:6 }, animationSpeed * 2, function () {
				complete++;
				if (complete == total - 1){
					$('.tqExplode-el-'+opts.slideCount, sliderContainer).remove();
					$('.tqExplode-holder-'+opts.slideCount, sliderContainer).remove();

					opts.animIn	   = { opacity: 1 };
					opts.animOut   = { opacity: 0 };
				}
			});
			factor += timeFactor;
		});


	});
};