$(function() {
  var bannerImgs = [
			{
				'img': '../images/banner1.jpg',
				'name': 'banner1'
			},
			{
				'img': '../images/banner2.jpg',
				'name': 'banner2'
			},
			{
				'img': '../images/banner3.jpg',
				'name': 'banner3'
			},
			{
				'img': '../images/banner4.jpg',
				'name': 'banner4'
			},
			{
				'img': '../images/banner5.jpg',
				'name': 'banner5'
			}
		];
		
		$("#banner ul").html('');
		$.each(bannerImgs, function(key,data) {
			var html = '<li class="swiper-slide"><img src="' + data.img + '" alt="'+ data.name+ '" /></li>';
			$("#banner ul").append(html);
		});
		
	//Main Swiper
	var swiper = new Swiper('.swiper1', {
		pagination: '.pagination1',
		loop: true,
		grabCursor: true
	});
	//Navigation arrows
	$('.arrow-left').click(function(e) {
		e.preventDefault()
		swiper.swipePrev()
	});
	$('.arrow-right').click(function(e) {
		e.preventDefault()
		swiper.swipeNext()
	});
	//Clickable pagination
	$('.pagination1 .swiper-pagination-switch').click(function() {
		swiper.swipeTo($(this).index())
	})

	
})