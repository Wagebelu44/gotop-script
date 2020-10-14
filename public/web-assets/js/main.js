//Navbar
var navbar = $('.navbar');
var navbarButton = $('.navbar-toggler');
var navbarBackdrop = $('.navbar-backdrop');

$(window).scroll(function () {
    if ($(this).scrollTop() > 0) {
        navbar.addClass('navbar-shadow');
    } else {
        navbar.removeClass('navbar-shadow');
    }
});

navbarButton.on('click', function () {
    navbarBackdrop.toggleClass('hidden');
    navbar.toggleClass('navbar-shadow');
});


// smooth scroll to anchor, with option of hash appearing in url. Thanks:
// https://paulund.co.uk/smooth-scroll-to-internal-links-with-jquery
$(document).ready(function(){
	$('a[href^="#"]').on('click',function (e) {
	    e.preventDefault();
	    var target = this.hash;
	    var $target = $(target);
	    $('html, body').stop().animate({
	        'scrollTop': $target.offset().top
	    }, 900, 'swing', function () {
	        // window.location.hash = target;
	    });
	});
});

// Back to top
var btn = $('#button');

$(window).scroll(function() {
  if ($(window).scrollTop() > 300) {
    btn.addClass('show');
  } else {
    btn.removeClass('show');
  }
});

btn.on('click', function(e) {
  e.preventDefault();
  $('html, body').animate({scrollTop:0}, '300');
});

